<?php

declare(strict_types=1);

namespace Lendable\Interview\Infrastructure\Formatting;

use Lendable\Interview\Application\Service\OutputFormatter;
use Lendable\Interview\Domain\Model\Loan\Money;

final class StandardOutputFormatter implements OutputFormatter
{
    public function format(Money $money): string
    {
        $floatAmount = $money->getMinorAmount() / 100.0;
        return number_format($floatAmount, 2, '.', ',');
    }
}
