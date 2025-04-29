<?php

namespace Lendable\Interview\Domain\Model\Fee;

use Lendable\Interview\Domain\Model\Loan\Money;

final readonly class Breakpoint
{
    public function __construct(
        public readonly Money $amount,
        public readonly Money $fee
    ) {}
}