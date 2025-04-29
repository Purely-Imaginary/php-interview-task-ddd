<?php

declare(strict_types=1);

namespace Lendable\Interview\Unit\Domain\Service;

use InvalidArgumentException;
use Lendable\Interview\Domain\Exception\TermNotSupportedException;
use Lendable\Interview\Domain\Model\Fee\Breakpoint;
use Lendable\Interview\Domain\Model\Loan\Money;
use Lendable\Interview\Domain\Model\Loan\Term;
use Lendable\Interview\Domain\Service\InterpolationService;
use Lendable\Interview\Domain\Service\RoundingService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class InterpolationServiceTest extends TestCase
{
    public static function interpolationDataProvider(): array
    {
        // Helper to create Breakpoint quickly
        $bp = fn($a, $f) => new Breakpoint(Money::fromMinor($a), Money::fromMinor($f));

        // [loanAmountMinor, lowerBreakpoint, upperBreakpoint, expectedFeeMinor]
        return [
            'exact match lower' => [1_000_00, $bp(1_000_00,  50_00), $bp(2_000_00,  90_00),  50_00],
            'exact match upper' => [2_000_00, $bp(1_000_00,  50_00), $bp(2_000_00,  90_00),  90_00],
            'bounds are same'   => [1_500_00, $bp(1_000_00,  50_00), $bp(1_000_00,  50_00),  50_00],
            'halfway (1k-2k)'   => [1_500_00, $bp(1_000_00,  50_00), $bp(2_000_00,  90_00),  70_00],
            'partway (1k-2k)'   => [1_250_00, $bp(1_000_00,  50_00), $bp(2_000_00,  90_00),  60_00],
            'partway (4k-5k)'   => [4_800_00, $bp(4_000_00, 115_00), $bp(5_000_00, 100_00), 103_00],
            'needs rounding'    => [1_333_00, $bp(1_000_00,  50_00), $bp(2_000_00, 100_00),  66_65],
        ];
    }

    #[DataProvider('interpolationDataProvider')]
    #[Test]
    public function isFeeInterpolatedCorrectly(int $loanAmountMinor, Breakpoint $lowerBreakpoint, Breakpoint $higherBreakpoint, int $expectedFeeMinor): void
    {
        $interpolationService = new InterpolationService();
        $loanAmount = Money::fromMinor($loanAmountMinor);
        $expectedFee = Money::fromMinor($expectedFeeMinor);

        $actualFee = $interpolationService->interpolate($loanAmount, $lowerBreakpoint, $higherBreakpoint);

        $this->assertTrue($expectedFee->equals($actualFee), sprintf(
            'Failed asserting that interpolated fee %s matches expected %s for amount %s',
            $actualFee->getDecimalString(),
            $expectedFee->getDecimalString(),
            $loanAmount->getDecimalString(),
        ));
    }

}