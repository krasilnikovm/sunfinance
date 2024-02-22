<?php

declare(strict_types=1);

namespace App\Tests\Unit\Loan\Entity;

use App\Loan\Entity\Loan;
use PHPUnit\Framework\TestCase;

final class LoanTest extends TestCase
{
    private const int AMOUNT = 10_050;

    public function testShouldDecreasePartOfAmountToPay(): void
    {
        $loan = (new Loan())
            ->setAmountToPay(self::AMOUNT)
        ;

        $loan->decreaseAmountToPay(40);

        $expected = 10010;
        $actual = $loan->getAmountToPay();

        self::assertEquals($expected, $actual);
        self::assertTrue($loan->isActive());
    }

    public function testShouldDecreaseAllAmountToPay(): void
    {
        $loan = (new Loan())
            ->setAmountToPay(self::AMOUNT)
        ;

        $loan->decreaseAmountToPay(self::AMOUNT);

        $expected = 0;
        $actual = $loan->getAmountToPay();

        self::assertEquals($expected, $actual);
        self::assertTrue($loan->isPaid());
    }

    public function testShouldNowAllowDecreaseMoreThenAmountToPay(): void
    {
        $this->expectException(\DomainException::class);

        $loan = (new Loan())
            ->setAmountToPay(self::AMOUNT)
        ;

        $loan->decreaseAmountToPay(10_050_500);
    }
}
