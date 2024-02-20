<?php

declare(strict_types=1);

namespace App\Customer\DataFixtures;

use App\Customer\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

final class CustomerFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getCustomersTemplate() as $customerTemplate) {
            $manager->persist(
                (new Customer())
                    ->setId(Uuid::fromString($customerTemplate['id']))
                    ->setFirstname($customerTemplate['firstname'])
                    ->setLastname($customerTemplate['lastname'])
                    ->setSsn($customerTemplate['ssn'])
                    ->setEmail($customerTemplate['email'] ?? null)
                    ->setPhone($customerTemplate['phone'] ?? null)
            );
        }

        $manager->flush();
    }

    /**
     * @return array<array-key, array{
     *     id: string,
     *     firstname: string,
     *     lastname: string,
     *     ssn: string,
     *     email?: string,
     *     phone?: string,
     * }>
     */
    private function getCustomersTemplate(): array
    {
        return [
            [
                'id' => 'c539792e-7773-4a39-9cf6-f273b2581438',
                'firstname' => 'Pupa',
                'lastname' => 'Lupa',
                'ssn' => '0987654321',
                'email' => 'pupa.lupa@example.com',
            ],
            [
                'id' => 'd275ce5e-91c8-49fe-9407-1700b59efe80',
                'firstname' => 'John',
                'lastname' => 'Doe',
                'ssn' => '1234509876',
                'phone' => '+44123456789',
            ],
            [
                'id' => 'a5c50ea9-9a24-4c8b-b4ae-c47ee007081e',
                'firstname' => 'Biba',
                'lastname' => 'Boba',
                'ssn' => '1234567890',
                'phone' => '+44123456780',
                'email' => 'biba@example.com',
            ],
            [
                'id' => 'c5c05eeb-ff02-4de6-b92e-a1b7f02320df',
                'firstname' => 'Lorem',
                'lastname' => 'Ipsum',
                'ssn' => '6789054321',
                'phone' => '+481230943320',
                'email' => 'lorem@ipsum',
            ],
        ];
    }
}
