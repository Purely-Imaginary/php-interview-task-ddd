<?php

namespace Lendable\Interview\Domain\Model\Loan;

use Lendable\Interview\Domain\Exception\InvalidLoanAmountException;

final readonly class Loan
{
    private const int MINIMUM_AMOUNT = 1000_00;
    private const int MAXIMUM_AMOUNT = 20000_00;

    public function __construct(
        private readonly Money $amount,
        private readonly Term $term
    ) {
        if ($amount->getMinorAmount() < self::MINIMUM_AMOUNT || $amount->getMinorAmount() > self::MAXIMUM_AMOUNT) {
            throw new InvalidLoanAmountException($amount, Money::fromMinor(self::MINIMUM_AMOUNT), Money::fromMinor(self::MAXIMUM_AMOUNT));
        }
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function getTerm(): Term
    {
        return $this->term;
    }
}