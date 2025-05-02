<?php

declare(strict_types=1);

namespace Lendable\Interview\Unit\Domain\Service;

use InvalidArgumentException;
use Lendable\Interview\Domain\Model\Fee\Breakpoint;
use Lendable\Interview\Domain\Model\Fee\FeeStructure;
use Lendable\Interview\Domain\Model\Loan\Loan;
use Lendable\Interview\Domain\Model\Loan\Money;
use Lendable\Interview\Domain\Model\Loan\Term;
use Lendable\Interview\Domain\Service\FeeCalculator;
use Lendable\Interview\Domain\Service\InterpolationServiceInterface;
use Lendable\Interview\Domain\Service\RoundingServiceInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

final class FeeCalculatorTest extends TestCase
{
    /**
     * @throws Exception
     */
    #[Test]
    public function exactMatch(): void
    {
        $interpolationServiceMock = $this->createMock(InterpolationServiceInterface::class);
        $roundingServiceMock = $this->createMock(RoundingServiceInterface::class);
        $loanAmount = Money::fromMinor(1_000_00);
        $loan = new Loan($loanAmount, Term::fromMonths(12));
        $feeStructure = new FeeStructure($loan->getTerm(), [
            new Breakpoint(Money::fromMinor(1_000_00), Money::fromMinor(50_00)),
            new Breakpoint(Money::fromMinor(2_000_00), Money::fromMinor(90_00)),
        ]);

        $interpolationServiceMock->expects($this->never())
            ->method('interpolate');

        $expectedValue = Money::fromMinor(50_00);

        $roundingServiceMock->expects($this->once())
            ->method('roundFee')
            ->with($loanAmount, Money::fromMinor(50_00))
            ->willReturn($expectedValue);

        $feeCalculator = new FeeCalculator($interpolationServiceMock, $roundingServiceMock);
        $actualValue = $feeCalculator->calculate($loan, $feeStructure);
        $this->assertTrue($expectedValue->equals($actualValue));
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function interpolationNeeded(): void
    {
        $interpolationServiceMock = $this->createMock(InterpolationServiceInterface::class);
        $roundingServiceMock = $this->createMock(RoundingServiceInterface::class);
        $loanAmount = Money::fromMinor(1_500_00);
        $loan = new Loan($loanAmount, Term::fromMonths(12));
        $lowerBreakpoint = new Breakpoint(Money::fromMinor(1_000_00), Money::fromMinor(50_00));
        $higherBreakpoint = new Breakpoint(Money::fromMinor(2_000_00), Money::fromMinor(90_00));
        $feeStructure = new FeeStructure($loan->getTerm(), [
            $lowerBreakpoint,
            $higherBreakpoint,
        ]);
        $expectedValue = Money::fromMinor(70_00);

        $interpolationServiceMock->expects($this->once())
            ->method('interpolate')
            ->with($loanAmount, $lowerBreakpoint, $higherBreakpoint)
            ->willReturn($expectedValue);

        $roundingServiceMock->expects($this->once())
            ->method('roundFee')
            ->with($loanAmount, Money::fromMinor(70_00))
            ->willReturn($expectedValue);

        $feeCalculator = new FeeCalculator($interpolationServiceMock, $roundingServiceMock);
        $actualValue = $feeCalculator->calculate($loan, $feeStructure);
        $this->assertTrue($expectedValue->equals($actualValue));
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function throwsExceptionOnTermMismatch(): void
    {
        $interpolationServiceMock = $this->createMock(InterpolationServiceInterface::class);
        $roundingServiceMock = $this->createMock(RoundingServiceInterface::class);
        $loanAmount = Money::fromMinor(1_500_00);
        $loan = new Loan($loanAmount, Term::fromMonths(12));
        $lowerBreakpoint = new Breakpoint(Money::fromMinor(1_000_00), Money::fromMinor(50_00));
        $higherBreakpoint = new Breakpoint(Money::fromMinor(2_000_00), Money::fromMinor(90_00));
        $feeStructure = new FeeStructure(Term::fromMonths(24), [
            $lowerBreakpoint,
            $higherBreakpoint,
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Loan and fee structures must have the same term.');

        $feeCalculator = new FeeCalculator($interpolationServiceMock, $roundingServiceMock);
        $feeCalculator->calculate($loan, $feeStructure);
    }

}
