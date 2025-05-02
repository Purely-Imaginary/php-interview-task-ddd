<?php

declare(strict_types=1);

namespace Lendable\Interview\Application\Handler;

use Lendable\Interview\Domain\Exception\FeeStructureNotFoundException;
use Lendable\Interview\Domain\Model\Loan\Loan;
use Lendable\Interview\Domain\Model\Loan\Money;
use Lendable\Interview\Domain\Model\Loan\Term;
use Lendable\Interview\Domain\Repository\FeeStructureRepository;
use Lendable\Interview\Domain\Service\FeeCalculator;

final readonly class CalculateFeeHandler
{
    public function __construct(
        private FeeStructureRepository $feeStructureRepository,
        private FeeCalculator $feeCalculator,
    ) {
    }

    public function handle(Money $money, Term $term): Money
    {
        $loan = new Loan($money, $term); // This implicitly validates the amount

        $feeStructure = $this->feeStructureRepository->findForTerm($term);
        if (!$feeStructure instanceof \Lendable\Interview\Domain\Model\Fee\FeeStructure) {
            throw new FeeStructureNotFoundException($term->inMonths());
        }

        return $this->feeCalculator->calculate($loan, $feeStructure);
    }
}
