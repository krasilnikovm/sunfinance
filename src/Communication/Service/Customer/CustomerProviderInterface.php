<?php

declare(strict_types=1);

namespace App\Communication\Service\Customer;

interface CustomerProviderInterface
{
    public function provide(string $customerId): ?Customer;
}
