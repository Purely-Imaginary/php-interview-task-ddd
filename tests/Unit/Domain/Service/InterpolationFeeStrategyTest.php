<?php

declare(strict_types=1);

namespace Lendable\Interview\Unit\Domain\Service;

use Lendable\Interview\Domain\Model\Fee\Breakpoint;
use Lendable\Interview\Domain\Model\Fee\FeeStructure;
use Lendable\Interview\Domain\Model\Loan\Loan;
use Lendable\Interview\Domain\Model\Loan\Money;
use Lendable\Interview\Domain\Model\Loan\Term;
use Lendable\Interview\Domain\Service\InterpolationFeeStrategy;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

// Rename the test class to match the class being tested
final class InterpolationFeeStrategyTest extends TestCase
{
    /**
     * Returns [loanAmountMinor, termMonths, breakpointsArray, expectedFeeMinor]
     *
     * @return array<string, array{int, int, array<int, Breakpoint>, int}>
     */
    public static function interpolationDataProvider(): array
    {
        // Helper to create Breakpoint quickly
        $bp = fn (int $a, int $f): Breakpoint => new Breakpoint(Money::fromMinor($a), Money::fromMinor($f));

        $breakpoints1k2k = [$bp(1_000_00, 50_00), $bp(2_000_00, 90_00)];
        $breakpoints1k1k = [$bp(1_000_00, 50_00)]; // Test edge case with single/identical bounds
        $breakpoints4k5k = [$bp(4_000_00, 115_00), $bp(5_000_00, 100_00)];
        $breakpoints1k2kV2 = [$bp(1_000_00, 50_00), $bp(2_000_00, 100_00)]; // For rounding test case

        // Structure: [loanAmountMinor, termMonths, breakpointsArray, expectedFeeMinor]
        return [
            'exact match lower 1k' => [1_000_00, 12, $breakpoints1k2k, 50_00],
            'exact match upper 2k' => [2_000_00, 12, $breakpoints1k2k, 90_00],

            'bounds are same 1k'   => [1_000_00, 12, $breakpoints1k1k, 50_00],
            'halfway (1k-2k)'   => [1_500_00, 12, $breakpoints1k2k, 70_00],
            'partway (1k-2k)'   => [1_250_00, 12, $breakpoints1k2k, 60_00],
            'partway (4k-5k)'   => [4_800_00, 12, $breakpoints4k5k, 103_00],

            'needs rounding'    => [1_333_00, 12, $breakpoints1k2kV2, 66_65],
        ];
    }

    /**
     * @param array<Breakpoint> $breakpointsArray
     */
    #[DataProvider('interpolationDataProvider')]
    #[Test]
    public function testCalculateBaseFeeInterpolatesCorrectly(
        int $loanAmountMinor,
        int $termMonths,
        array $breakpointsArray,
        int $expectedFeeMinor
    ): void {
        $strategy = new InterpolationFeeStrategy();

        $loanAmount = Money::fromMinor($loanAmountMinor);
        $term = Term::fromMonths($termMonths);
        $loan = new Loan($loanAmount, $term);

        $feeStructure = new FeeStructure($term, $breakpointsArray);

        $expectedFee = Money::fromMinor($expectedFeeMinor);

        $actualFee = $strategy->calculateBaseFee($loan, $feeStructure);

        $this->assertTrue($expectedFee->equals($actualFee), sprintf(
            'Failed asserting that interpolated base fee %s matches expected %s for amount %s',
            $actualFee->getDecimalString(),
            $expectedFee->getDecimalString(),
            $loanAmount->getDecimalString()
        ));
    }
}
