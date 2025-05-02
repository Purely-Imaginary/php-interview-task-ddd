<?php

declare(strict_types=1);

namespace Lendable\Interview\Domain\Service;

use Lendable\Interview\Domain\Model\Fee\FeeStructure;
use Lendable\Interview\Domain\Model\Loan\Loan;
use Lendable\Interview\Domain\Model\Loan\Money;

final class InterpolationFeeStrategy implements FeeCalculationStrategyInterface
{
    // Values in between the breakpoints should be interpolated linearly
    // between the lower bound and upper bound that they fall between.
    public function calculateBaseFee(Loan $loan, FeeStructure $feeStructure): Money
    {
        ['lower' => $lower, 'upper' => $upper] = $feeStructure->findBoundaryBreakpoints($loan->getAmount());
        $loanAmount = $loan->getAmount();
        $interpolationRange = $upper->amount->getMinorAmount() - $lower->amount->getMinorAmount();
        $interpolationPoint = $loanAmount->getMinorAmount() - $lower->amount->getMinorAmount();
        if ($interpolationRange === 0) {
            return $lower->fee;
        }

        $interpolationFactor = $interpolationPoint / $interpolationRange;
        $feeRange = $upper->fee->getMinorAmount() - $lower->fee->getMinorAmount();

        return Money::fromMinor((int) round($lower->fee->getMinorAmount() + ($interpolationFactor * $feeRange)));
    }
}
