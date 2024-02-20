<?php

declare(strict_types=1);

namespace App\Communication\Service\Customer;

final readonly class Customer
{
    public function __construct(
        public string $id,
        public string $firstname,
        public string $lastname,
        public ?string $email,
        public ?string $phone,
    ) {
    }
}
