<?php

declare(strict_types=1);

namespace Lendable\Interview\Unit\Domain\Service;

use InvalidArgumentException;
use Lendable\Interview\Domain\Model\Fee\Breakpoint;
use Lendable\Interview\Domain\Model\Fee\FeeStructure;
use Lendable\Interview\Domain\Model\Loan\Loan;
use Lendable\Interview\Domain\Model\Loan\Money;
use Lendable\Interview\Domain\Model\Loan\Term;
use Lendable\Interview\Domain\Service\FeeCalculationStrategyInterface;
use Lendable\Interview\Domain\Service\FeeCalculator;
use Lendable\Interview\Domain\Service\RoundingServiceInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

final class FeeCalculatorTest extends TestCase
{
    private FeeCalculationStrategyInterface $strategyMock;
    private RoundingServiceInterface $roundingMock;
    private FeeCalculator $feeCalculator;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->strategyMock = $this->createMock(FeeCalculationStrategyInterface::class);
        $this->roundingMock = $this->createMock(RoundingServiceInterface::class);
        $this->feeCalculator = new FeeCalculator($this->strategyMock, $this->roundingMock);
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function testCalculationDelegatesToStrategyAndRounding(): void
    {
        $loanAmount = Money::fromMinor(1_500_00);
        $term = Term::fromMonths(12);
        $loan = new Loan($loanAmount, $term);

        $feeStructure = new FeeStructure($term, [
            new Breakpoint(Money::fromMinor(1_000_00), Money::fromMinor(50_00)),
            new Breakpoint(Money::fromMinor(2_000_00), Money::fromMinor(90_00)),
        ]);

        $expectedBaseFee = Money::fromMinor(70_00);
        $expectedFinalFee = Money::fromMinor(75_00);

        $this->strategyMock
            ->expects($this->once())
            ->method('calculateBaseFee')
            ->with($this->identicalTo($loan), $this->identicalTo($feeStructure))
            ->willReturn($expectedBaseFee);

        $this->roundingMock
            ->expects($this->once())
            ->method('roundFee')
            ->with($this->identicalTo($loanAmount), $this->identicalTo($expectedBaseFee))
            ->willReturn($expectedFinalFee);

        $actualFee = $this->feeCalculator->calculate($loan, $feeStructure);

        $this->assertTrue($expectedFinalFee->equals($actualFee));
    }


    /**
     * @throws Exception
     */
    #[Test]
    public function throwsExceptionOnTermMismatch(): void
    {
        $loanAmount = Money::fromMinor(1_500_00);
        $loan = new Loan($loanAmount, Term::fromMonths(12));

        $mismatchedFeeStructure = new FeeStructure(Term::fromMonths(24), [
            new Breakpoint(Money::fromMinor(1_000_00), Money::fromMinor(70_00)),
        ]);

        $this->strategyMock->expects($this->never())->method('calculateBaseFee');
        $this->roundingMock->expects($this->never())->method('roundFee');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Loan and fee structures must have the same term.');

        $this->feeCalculator->calculate($loan, $mismatchedFeeStructure);
    }
}