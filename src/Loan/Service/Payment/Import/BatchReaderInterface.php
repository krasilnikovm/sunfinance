<?php

declare(strict_types=1);

namespace App\Loan\Service\Payment\Import;

use phpDocumentor\Reflection\DocBlock\Description;

interface BatchReaderInterface
{
    /**
     * @return iterable<array<int, array{
     *      payerName: string,
     *      payerSurname: string,
     *      paymentDate: string,
     *      amount: string,
     *      description: string,
     *      paymentReference: string,
     *  }>>
     */
    public function readBatch(string $filepath, int $batchSize): iterable;
}
