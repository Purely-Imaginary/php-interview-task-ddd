<?php

namespace Lendable\Interview\Domain\Service;

use Lendable\Interview\Domain\Model\Fee\Breakpoint;
use Lendable\Interview\Domain\Model\Loan\Money;

final class RoundingService
{
    private const int DIVISOR = 5_00;

    // The fee should be rounded up such that the sum of the fee and the loan amount is exactly divisible by £5.
    public function roundFee(Money $loanAmount, Money $calculatedFee): Money
    {
        $totalMinor = $loanAmount->getMinorAmount() + $calculatedFee->getMinorAmount();
        $remainder = $totalMinor % self::DIVISOR;

        if ($remainder === 0) {
            return $calculatedFee;
        }

        $amountToAdd = Money::fromMinor(self::DIVISOR - ($remainder));

        return $calculatedFee->add($amountToAdd);
    }
}