<?php

namespace App\Entity\Main;

use App\Repository\Main\JardinewProductsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JardinewProductsRepository::class)]
class JardinewProducts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    #[ORM\Column(length: 255)]
    private ?string $sku = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ref = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sref1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sref2 = null;

    #[ORM\Column(nullable: true)]
    private ?float $lastPurchase = null;

    #[ORM\Column(nullable: true)]
    private ?float $stock = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $permalien = null;

    #[ORM\Column(nullable: true)]
    private ?int $idWordpress = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $marge = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datePurchase = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $closed = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $uv = null;

    #[ORM\Column(nullable: true)]
    private ?float $port = null;

    #[ORM\Column(nullable: true)]
    private ?float $previousPurchase = null;

    #[ORM\Column(nullable: true)]
    private ?float $coeffConversion = null;

    #[ORM\Column(nullable: true)]
    private ?int $NumberPurchase = null;

    #[ORM\Column(nullable: true)]
    private ?bool $validationPrice = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(string $sku): static
    {
        $this->sku = $sku;

        return $this;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(?string $ref): static
    {
        $this->ref = $ref;

        return $this;
    }

    public function getSref1(): ?string
    {
        return $this->sref1;
    }

    public function setSref1(?string $sref1): static
    {
        $this->sref1 = $sref1;

        return $this;
    }

    public function getSref2(): ?string
    {
        return $this->sref2;
    }

    public function setSref2(?string $sref2): static
    {
        $this->sref2 = $sref2;

        return $this;
    }

    public function getLastPurchase(): ?float
    {
        return $this->lastPurchase;
    }

    public function setLastPurchase(?float $lastPurchase): static
    {
        $this->lastPurchase = $lastPurchase;

        return $this;
    }

    public function getStock(): ?float
    {
        return $this->stock;
    }

    public function setStock(?float $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function getPermalien(): ?string
    {
        return $this->permalien;
    }

    public function setPermalien(?string $permalien): static
    {
        $this->permalien = $permalien;

        return $this;
    }

    public function getIdWordpress(): ?int
    {
        return $this->idWordpress;
    }

    public function setIdWordpress(?int $idWordpress): static
    {
        $this->idWordpress = $idWordpress;

        return $this;
    }

    public function getMarge(): ?string
    {
        return $this->marge;
    }

    public function setMarge(?string $marge): static
    {
        $this->marge = $marge;

        return $this;
    }

    public function getDatePurchase(): ?\DateTimeInterface
    {
        return $this->datePurchase;
    }

    public function setDatePurchase(?\DateTimeInterface $datePurchase): static
    {
        $this->datePurchase = $datePurchase;

        return $this;
    }

    public function getClosed(): ?string
    {
        return $this->closed;
    }

    public function setClosed(?string $closed): static
    {
        $this->closed = $closed;

        return $this;
    }

    public function getUv(): ?string
    {
        return $this->uv;
    }

    public function setUv(?string $uv): static
    {
        $this->uv = $uv;

        return $this;
    }

    public function getPort(): ?float
    {
        return $this->port;
    }

    public function setPort(?float $port): static
    {
        $this->port = $port;

        return $this;
    }

    public function getPreviousPurchase(): ?float
    {
        return $this->previousPurchase;
    }

    public function setPreviousPurchase(?float $previousPurchase): static
    {
        $this->previousPurchase = $previousPurchase;

        return $this;
    }

    public function getCoeffConversion(): ?float
    {
        return $this->coeffConversion;
    }

    public function setCoeffConversion(?float $coeffConversion): static
    {
        $this->coeffConversion = $coeffConversion;

        return $this;
    }

    public function getNumberPurchase(): ?int
    {
        return $this->NumberPurchase;
    }

    public function setNumberPurchase(?int $NumberPurchase): static
    {
        $this->NumberPurchase = $NumberPurchase;

        return $this;
    }

    public function isValidationPrice(): ?bool
    {
        return $this->validationPrice;
    }

    public function setValidationPrice(?bool $validationPrice): static
    {
        $this->validationPrice = $validationPrice;

        return $this;
    }
}
