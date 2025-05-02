<?php

declare(strict_types=1);

namespace Lendable\Interview\Domain\Service;

use Lendable\Interview\Domain\Model\Fee\FeeStructure;
use Lendable\Interview\Domain\Model\Loan\Loan;
use Lendable\Interview\Domain\Model\Loan\Money;

final readonly class FeeCalculator
{
    public function __construct(
        private FeeCalculationStrategyInterface $feeCalculationStrategy,
        private RoundingServiceInterface        $roundingService
    ) {
    }

    public function calculate(Loan $loan, FeeStructure $feeStructure): Money
    {
        // Sanity check: make sure that we have the same terms in both structures
        if ($loan->getTerm() !== $feeStructure->getTerm()) {
            throw new \InvalidArgumentException('Loan and fee structures must have the same term.');
        }

        $baseFee = $this->feeCalculationStrategy->calculateBaseFee($loan, $feeStructure);

        return $this->roundingService->roundFee($loan->getAmount(), $baseFee);
    }
}
