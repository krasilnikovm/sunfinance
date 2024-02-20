<?php

declare(strict_types=1);

namespace App\Customer\Service\Communication;

use App\Communication\Service\Customer\Customer;
use App\Communication\Service\Customer\CustomerProviderInterface;
use App\Customer\Entity;
use App\Customer\Repository\CustomerRepository;
use Symfony\Component\Uid\Uuid;

final readonly class CustomerProvider implements CustomerProviderInterface
{
    public function __construct(
        private CustomerRepository $customerRepository,
    ) {
    }

    #[\Override]
    public function provide(string $customerId): ?Customer
    {
        $customer = $this->customerRepository->find(Uuid::fromString($customerId)->toHex());

        if (!$customer instanceof Entity\Customer) {
            return null;
        }

        return new Customer(
            id: $customer->getId()->toRfc4122(),
            firstname: (string) $customer->getFirstname(),
            lastname: (string) $customer->getLastname(),
            email: $customer->getEmail(),
            phone: $customer->getPhone()
        );
    }
}
