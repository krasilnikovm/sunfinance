<?php

declare(strict_types=1);

namespace App\Tests\Application\Loan\Controller;

use App\Loan\DataFixtures\LoanFixtures;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\ResetDatabase;

class ProcessPaymentActionTest extends WebTestCase
{
    use ResetDatabase;
    private readonly KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient([], [
            'CONTENT_TYPE' => 'application/json',
        ]);

        /** @var LoanFixtures $fixtures */
        $fixtures = static::getContainer()->get(LoanFixtures::class);
        $doctrine = static::getContainer()->get('doctrine');

        $fixtures->load($doctrine->getManager());
    }

    public function testShouldSavePayment(): void
    {
        $this->sendValidPaymentApiRequest();

        $this->assertResponseIsSuccessful();
    }

    public function testShouldReturnConflictResponse(): void
    {
        $this->sendValidPaymentApiRequest();
        $this->assertResponseIsSuccessful();

        $this->sendValidPaymentApiRequest();
        $this->assertResponseStatusCodeSame(Response::HTTP_CONFLICT);
    }

    public function testShouldReturnBadRequestResponse(): void
    {
        $this->client->request(
            method: 'POST',
            uri: '/api/payment',
            content: json_encode([
                'firstname' => 'Test',
                'refId' => '130d8a89-11a9-47d0-d6ef-1aea54924d3b',
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    private function sendValidPaymentApiRequest(): void
    {
        $this->client->request(
            method: 'POST',
            uri: '/api/payment',
            content: json_encode([
                'firstname' => 'Test',
                'lastname' => 'Test',
                'paymentDate' => '2022-12-12T15:19:21+00:00',
                'amount' => '70.00',
                'description' => 'NANA LN55522533',
                'refId' => '130d8a89-11a9-47d0-d6ef-1aea54924d3b',
            ])
        );
    }
}
