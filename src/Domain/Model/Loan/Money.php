<?php

declare(strict_types=1);

namespace Lendable\Interview\Domain\Model\Loan;

final readonly class Money
{
    private function __construct(private readonly int $minorAmount)
    {
        if ($minorAmount < 0) {
            throw new \InvalidArgumentException('Money cannot be negative.');
        }
    }

    public static function fromMinor(int $minorAmount): self
    {
        return new self($minorAmount);
    }

    public static function fromDecimalString(string $decimalString): self
    {
        $cleanedString = str_replace(',', '', $decimalString);
        if (is_numeric($cleanedString) === false) {
            throw new \InvalidArgumentException('Invalid decimal string.');
        }

        $cleanedFloat = (float)$cleanedString;

        return self::fromMinor((int) round($cleanedFloat * 100));
    }

    public function getMinorAmount(): int
    {
        return $this->minorAmount;
    }

    public function getDecimalString(): string
    {
        return number_format($this->minorAmount / 100, 2);
    }

    public function equals(self $other): bool
    {
        return $this->minorAmount === $other->minorAmount;
    }

    public function isLessThan(Money $amount): bool
    {
        return $this->minorAmount < $amount->getMinorAmount();
    }

    public function isGreaterThan(Money $amount): bool
    {
        return $this->minorAmount > $amount->getMinorAmount();
    }

    public function add(Money $amount): Money
    {
        return self::fromMinor($this->minorAmount + $amount->getMinorAmount());
    }
}
