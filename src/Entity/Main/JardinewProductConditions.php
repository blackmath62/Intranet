<?php

namespace App\Entity\Main;

use App\Repository\Main\JardinewProductConditionsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JardinewProductConditionsRepository::class)]
class JardinewProductConditions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $purchase = null;

    #[ORM\Column]
    private ?int $idWordpress = null;

    #[ORM\Column(nullable: true)]
    private ?float $coeffCorrection = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPurchase(): ?int
    {
        return $this->purchase;
    }

    public function setPurchase(int $purchase): static
    {
        $this->purchase = $purchase;

        return $this;
    }

    public function getIdWordpress(): ?int
    {
        return $this->idWordpress;
    }

    public function setIdWordpress(int $idWordpress): static
    {
        $this->idWordpress = $idWordpress;

        return $this;
    }

    public function getCoeffCorrection(): ?float
    {
        return $this->coeffCorrection;
    }

    public function setCoeffCorrection(?float $coeffCorrection): static
    {
        $this->coeffCorrection = $coeffCorrection;

        return $this;
    }
}
