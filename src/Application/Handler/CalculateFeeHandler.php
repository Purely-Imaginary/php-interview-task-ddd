<?php

namespace Lendable\Interview\Application\Handler;

use Lendable\Interview\Domain\Exception\FeeStructureNotFoundException;
use Lendable\Interview\Domain\Model\Loan\Loan;
use Lendable\Interview\Domain\Model\Loan\Money;
use Lendable\Interview\Domain\Model\Loan\Term;
use Lendable\Interview\Domain\Repository\FeeStructureRepository;
use Lendable\Interview\Domain\Service\FeeCalculator;

final class CalculateFeeHandler
{
    public function __construct(
        private readonly FeeStructureRepository $feeStructureRepository,
        private readonly FeeCalculator $feeCalculator,
    ) {
    }

    public function handle(Money $money, Term $term): Money
    {
        $loan = new Loan($money, $term); // This implicitly validates the amount

        $feeStructure = $this->feeStructureRepository->findForTerm($term);
        if ($feeStructure === null) {
            throw new FeeStructureNotFoundException($term->inMonths());
        }

        return $this->feeCalculator->calculate($loan, $feeStructure);
    }
}