<?php

declare(strict_types=1);

namespace Lendable\Interview\Domain\Service;

use Lendable\Interview\Domain\Model\Loan\Money;

interface RoundingServiceInterface
{
    public function roundFee(Money $loanAmount, Money $calculatedFee): Money;
}
