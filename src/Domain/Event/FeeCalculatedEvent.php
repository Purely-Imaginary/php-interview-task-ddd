<?php

declare(strict_types=1);

namespace Lendable\Interview\Domain\Event;

use Lendable\Interview\Domain\Model\Loan\Loan;
use Lendable\Interview\Domain\Model\Loan\Money;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event dispatched after a loan fee has been successfully calculated.
 */
final class FeeCalculatedEvent extends Event
{
    public function __construct(
        private readonly Loan  $loan,
        private readonly Money $calculatedFee
    )
    {
    }

    public function getLoan(): Loan
    {
        return $this->loan;
    }

    public function getCalculatedFee(): Money
    {
        return $this->calculatedFee;
    }
}