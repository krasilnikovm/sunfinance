<?php

declare(strict_types=1);

namespace App\Loan\Event;

final readonly class PaymentReceivedEvent
{
    public function __construct(
        public string $customerId
    ) {
    }
}
