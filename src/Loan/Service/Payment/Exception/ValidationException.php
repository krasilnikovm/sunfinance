<?php

declare(strict_types=1);

namespace App\Loan\Service\Payment\Exception;

final class ValidationException extends \Exception
{
    private const string MESSAGE = 'validation exception';

    /**
     * @var array<array-key, string>
     */
    public readonly array $errors;

    /**
     * @param array<array-key, string> $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;

        parent::__construct(self::MESSAGE);
    }
}
