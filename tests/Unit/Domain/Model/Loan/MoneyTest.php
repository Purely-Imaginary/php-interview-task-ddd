<?php

declare(strict_types=1);

namespace Lendable\Interview\Unit\Domain\Model\Loan;

use InvalidArgumentException;
use Lendable\Interview\Domain\Model\Loan\Money;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    #[Test]
    public function canCreateFromMinorAmount(): void
    {
        $amount = 123_45;
        $money = Money::fromMinor($amount);

        $this->assertSame($amount, $money->getMinorAmount());
    }

    #[Test]
    public function canCreateFromValidDecimalString(): void
    {
        $money = Money::fromDecimalString('1234.56');
        $this->assertSame(1_234_56, $money->getMinorAmount());

        $moneyWithComma = Money::fromDecimalString('1,234.56');
        $this->assertSame(1_234_56, $moneyWithComma->getMinorAmount());

        $moneyIntegerString = Money::fromDecimalString('500');
        $this->assertSame(500_00, $moneyIntegerString->getMinorAmount());
    }

    #[Test]
    public function cannotCreateFromInvalidDecimalString(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Money::fromDecimalString('1234.56.78');
    }

    #[Test]
    public function canGetDecimalString(): void
    {
        $money = Money::fromMinor(123_45);
        $this->assertSame('123.45', $money->getDecimalString());
    }

    #[Test]
    public function canAdd(): void
    {
        $money = Money::fromMinor(123_45);
        $addedMoney = $money->add(Money::fromMinor(56_78));
        $this->assertSame(180_23, $addedMoney->getMinorAmount());
    }

    #[Test]
    public function throwsExceptionForNegativeMinorAmount(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Money cannot be negative.');

        Money::fromMinor(-100);
    }

    #[Test]
    public function throwsExceptionForInvalidDecimalString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid decimal string.');

        Money::fromDecimalString('abc');
    }

    #[Test]
    public function equalityCheckWorks(): void
    {
        $moneyA = Money::fromMinor(10_00);
        $moneyB = Money::fromMinor(10_00);
        $moneyC = Money::fromMinor(20_00);

        $this->assertTrue($moneyA->equals($moneyB));
        $this->assertFalse($moneyA->equals($moneyC));
    }

    #[Test]
    public function comparisonMethodsWork(): void
    {
        $money10 = Money::fromMinor(10_00);
        $money20 = Money::fromMinor(20_00);

        $this->assertTrue($money10->isLessThan($money20));
        $this->assertFalse($money20->isLessThan($money10));
        $this->assertFalse($money10->isLessThan($money10));

        $this->assertTrue($money20->isGreaterThan($money10));
        $this->assertFalse($money10->isGreaterThan($money20));
        $this->assertFalse($money20->isGreaterThan($money20));
    }

    #[Test]
    public function additionReturnsNewCorrectInstance(): void
    {
        $moneyA = Money::fromMinor(10_00);
        $moneyB = Money::fromMinor(5_50);
        $expected = Money::fromMinor(15_50);

        $result = $moneyA->add($moneyB);

        $this->assertTrue($expected->equals($result));
        $this->assertNotSame($moneyA, $result);
        $this->assertNotSame($moneyB, $result);
    }
}