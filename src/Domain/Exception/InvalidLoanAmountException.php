<?php

declare(strict_types=1);

namespace Lendable\Interview\Domain\Exception;

use Lendable\Interview\Domain\Model\Loan\Money;

/**
 * Exception thrown when an unsupported loan amount is provided.
 */
class InvalidLoanAmountException extends \DomainException
{
    public function __construct(Money $invalidAmount, Money $minAmount, Money $maxAmount)
    {
        parent::__construct(
            sprintf(
                'Loan amount %s is not supported. Amount must be between %s and %s.',
                $invalidAmount->getDecimalString(),
                $minAmount->getDecimalString(),
                $maxAmount->getDecimalString()
            )
        );
    }

}
