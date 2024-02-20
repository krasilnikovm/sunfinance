<?php

declare(strict_types=1);

namespace App\Loan\Service\Payment\Creator;

use App\Loan\Entity\Embeddable\Payer;
use App\Loan\Entity\Loan;
use App\Loan\Entity\Payment;
use App\Loan\Repository\LoanRepository;
use App\Loan\Repository\PaymentRepository;
use App\Loan\Service\Payment\AmountConverter;
use App\Loan\Service\Payment\Exception\DuplicatePaymentException;
use App\Loan\Service\Payment\Exception\LoanNotFoundException;
use App\Loan\Service\Payment\Exception\ValidationException;
use App\Loan\Service\Payment\PaymentData;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Webmozart\Assert\Assert;

final readonly class PaymentCreator implements PaymentCreatorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PaymentRepository $paymentRepository,
        private ValidatorInterface $validator,
        private LoanRepository $loanRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @param array<array-key, PaymentData> $paymentDataCollection
     *
     * @return array<array-key, Payment>
     *
     * @throws DuplicatePaymentException
     * @throws LoanNotFoundException
     * @throws ValidationException
     */
    #[\Override]
    public function create(array $paymentDataCollection): array
    {
        Assert::allIsInstanceOf($paymentDataCollection, PaymentData::class);

        foreach ($paymentDataCollection as $paymentData) {
            $this->validate($paymentData);
        }

        $payments = [];

        foreach ($paymentDataCollection as $paymentData) {
            $loan = $this->fetchLoanByReference($paymentData->getReference());

            $payments[] = Payment::create(
                amount: AmountConverter::toInt($paymentData->amount),
                refId: $paymentData->refId,
                paymentDate: $paymentData->paymentDate,
                payer: Payer::create(
                    firstname: $paymentData->firstname,
                    lastname: $paymentData->lastname,
                ),
                loan: $loan,
            );

            $this->entityManager->persist($payments[array_key_last($payments)]);
        }

        $this->entityManager->flush();

        return $payments;
    }

    /**
     * @throws DuplicatePaymentException
     * @throws ValidationException
     */
    private function validate(PaymentData $paymentData): void
    {
        $violations = $this->validator->validate($paymentData);

        $errors = [];

        foreach ($violations as $error) {
            if ($error->getMessage() instanceof \Stringable) {
                $errors[] = $error->getMessage()->__toString();
            }

            if (is_string($error->getMessage())) {
                $errors[] = $error->getMessage();
            }
        }

        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }

        if ($this->isPaymentExist($paymentData)) {
            $this->logger->warning('duplicated request detected', [
                'refId' => $paymentData->refId,
                'reference' => $paymentData->getReference(),
            ]);

            throw new DuplicatePaymentException($paymentData->refId);
        }
    }

    private function isPaymentExist(PaymentData $paymentData): bool
    {
        $payment = $this->paymentRepository->fetchPaymentByRefId($paymentData->refId);

        return $payment instanceof Payment;
    }

    /**
     * @throws LoanNotFoundException
     */
    private function fetchLoanByReference(string $reference): Loan
    {
        $loan = $this->loanRepository->fetchLoanByReference($reference);

        if (null === $loan) {
            throw new LoanNotFoundException($reference);
        }

        return $loan;
    }
}
