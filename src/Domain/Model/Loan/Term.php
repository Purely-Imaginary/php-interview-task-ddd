<?php

declare(strict_types=1);

namespace Lendable\Interview\Domain\Model\Loan;

use Lendable\Interview\Domain\Exception\TermNotSupportedException;

enum Term: int
{
    case MONTHS_12 = 12;
    case MONTHS_24 = 24;

    /**
     * @throws TermNotSupportedException
     */
    public static function fromMonths(int $months): self
    {
        $returnValue = self::tryFrom($months);
        if ($returnValue === null) {
            throw new TermNotSupportedException($months);
        }

        return $returnValue;
    }

    public function inMonths(): int
    {
        return $this->value;
    }
}
