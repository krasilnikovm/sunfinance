<?php

declare(strict_types=1);

namespace App\Loan\Service\Payment\Exception;

final class LoanNotFoundException extends \Exception
{
    private const string MESSAGE = 'the loan with reference "%s" not found';

    public function __construct(string $reference)
    {
        parent::__construct(sprintf(self::MESSAGE, $reference));
    }
}
