<?php

declare(strict_types=1);

namespace App\Loan\Event;

final readonly class LoanPaidEvent
{
    public function __construct(
        public string $customerId
    ) {
    }
}
