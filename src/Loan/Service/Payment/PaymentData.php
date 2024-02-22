<?php

declare(strict_types=1);

namespace App\Loan\Service\Payment;

use App\Loan\Validator as SunAssert;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class PaymentData
{
    private const string PATTERN = '/LN\d{8}$/';

    public function __construct(
        #[Assert\NotBlank(message: 'the "firstname" must be not blank')]
        public string $firstname,

        #[Assert\NotBlank(message: 'the "lastname" must be not blank')]
        public string $lastname,

        public \DateTimeImmutable $paymentDate,

        #[Assert\NotBlank(message: 'the "amount" must be not blank')]
        #[SunAssert\Amount]
        public string $amount,

        #[Assert\NotBlank(message: 'the "description" must be not blank')]
        #[Assert\Regex(self::PATTERN)]
        public string $description,

        #[Assert\NotBlank(message: 'the "refId" must be not blank')]
        public string $refId,
    ) {
    }

    public function getReference(): string
    {
        preg_match(self::PATTERN, $this->description, $matches);

        return $matches[0] ?? '';
    }
}
