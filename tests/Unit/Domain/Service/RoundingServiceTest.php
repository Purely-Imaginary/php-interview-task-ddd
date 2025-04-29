<?php

declare(strict_types=1);

namespace Lendable\Interview\Unit\Domain\Service;

use InvalidArgumentException;
use Lendable\Interview\Domain\Exception\TermNotSupportedException;
use Lendable\Interview\Domain\Model\Loan\Money;
use Lendable\Interview\Domain\Model\Loan\Term;
use Lendable\Interview\Domain\Service\RoundingService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RoundingServiceTest extends TestCase
{
    public static function roundingDataProvider(): array
    {
        // [loanAmountMinor, calculatedFeeMinor, expectedFeeMinor]
        return [
            'no rounding needed (sum=1570)'   => [  1500_00,  70_00,  70_00],
            'no rounding needed (sum=2840)'   => [  2750_00,  90_00,  90_00],
            'no rounding needed (sum=12000)'  => [11_500_00, 500_00, 500_00],
            'rounding needed (sum=123.45)'    => [   100_00,  23_45,  25_00],
            'rounding needed (sum=1999.99)'   => [ 1_994_99,   5_00,   5_01],
            'rounding needed (sum=1.01)'      => [     1_00,   0_01,   4_00],
            'rounding needed near boundary'   => [   100_00,   4_99,   5_00],
        ];
    }

    #[DataProvider('roundingDataProvider')]
    #[Test]
    public function feeIsRoundedCorrectly(int $loanAmountMinor, int $calculatedFeeMinor, int $expectedFeeMinor): void
    {
        $roundingService = new RoundingService();
        $loanAmount = Money::fromMinor($loanAmountMinor);
        $calculatedFee = Money::fromMinor($calculatedFeeMinor);
        $expectedFee = Money::fromMinor($expectedFeeMinor);

        $actualFee = $roundingService->roundFee($loanAmount, $calculatedFee);

        $this->assertTrue($expectedFee->equals($actualFee), sprintf(
            'Failed asserting that rounded fee %s matches expected %s for amount %s and base fee %s',
            $actualFee->getDecimalString(),
            $expectedFee->getDecimalString(),
            $loanAmount->getDecimalString(),
            $calculatedFee->getDecimalString()
        ));
    }

}