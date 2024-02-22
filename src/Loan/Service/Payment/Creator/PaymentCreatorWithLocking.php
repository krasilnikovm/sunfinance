<?php

declare(strict_types=1);

namespace App\Loan\Service\Payment\Creator;

use App\Loan\Service\Payment\Exception\DuplicatePaymentException;
use App\Loan\Service\Payment\PaymentData;
use Psr\Log\LoggerInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;

final readonly class PaymentCreatorWithLocking implements PaymentCreatorInterface
{
    public function __construct(
        private LockFactory $lockFactory,
        private PaymentCreator $decorator,
        private LoggerInterface $logger
    ) {
    }

    public function create(array $paymentDataCollection): array
    {
        ['getLock' => $getLock, 'reset' => $reset] = $this->getLockPoolClosures();

        foreach ($paymentDataCollection as $paymentData) {
            $lock = $getLock($paymentData);

            if ($lock->isAcquired()) {
                $this->logger->warning('duplicated payment found', [
                    'refId' => $paymentData->refId,
                    'reference' => $paymentData->getReference(),
                ]);

                $this->releaseLocks($getLock, $paymentDataCollection);
                $reset();
                throw new DuplicatePaymentException($paymentData->refId);
            }

            $lock->acquire(true);
        }

        try {
            return $this->decorator->create($paymentDataCollection);
        } finally {
            $this->releaseLocks($getLock, $paymentDataCollection);
            $reset();
        }
    }

    /**
     * @param callable(PaymentData): LockInterface $getLock
     * @param array<array-key, PaymentData>        $paymentDataCollection
     */
    private function releaseLocks(callable $getLock, array $paymentDataCollection): void
    {
        foreach ($paymentDataCollection as $paymentData) {
            $getLock($paymentData)->release();
        }
    }

    /**
     * @return array{
     *     getLock: callable(PaymentData): LockInterface,
     *     reset: callable(): void
     * }
     */
    private function getLockPoolClosures(): array
    {
        $locks = [];

        return [
            'getLock' => function (PaymentData $paymentData) use (&$locks): LockInterface {
                $key = sprintf('payment-creator-%s', $paymentData->refId);
                if ($lock = $locks[$key] ?? null) {
                    return $lock;
                }

                return $locks[$key] = $this->lockFactory->createLock($key);
            },
            'reset' => static function () use (&$locks): void {
                $locks = [];
            },
        ];
    }
}
