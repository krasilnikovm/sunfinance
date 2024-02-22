<?php

declare(strict_types=1);

namespace App\Loan\EventSubscriber;

use App\Loan\Event\LoanPaidEvent;
use App\Loan\Service\Communication\CommunicationInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class LoanPaidSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private CommunicationInterface $communication,
    ) {
    }

    public function onSendNotification(LoanPaidEvent $event): void
    {
        $this->communication->sendLoanPaidNotification($event->customerId);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoanPaidEvent::class => 'onSendNotification',
        ];
    }
}
