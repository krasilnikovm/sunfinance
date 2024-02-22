<?php

declare(strict_types=1);

namespace App\Loan\Entity\Enums;

enum PaymentStatus: string
{
    case Pending = 'PENDING';
    case Assigned = 'ASSIGNED';
    case PartiallyAssigned = 'PARTIALLY_ASSIGNED';
}
