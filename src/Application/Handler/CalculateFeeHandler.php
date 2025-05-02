<?php

declare(strict_types=1);

namespace Lendable\Interview\Application\Handler;

use Lendable\Interview\Domain\Event\FeeCalculatedEvent;
use Lendable\Interview\Domain\Exception\FeeStructureNotFoundException;
use Lendable\Interview\Domain\Model\Fee\FeeStructure;
use Lendable\Interview\Domain\Model\Loan\Loan;
use Lendable\Interview\Domain\Model\Loan\Money;
use Lendable\Interview\Domain\Model\Loan\Term;
use Lendable\Interview\Domain\Repository\FeeStructureRepository;
use Lendable\Interview\Domain\Service\FeeCalculator;
use Psr\EventDispatcher\EventDispatcherInterface;

final readonly class CalculateFeeHandler
{
    public function __construct(
        private FeeStructureRepository $feeStructureRepository,
        private FeeCalculator $feeCalculator,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function handle(Money $money, Term $term): Money
    {
        $loan = new Loan($money, $term); // This implicitly validates the amount

        $feeStructure = $this->feeStructureRepository->findForTerm($term);
        if (!$feeStructure instanceof FeeStructure) {
            throw new FeeStructureNotFoundException($term->inMonths());
        }

        $result = $this->feeCalculator->calculate($loan, $feeStructure);

        $event = new FeeCalculatedEvent($loan, $result);
        $this->eventDispatcher->dispatch($event);

        return $result;
    }
}
