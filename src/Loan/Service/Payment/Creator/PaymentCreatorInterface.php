<?php

declare(strict_types=1);

namespace App\Loan\Service\Payment\Creator;

use App\Loan\Entity\Payment;
use App\Loan\Service\Payment\Exception\DuplicatePaymentException;
use App\Loan\Service\Payment\Exception\LoanNotFoundException;
use App\Loan\Service\Payment\Exception\ValidationException;
use App\Loan\Service\Payment\PaymentData;

interface PaymentCreatorInterface
{
    /**
     * @param array<array-key, PaymentData> $paymentDataCollection
     *
     * @return array<array-key, Payment>
     *
     * @throws DuplicatePaymentException
     * @throws LoanNotFoundException
     * @throws ValidationException
     */
    public function create(array $paymentDataCollection): array;
}
