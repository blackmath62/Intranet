<?php

namespace App\Entity\Main;

use App\Entity\Main\users;
use App\Repository\Main\RetraitMarchandisesEanRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=RetraitMarchandisesEanRepository::class)
 */
class RetraitMarchandisesEan
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $chantier;

    /**
     * @ORM\ManyToOne(targetEntity=users::class, inversedBy="retraitMarchandisesEans")
     */
    private $createdBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $sendAt;

    /**
     * @ORM\Column(type="string", length=13)
     * @Assert\Length(min=13,max=13, minMessage="Le code EAN doit faire 13 caractéres numériques de long",maxMessage="Le code EAN doit faire 13 caractéres numériques de long")
     */
    private $ean;

    /**
     * @ORM\Column(type="float")
     */
    private $qte;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getEan(): ?int
    {
        return $this->ean;
    }

    public function setEan(int $ean): self
    {
        $this->ean = $ean;

        return $this;
    }

    public function getChantier(): ?string
    {
        return $this->chantier;
    }

    public function setChantier(string $chantier): self
    {
        $this->chantier = $chantier;

        return $this;
    }

    public function getCreatedBy(): ?users
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?users $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getSendAt(): ?\DateTimeInterface
    {
        return $this->sendAt;
    }

    public function setSendAt(?\DateTimeInterface $sendAt): self
    {
        $this->sendAt = $sendAt;

        return $this;
    }

    public function getQte(): ?float
    {
        return $this->qte;
    }

    public function setQte(float $qte): self
    {
        $this->qte = $qte;

        return $this;
    }
}
