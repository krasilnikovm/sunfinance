<?php

declare(strict_types=1);

namespace App\Tests\Unit\Loan\Entity;

use App\Loan\Entity\Embeddable\Payer;
use App\Loan\Entity\Loan;
use App\Loan\Entity\Payment;
use PHPUnit\Framework\TestCase;

final class PaymentTest extends TestCase
{
    private const int AMOUNT = 10_050;
    private const string REF_ID = 'xmskam6#g4';
    private const string FIRSTNAME = 'Test';
    private const string LASTNAME = 'Test';

    public function testShouldCompleteLoanPayment(): void
    {
        $loan = (new Loan())
            ->setAmountToPay(self::AMOUNT)
        ;

        $payment = Payment::create(
            amount: self::AMOUNT,
            refId: self::REF_ID,
            paymentDate: new \DateTimeImmutable(),
            payer: Payer::create(
                firstname: self::FIRSTNAME,
                lastname: self::LASTNAME,
            ),
            loan: $loan
        );

        $payment->processPayment();

        $expectedAmountToPay = 0;

        self::assertTrue($loan->isPaid());
        self::assertTrue($payment->isAssigned());
        self::assertEquals($expectedAmountToPay, $loan->getAmountToPay());
    }

    public function testShouldCompleteLoanPaymentWhenPaymentAmountMoreThenLoanAmount(): void
    {
        $loan = (new Loan())
            ->setAmountToPay(self::AMOUNT)
        ;

        $payment = Payment::create(
            amount: 11_050,
            refId: self::REF_ID,
            paymentDate: new \DateTimeImmutable(),
            payer: Payer::create(
                firstname: self::FIRSTNAME,
                lastname: self::LASTNAME,
            ),
            loan: $loan
        );

        $payment->processPayment();

        $expectedRefundAmount = 1000;
        $expectedAmountToPay = 0;

        self::assertTrue($loan->isPaid());
        self::assertTrue($payment->isPartiallyAssigned());

        self::assertEquals($expectedAmountToPay, $loan->getAmountToPay());
        self::assertEquals($payment->getPaymentOrder()?->getAmount(), $expectedRefundAmount);
    }

    public function testShouldCompleteLoanPaymentWhenPaymentAmountLessThenLoanAmount(): void
    {
        $loan = (new Loan())
            ->setAmountToPay(self::AMOUNT)
        ;

        $payment = Payment::create(
            amount: 40,
            refId: self::REF_ID,
            paymentDate: new \DateTimeImmutable(),
            payer: Payer::create(
                firstname: self::FIRSTNAME,
                lastname: self::LASTNAME,
            ),
            loan: $loan
        );

        $payment->processPayment();

        $expectedAmountToPay = 10010;

        self::assertTrue($loan->isActive());
        self::assertTrue($payment->isAssigned());
        self::assertEquals($expectedAmountToPay, $loan->getAmountToPay());
    }
}
