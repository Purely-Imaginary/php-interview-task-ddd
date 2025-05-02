<?php

declare(strict_types=1);

namespace Lendable\Interview\Infrastructure\Event\Listener;

use Lendable\Interview\Domain\Event\FeeCalculatedEvent;
use Psr\Log\LoggerInterface;

/**
 * Listens for the FeeCalculatedEvent and logs the details.
 */
final readonly class LogFeeCalculationListener
{
    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    /**
     * Handle the FeeCalculatedEvent.
     * This method will be called by the event dispatcher.
     */
    public function __invoke(FeeCalculatedEvent $event): void
    {
        $loan = $event->getLoan();
        $fee = $event->getCalculatedFee();

        $this->logger->info('Fee calculated successfully.', [
            'loan_amount' => $loan->getAmount()->getDecimalString(),
            'loan_term' => $loan->getTerm()->inMonths(),
            'calculated_fee' => $fee->getDecimalString(),
            'loan_amount_minor' => $loan->getAmount()->getMinorAmount(),
            'calculated_fee_minor' => $fee->getMinorAmount(),
        ]);
    }
}