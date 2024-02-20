<?php

declare(strict_types=1);

namespace App\Loan\Service\Payment\Report;

final readonly class PaymentModel
{
    public function __construct(
        public string $id,
        public string $amount,
        public string $status,
        public string $refId,
        public string $payerName,
        public string $payerSurname,
    ) {
    }
}
