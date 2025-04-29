<?php

namespace Lendable\Interview\Domain\Service;

use Lendable\Interview\Domain\Model\Fee\Breakpoint;
use Lendable\Interview\Domain\Model\Fee\FeeStructure;
use Lendable\Interview\Domain\Model\Loan\Loan;
use Lendable\Interview\Domain\Model\Loan\Money;

final class FeeCalculator
{
    public function __construct(
        private readonly InterpolationService $interpolationService,
        private readonly RoundingService $roundingService
    ) {
    }

    public function calculate(Loan $loan, FeeStructure $feeStructure): Money
    {
        // Sanity check: make sure that we have the same terms in both structures
        if ($loan->getTerm() !== $feeStructure->getTerm()) {
            throw new \InvalidArgumentException('Loan and fee structures must have the same term.');
        }

        ['lower' => $lower, 'upper' => $upper] = $feeStructure->findBoundaryBreakpoints($loan->getAmount());

        $baseFee = $lower->amount->equals($upper->amount) ?
            $lower->fee :
            $this->interpolationService->interpolate($loan->getAmount(), $lower, $upper);

        return $this->roundingService->roundFee($loan->getAmount(), $baseFee);
    }
}