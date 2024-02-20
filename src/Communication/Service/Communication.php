<?php

declare(strict_types=1);

namespace App\Communication\Service;

use App\Communication\Service\Customer\Customer;
use App\Communication\Service\Customer\CustomerProviderInterface;
use App\Loan\Service\Communication\CommunicationInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

final readonly class Communication implements CommunicationInterface
{
    public function __construct(
        private CustomerProviderInterface $customerProvider,
        private NotifierInterface $notifier,
        private LoggerInterface $logger,
    ) {
    }

    #[\Override]
    public function sendPaymentReceivedNotification(string $customerId): void
    {
        $customer = $this->customerProvider->provide($customerId);

        if (!$customer instanceof Customer) {
            $this->logger->warning('user not found, notification message skipped', [
                'customerId' => $customerId,
            ]);

            return;
        }

        $this->sendNotification(
            $customer,
            "Hi {$customer->firstname} {$customer->lastname}! SunFinance received your payment. Thanks!",
        );
    }

    #[\Override]
    public function sendLoanPaidNotification(string $customerId): void
    {
        $customer = $this->customerProvider->provide($customerId);

        if (!$customer instanceof Customer) {
            $this->logger->warning('user not found, notification message skipped', [
                'customerId' => $customerId,
            ]);

            return;
        }

        $this->sendNotification(
            $customer,
            "Hi {$customer->firstname} {$customer->lastname}! Your Loan fully paid.",
        );
    }

    private function sendNotification(Customer $customer, string $content): void
    {
        $channels = $this->getChannels($customer);

        if (empty($channels)) {
            $this->logger->info('user has not phone and email, notification message skipped', [
                'customerId' => $customer->id,
            ]);

            return;
        }

        $notification = new Notification('SunFinance Payment Notification', $channels);
        $notification->content($content);

        $this->notifier->send($notification, new Recipient((string) $customer->email, (string) $customer->phone));
    }

    /**
     * @return string[]
     */
    private function getChannels(Customer $customer): array
    {
        $channels = [];

        if ($customer->phone) {
            $channels[] = 'phone';
        }

        if ($customer->email) {
            $channels[] = 'email';
        }

        return $channels;
    }
}
