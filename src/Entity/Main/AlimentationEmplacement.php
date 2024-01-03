<?php

namespace App\Entity\Main;

use App\Repository\Main\AlimentationEmplacementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlimentationEmplacementRepository::class)]

class AlimentationEmplacement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]

    private $id;

    #[ORM\Column(type: "datetime")]
    private $createdAt;

    #[ORM\Column(type: "string", length: 8)]
    private $emplacement;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $sendAt;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "alimentationEmplacements")]
    private $createdBy;

    #[ORM\Column(type: "string", length: 13)]
    private $ean;

    #[ORM\Column(length: 255)]
    private ?string $qte = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $oldLocation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt) : self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getEmplacement(): ?string
    {
        return $this->emplacement;
    }

    public function setEmplacement(string $emplacement): self
    {
        $this->emplacement = $emplacement;

        return $this;
    }

    public function getSendAt(): ?\DateTimeInterface
    {
        return $this->sendAt;
    }

    public function setSendAt(?\DateTimeInterface $sendAt) : self
    {
        $this->sendAt = $sendAt;

        return $this;
    }

    public function getCreatedBy(): ?Users
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?Users $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getEan(): ?string
    {
        return $this->ean;
    }

    public function setEan(string $ean): self
    {
        $this->ean = $ean;

        return $this;
    }

    public function getQte(): ?string
    {
        return $this->qte;
    }

    public function setQte(string $qte): static
    {
        $this->qte = $qte;

        return $this;
    }

    public function getOldLocation(): ?string
    {
        return $this->oldLocation;
    }

    public function setOldLocation(?string $oldLocation): static
    {
        $this->oldLocation = $oldLocation;

        return $this;
    }
}
