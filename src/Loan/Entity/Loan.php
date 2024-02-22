<?php

declare(strict_types=1);

namespace App\Loan\Entity;

use App\Loan\Entity\Enums\LoanState;
use App\Loan\Repository\LoanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: LoanRepository::class)]
class Loan
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $customerId = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $reference = null;

    #[ORM\Column(type: 'string', enumType: LoanState::class)]
    private LoanState $state = LoanState::Active;

    #[ORM\Column]
    private ?int $amountIssued = null;

    #[ORM\Column]
    private ?int $amountToPay = null;

    /**
     * @var Collection<array-key, Payment>
     */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'loan')]
    private Collection $payments;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->payments = new ArrayCollection();
    }

    public function setId(Uuid $uuid): self
    {
        $this->id = $uuid;

        return $this;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCustomerId(): ?Uuid
    {
        return $this->customerId;
    }

    public function setCustomerId(Uuid $customerId): static
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getState(): LoanState
    {
        return $this->state;
    }

    public function setState(LoanState $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getAmountIssued(): ?int
    {
        return $this->amountIssued;
    }

    public function setAmountIssued(int $amountIssued): static
    {
        $this->amountIssued = $amountIssued;

        return $this;
    }

    public function getAmountToPay(): ?int
    {
        return $this->amountToPay;
    }

    public function setAmountToPay(int $amountToPay): static
    {
        $this->amountToPay = $amountToPay;

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setLoan($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getLoan() === $this) {
                $payment->setLoan(null);
            }
        }

        return $this;
    }

    public function markAsPaid(): void
    {
        $this->state = LoanState::Paid;
    }

    /**
     * @throws \DomainException
     */
    public function decreaseAmountToPay(int $amount): void
    {
        if ($amount > $this->amountToPay) {
            throw new \DomainException('not possible to decrease the amount more than it is');
        }

        $this->amountToPay -= $amount;

        if ($this->amountToPay <= 0) {
            $this->state = LoanState::Paid;
        }
    }

    public function isActive(): bool
    {
        return LoanState::Active === $this->state;
    }

    public function isPaid(): bool
    {
        return LoanState::Paid === $this->state;
    }
}
