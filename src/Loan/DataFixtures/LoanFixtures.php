<?php

declare(strict_types=1);

namespace App\Loan\DataFixtures;

use App\Loan\Entity\Enums\LoanState;
use App\Loan\Entity\Loan;
use App\Loan\Service\Payment\AmountConverter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

final class LoanFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getLoansTemplate() as $loanTemplate) {
            $manager->persist(
                (new Loan())
                ->setId(Uuid::fromString($loanTemplate['id']))
                ->setCustomerId(Uuid::fromString($loanTemplate['customerId']))
                ->setReference($loanTemplate['reference'])
                ->setState(LoanState::from($loanTemplate['state']))
                ->setAmountIssued(AmountConverter::toInt($loanTemplate['amountIssued']))
                ->setAmountToPay(AmountConverter::toInt($loanTemplate['amountToPay']))
            );
        }

        $manager->flush();
    }

    /**
     * @return array<array-key, array{
     *     id: string,
     *     customerId: string,
     *     reference: string,
     *     state: string,
     *     amountIssued: string,
     *     amountToPay: string,
     * }>
     */
    private function getLoansTemplate(): array
    {
        return [
            [
                'id' => '51ed9314-955c-4014-8be2-b0e2b13588a5',
                'customerId' => 'c539792e-7773-4a39-9cf6-f273b2581438',
                'reference' => 'LN12345678',
                'state' => 'ACTIVE',
                'amountIssued' => '100.00',
                'amountToPay' => '120.00',
            ],
            [
                'id' => 'a54b0796-2fcb-4547-b23d-125786600ec3',
                'customerId' => 'c539792e-7773-4a39-9cf6-f273b2581438',
                'reference' => 'LN22345678',
                'state' => 'ACTIVE',
                'amountIssued' => '200.00',
                'amountToPay' => '250.00',
            ],
            [
                'id' => 'f7f81281-64a9-47a7-af60-5c6896896d1f',
                'customerId' => 'd275ce5e-91c8-49fe-9407-1700b59efe80',
                'reference' => 'LN55522533',
                'state' => 'ACTIVE',
                'amountIssued' => '50.00',
                'amountToPay' => '70.00',
            ],
            [
                'id' => 'b8d26e7b-1607-441d-8bb0-87517a874572',
                'customerId' => 'c5c05eeb-ff02-4de6-b92e-a1b7f02320df',
                'reference' => 'LN20221212',
                'state' => 'ACTIVE',
                'amountIssued' => '66.00',
                'amountToPay' => '100.00',
            ],
        ];
    }
}
