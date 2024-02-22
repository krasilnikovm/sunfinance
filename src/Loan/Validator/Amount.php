<?php

declare(strict_types=1);

namespace App\Loan\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class Amount extends Constraint
{
    public const string INVALID_AMOUNT_FORMAT_MESSAGE = 'The "amount" "{{ value }}" is not valid.';
    public const string NEGATIVE_AMOUNT_MESSAGE = 'The "amount" value "{{ value }}" is negative.';
}
