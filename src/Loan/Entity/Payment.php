<?php

declare(strict_types=1);

namespace App\Loan\Entity;

use App\Loan\Entity\Embeddable\Payer;
use App\Loan\Entity\Enums\PaymentStatus;
use App\Loan\Repository\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
#[ORM\Index(fields: ['paymentDate'])]
class Payment
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Column]
    private ?int $amount = null;

    #[ORM\Column(length: 30)]
    private PaymentStatus $status = PaymentStatus::Pending;

    #[ORM\Column(unique: true)]
    private ?string $refId = null;

    #[ORM\Embedded]
    private ?Payer $payer = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $paymentDate = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?PaymentOrder $paymentOrder = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Loan $loan = null;

    public static function create(
        int $amount,
        string $refId,
        \DateTimeImmutable $paymentDate,
        Payer $payer,
        Loan $loan,
    ): self {
        return (new Payment())
            ->setAmount($amount)
            ->setPayer($payer)
            ->setStatus(PaymentStatus::Pending)
            ->setRefId($refId)
            ->setPaymentDate($paymentDate)
            ->setLoan($loan)
        ;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getStatus(): PaymentStatus
    {
        return $this->status;
    }

    public function setStatus(PaymentStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getRefId(): ?string
    {
        return $this->refId;
    }

    public function setRefId(string $refId): static
    {
        $this->refId = $refId;

        return $this;
    }

    public function getLoan(): ?Loan
    {
        return $this->loan;
    }

    public function setLoan(?Loan $loan): static
    {
        $this->loan = $loan;

        return $this;
    }

    public function getPaymentDate(): ?\DateTimeImmutable
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(?\DateTimeImmutable $paymentDate): self
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    public function getPayer(): ?Payer
    {
        return $this->payer;
    }

    public function setPayer(?Payer $payer): self
    {
        $this->payer = $payer;

        return $this;
    }

    public function processPayment(): void
    {
        if ($this->isAmountMatchedByLoanAmount()) {
            $this->markAsAssigned();
            $this->loan?->decreaseAmountToPay((int) $this->amount);

            return;
        }

        if ($this->isAmountMoreThenLoanAmount()) {
            $this->addPaymentOrder();
            $this->loan?->decreaseAmountToPay((int) $this->loan->getAmountToPay());

            return;
        }

        if ($this->isAmountLessThenLoanAmount()) {
            $this->markAsAssigned();
            $this->loan?->decreaseAmountToPay((int) $this->amount);

            return;
        }
    }

    public function isAssigned(): bool
    {
        return PaymentStatus::Assigned === $this->status;
    }

    public function isPartiallyAssigned(): bool
    {
        return PaymentStatus::PartiallyAssigned === $this->status;
    }

    public function getPaymentOrder(): ?PaymentOrder
    {
        return $this->paymentOrder;
    }

    public function getCustomerId(): string
    {
        return (string) $this->loan?->getCustomerId()?->toRfc4122();
    }

    private function isAmountMatchedByLoanAmount(): bool
    {
        return $this->amount === $this->getLoan()?->getAmountToPay();
    }

    private function isAmountMoreThenLoanAmount(): bool
    {
        return $this->amount > $this->getLoan()?->getAmountToPay();
    }

    private function isAmountLessThenLoanAmount(): bool
    {
        return $this->amount < $this->getLoan()?->getAmountToPay();
    }

    private function markAsAssigned(): void
    {
        $this->status = PaymentStatus::Assigned;
    }

    private function markAsPartiallyAssigned(): void
    {
        $this->status = PaymentStatus::PartiallyAssigned;
    }

    private function addPaymentOrder(): void
    {
        $this->paymentOrder = (new PaymentOrder())
            ->setPayment($this)
            ->setAmount((int) $this->amount - (int) $this->loan?->getAmountToPay());

        $this->markAsPartiallyAssigned();
    }
}
