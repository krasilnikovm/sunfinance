<?php

declare(strict_types=1);

namespace App\Loan\Service\Payment\Exception;

final class DuplicatePaymentException extends \Exception
{
    private const string MESSAGE_PATTERN = 'there is a payment with refId "%s" in database';

    private string $refId;

    public function __construct(string $refId)
    {
        $this->refId = $refId;
        parent::__construct(sprintf(self::MESSAGE_PATTERN, $refId));
    }

    public function getRefId(): string
    {
        return $this->refId;
    }
}
