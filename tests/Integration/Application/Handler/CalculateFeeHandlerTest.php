<?php

declare(strict_types=1);

namespace Lendable\Interview\Tests\Integration\Application\Handler;

use Lendable\Interview\Application\Handler\CalculateFeeHandler;
use Lendable\Interview\Domain\Exception\InvalidLoanAmountException;
use Lendable\Interview\Domain\Model\Loan\Money;
use Lendable\Interview\Domain\Model\Loan\Term;
use Lendable\Interview\Domain\Service\FeeCalculator;
use Lendable\Interview\Domain\Service\InterpolationService;
use Lendable\Interview\Domain\Service\RoundingService;
use Lendable\Interview\Infrastructure\Persistence\InMemoryFeeStructureRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CalculateFeeHandlerTest extends TestCase
{
    private CalculateFeeHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $repository = new InMemoryFeeStructureRepository();
        $interpolationService = new InterpolationService();
        $roundingService = new RoundingService();
        $calculator = new FeeCalculator(
            $interpolationService,
            $roundingService
        );
        $this->handler = new CalculateFeeHandler($repository, $calculator);
    }

    /**
     * @return array<string, array{string, int, int}>
     */
    public static function successfulCalculationProvider(): array
    {
        // [amountString, termMonths, expectedFeeMinor]
        return [
            'Example 1: 11500 for 24 months'                         => ['11500.00', 24, 460_00],
            'Example 2: 19250 for 12 months'                         => ['19250.00', 12, 385_00],
            'Boundary Low: 1000 for 12 months'                       => [ '1000.00', 12,  50_00],
            'Boundary High: 20000 for 24 months'                     => ['20000.00', 24, 800_00],
            'Interpolation: 1500 for 12 months'                      => [ '1500.00', 12,  70_00],
            'Interpolation: 2750 for 12 months (no rounding needed)' => [ '2750.00', 12,  90_00],
            'Interpolation needing rounding: 3500 for 12 months'     => [ '3500.00', 12, 105_00],
        ];
    }

    #[DataProvider('successfulCalculationProvider')]
    #[Test]
    public function testHandlesSuccessfulCalculation(string $amountString, int $termMonths, int $expectedFeeMinor): void
    {
        $amount = Money::fromDecimalString($amountString);
        $term = Term::fromMonths($termMonths);
        $expectedFee = Money::fromMinor($expectedFeeMinor);

        $actualFee = $this->handler->handle($amount, $term);

        $this->assertTrue($expectedFee->equals($actualFee), sprintf(
            'Failed asserting that fee %s matches expected %s for amount %s and term %d',
            $actualFee->getDecimalString(),
            $expectedFee->getDecimalString(),
            $amountString,
            $termMonths
        ));
    }

    /**
     * @return array<string, array{string, int}>
     */
    public static function invalidAmountProvider(): array
    {
        // [amountString, termMonths]
        return [
            'Amount too low'  => [  '999.99', 12],
            'Amount too high' => ['20000.01', 24],
        ];
    }

    #[DataProvider('invalidAmountProvider')]
    #[Test]
    public function testThrowsExceptionForInvalidLoanAmount(string $amountString, int $termMonths): void
    {
        $amount = Money::fromDecimalString($amountString);
        $term = Term::fromMonths($termMonths);

        $this->expectException(InvalidLoanAmountException::class);

        $this->handler->handle($amount, $term);
    }
}
