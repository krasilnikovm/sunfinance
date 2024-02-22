<?php

declare(strict_types=1);

namespace App\Loan\EventSubscriber;

use App\Loan\Event\PaymentReceivedEvent;
use App\Loan\Service\Communication\CommunicationInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class PaymentReceivedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private CommunicationInterface $communication,
    ) {
    }

    public function onSendNotification(PaymentReceivedEvent $event): void
    {
        $this->communication->sendPaymentReceivedNotification($event->customerId);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PaymentReceivedEvent::class => 'onSendNotification',
        ];
    }
}
