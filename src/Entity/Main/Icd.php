<?php

namespace App\Entity\Main;

use App\Repository\Main\IcdRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=IcdRepository::class)
 */
class Icd
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ref;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sref1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sref2;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $designation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $qte;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pu;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pu_corrige;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(string $ref): self
    {
        $this->ref = $ref;

        return $this;
    }

    public function getSref1(): ?string
    {
        return $this->sref1;
    }

    public function setSref1(?string $sref1): self
    {
        $this->sref1 = $sref1;

        return $this;
    }

    public function getSref2(): ?string
    {
        return $this->sref2;
    }

    public function setSref2(?string $sref2): self
    {
        $this->sref2 = $sref2;

        return $this;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): self
    {
        $this->designation = $designation;

        return $this;
    }

    public function getQte(): ?string
    {
        return $this->qte;
    }

    public function setQte(string $qte): self
    {
        $this->qte = $qte;

        return $this;
    }

    public function getPu(): ?string
    {
        return $this->pu;
    }

    public function setPu(?string $pu): self
    {
        $this->pu = $pu;

        return $this;
    }

    public function getPuCorrige(): ?string
    {
        return $this->pu_corrige;
    }

    public function setPuCorrige(?string $pu_corrige): self
    {
        $this->pu_corrige = $pu_corrige;

        return $this;
    }
}
