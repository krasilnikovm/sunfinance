<?php

declare(strict_types=1);

namespace App\Loan\Service\Payment;

use App\Loan\Service\Payment\Exception\DuplicatePaymentException;
use App\Loan\Service\Payment\Exception\LoanNotFoundException;
use App\Loan\Service\Payment\Exception\ValidationException;
use App\Loan\Service\Payment\Import\BatchReaderInterface;

readonly class PaymentImporter
{
    private const int BATCH_SIZE = 500;

    public function __construct(
        private PaymentProcessor $paymentProcessor,
        private BatchReaderInterface $batchReader,
    ) {
    }

    /**
     * @throws DuplicatePaymentException
     * @throws LoanNotFoundException
     * @throws ValidationException
     */
    public function import(string $path): void
    {
        foreach ($this->batchReader->readBatch($path, self::BATCH_SIZE) as $batch) {
            $paymentDataCollection = array_map(
                function (array $row) {
                    return new PaymentData(
                        firstname: (string) $row['payerName'],
                        lastname: (string) $row['payerSurname'],
                        paymentDate: $this->createDateTimeFromString($row['paymentDate']),
                        amount: (string) $row['amount'],
                        description: (string) $row['description'],
                        refId: (string) $row['paymentReference'],
                    );
                },
                $batch
            );

            $this->paymentProcessor->processBatch($paymentDataCollection);
        }
    }

    private function createDateTimeFromString(string $datetime): \DateTimeImmutable
    {
        $date = \DateTimeImmutable::createFromFormat('YmdHis', $datetime);

        if ($date instanceof \DateTimeImmutable) {
            return $date;
        }

        $date = \DateTimeImmutable::createFromFormat('D, d M Y H:i:s O', $datetime);

        if (false === $date) {
            throw new ValidationException(errors: ['csv file contains invalid datetime format']);
        }

        return $date;
    }
}
