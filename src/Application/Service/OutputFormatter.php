<?php

declare(strict_types=1);

namespace Lendable\Interview\Application\Service;

use Lendable\Interview\Domain\Model\Loan\Money;

interface OutputFormatter
{
    public function format(Money $money): string;
}
