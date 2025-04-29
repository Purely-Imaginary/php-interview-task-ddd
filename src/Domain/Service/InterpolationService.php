<?php

namespace Lendable\Interview\Domain\Service;

use Lendable\Interview\Domain\Model\Fee\Breakpoint;
use Lendable\Interview\Domain\Model\Loan\Money;

final class InterpolationService implements \Lendable\Interview\Domain\Service\InterpolationServiceInterface
{
    // Values in between the breakpoints should be interpolated linearly
    // between the lower bound and upper bound that they fall between.
    public function interpolate(Money $loanAmount, Breakpoint $lower, Breakpoint $upper): Money
    {
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