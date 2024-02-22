<?php

namespace App\Loan\Entity\Embeddable;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Payer
{
    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    public static function create(string $firstname, string $lastname): self
    {
        return (new self())
            ->setFirstname($firstname)
            ->setLastname($lastname)
        ;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }
}
