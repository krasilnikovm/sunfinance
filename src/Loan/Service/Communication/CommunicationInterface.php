<?php

declare(strict_types=1);

namespace App\Loan\Service\Communication;

interface CommunicationInterface
{
    public function sendPaymentReceivedNotification(string $customerId): void;

    public function sendLoanPaidNotification(string $customerId): void;
}
