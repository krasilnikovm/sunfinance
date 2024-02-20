<?php

declare(strict_types=1);

namespace App\Loan\Service\Payment;

use App\Loan\Entity\Payment;
use App\Loan\Event\LoanPaidEvent;
use App\Loan\Event\PaymentReceivedEvent;
use App\Loan\Service\Payment\Creator\PaymentCreatorInterface;
use App\Loan\Service\Payment\Exception\DuplicatePaymentException;
use App\Loan\Service\Payment\Exception\LoanNotFoundException;
use App\Loan\Service\Payment\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

readonly class PaymentProcessor
{
    public function __construct(
        private PaymentCreatorInterface $paymentCreator,
        private EventDispatcherInterface $eventDispatcher,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws DuplicatePaymentException
     * @throws LoanNotFoundException
     * @throws ValidationException
     */
    public function process(PaymentData $paymentData): void
    {
        $this->processBatch([$paymentData]);
    }

    /**
     * @param array<array-key, PaymentData> $paymentDataCollection
     *
     * @throws DuplicatePaymentException
     * @throws LoanNotFoundException
     * @throws ValidationException
     */
    public function processBatch(array $paymentDataCollection): void
    {
        Assert::allIsInstanceOf($paymentDataCollection, PaymentData::class);

        $payments = $this->paymentCreator->create($paymentDataCollection);

        foreach ($payments as $payment) {
            $payment->processPayment();
        }

        $this->entityManager->flush();

        $this->dispatchEvents($payments);
    }

    /**
     * @param array<array-key, Payment> $payments
     */
    private function dispatchEvents(array $payments): void
    {
        foreach ($payments as $payment) {
            if ($payment->isAssigned() || $payment->isPartiallyAssigned()) {
                $this->eventDispatcher->dispatch(new PaymentReceivedEvent($payment->getCustomerId()));
            }

            if ($payment->getLoan()?->isPaid()) {
                $this->eventDispatcher->dispatch(new LoanPaidEvent($payment->getCustomerId()));
            }
        }
    }
}
