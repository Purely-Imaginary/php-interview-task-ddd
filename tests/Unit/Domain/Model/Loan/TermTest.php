<?php

declare(strict_types=1);

namespace Lendable\Interview\Unit\Domain\Model\Loan;

use InvalidArgumentException;
use Lendable\Interview\Domain\Exception\TermNotSupportedException;
use Lendable\Interview\Domain\Model\Loan\Money;
use Lendable\Interview\Domain\Model\Loan\Term;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TermTest extends TestCase
{
    #[DataProvider('invalidMonthsProvider')]
    #[Test]
    public function cannotCreateTermWithInvalidMonths(int $invalidValue): void
    {
        $this->expectException(TermNotSupportedException::class);
        $this->expectExceptionMessage(sprintf('Term %d months is not supported. Only 12 and 24 month terms are available.', $invalidValue));

        Term::fromMonths($invalidValue);
    }

    public static function invalidMonthsProvider(): array
    {
        return [
            'too high'     => [15],
            'negative'     => [-1],
            'zero'         => [0],
            'way too high' => [500],
        ];
    }
    #[Test]
    public function canCreateTermWith12Months(): void
    {
        $term = Term::fromMonths(12);
        $this->assertSame(12, $term->inMonths());
        $this->assertSame(Term::MONTHS_12, $term);
    }

    #[Test]
    public function canCreateTermWith24Months(): void
    {
        $term = Term::fromMonths(24);
        $this->assertSame(24, $term->inMonths());
        $this->assertSame(Term::MONTHS_24, $term);
    }
}