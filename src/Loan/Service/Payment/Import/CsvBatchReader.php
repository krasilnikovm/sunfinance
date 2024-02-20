<?php

declare(strict_types=1);

namespace App\Loan\Service\Payment\Import;

use League\Csv\Reader;
use League\Csv\Statement;
use Webmozart\Assert\Assert;

final class CsvBatchReader implements BatchReaderInterface
{
    #[\Override]
    public function readBatch(string $filepath, int $batchSize): iterable
    {
        $stream = fopen($filepath, 'r');

        if (false === $stream) {
            throw new \InvalidArgumentException('can not open stream');
        }

        $csv = Reader::createFromStream($stream);

        $csv->setHeaderOffset(0);

        $offset = 0;
        do {
            $statement = Statement::create()
                ->offset($offset)
                ->limit($batchSize);

            $tabularData = $statement->process($csv);

            yield array_map(static function (array $row) {
                Assert::keyExists($row, 'payerName');
                Assert::keyExists($row, 'payerSurname');
                Assert::keyExists($row, 'paymentDate');
                Assert::keyExists($row, 'amount');
                Assert::keyExists($row, 'description');
                Assert::keyExists($row, 'paymentReference');

                return [
                    'payerName' => (string) $row['payerName'],
                    'payerSurname' => (string) $row['payerName'],
                    'paymentDate' => (string) $row['paymentDate'],
                    'amount' => (string) $row['amount'],
                    'description' => (string) $row['description'],
                    'paymentReference' => (string) $row['paymentReference'],
                ];
            }, array_values(iterator_to_array($tabularData->getRecords())));
        } while ($tabularData->getRecords()->valid());
    }
}
