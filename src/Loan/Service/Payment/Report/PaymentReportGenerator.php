<?php

declare(strict_types=1);

namespace App\Loan\Service\Payment\Report;

use App\Loan\Repository\PaymentRepository;
use App\Loan\Service\Payment\AmountConverter;

final readonly class PaymentReportGenerator
{
    private const int LIMIT = 100;

    public function __construct(
        private PaymentRepository $paymentRepository,
    ) {
    }

    /**
     * @return iterable<PaymentModel>
     */
    public function generateByPaymentDate(\DateTimeImmutable $paymentDate): iterable
    {
        $total = $this->paymentRepository->fetchCountPaymentsByPaymentDate($paymentDate);
        $pages = (int) ceil($total / self::LIMIT);

        for ($currentPage = 1; $currentPage <= $pages; ++$currentPage) {
            $payments = $this->paymentRepository->fetchPaymentsByPaymentDate(
                $paymentDate,
                $currentPage,
                self::LIMIT
            );

            foreach ($payments as $payment) {
                yield new PaymentModel(
                    id: $payment->getId()->toRfc4122(),
                    amount: AmountConverter::toString((int) $payment->getAmount()),
                    status: $payment->getStatus()->value,
                    refId: (string) $payment->getRefId(),
                    payerName: (string) $payment->getPayer()?->getFirstname(),
                    payerSurname: (string) $payment->getPayer()?->getLastname(),
                );
            }
        }
    }
}
