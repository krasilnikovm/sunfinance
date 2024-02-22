<?php

declare(strict_types=1);

namespace App\Loan\Entity\Enums;

enum LoanState: string
{
    case Active = 'ACTIVE';
    case Paid = 'PAID';
}
