<?php

namespace Lendable\Interview\Application\Service;

use Lendable\Interview\Domain\Model\Loan\Money;

interface OutputFormatter
{
    public function format(Money $money): string;
}