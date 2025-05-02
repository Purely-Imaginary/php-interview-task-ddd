<?php

declare(strict_types=1);

namespace Lendable\Interview\Domain\Service;

use Lendable\Interview\Domain\Model\Fee\FeeStructure;
use Lendable\Interview\Domain\Model\Loan\Loan;
use Lendable\Interview\Domain\Model\Loan\Money;

interface FeeCalculationStrategyInterface
{
    public function calculateBaseFee(Loan $loan, FeeStructure $feeStructure): Money;
}
