<?php

declare(strict_types=1);

namespace Lendable\Interview\Domain\Service;

use Lendable\Interview\Domain\Model\Fee\Breakpoint;
use Lendable\Interview\Domain\Model\Loan\Money;

interface InterpolationServiceInterface
{
    public function interpolate(Money $loanAmount, Breakpoint $lower, Breakpoint $upper): Money;
}
