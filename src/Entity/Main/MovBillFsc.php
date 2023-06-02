<?php

namespace App\Entity\Main;

use App\Repository\Main\MovBillFscRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MovBillFscRepository::class)
 */
class MovBillFsc
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
     * @ORM\Column(type="integer", unique=true)
     */
    private $facture;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateFact;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tiers;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $notreRef;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $TypeTiers;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="movBillFscs")
     */
    private $createdBy;

    /**
     * @ORM\ManyToMany(targetEntity=fscListMovement::class, inversedBy="movBillFscs")
     */
    private $ventilations;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateBl;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $bl;

    public function __construct()
    {
        $this->ventilations = new ArrayCollection();
    }

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

    public function getFacture(): ?int
    {
        return $this->facture;
    }

    public function setFacture(int $facture): self
    {
        $this->facture = $facture;

        return $this;
    }

    public function getDateFact(): ?\DateTimeInterface
    {
        return $this->dateFact;
    }

    public function setDateFact(\DateTimeInterface $dateFact): self
    {
        $this->dateFact = $dateFact;

        return $this;
    }

    public function getTiers(): ?string
    {
        return $this->tiers;
    }

    public function setTiers(string $tiers): self
    {
        $this->tiers = $tiers;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getNotreRef(): ?string
    {
        return $this->notreRef;
    }

    public function setNotreRef(?string $notreRef): self
    {
        $this->notreRef = $notreRef;

        return $this;
    }

    public function getTypeTiers(): ?string
    {
        return $this->TypeTiers;
    }

    public function setTypeTiers(string $TypeTiers): self
    {
        $this->TypeTiers = $TypeTiers;

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

    /**
     * @return Collection|fscListMovement[]
     */
    public function getVentilations(): Collection
    {
        return $this->ventilations;
    }

    public function addVentilation(fscListMovement $ventilation): self
    {
        if (!$this->ventilations->contains($ventilation)) {
            $this->ventilations[] = $ventilation;
        }

        return $this;
    }

    public function removeVentilation(fscListMovement $ventilation): self
    {
        $this->ventilations->removeElement($ventilation);

        return $this;
    }

    public function getDateBl(): ?\DateTimeInterface
    {
        return $this->dateBl;
    }

    public function setDateBl(?\DateTimeInterface $dateBl): self
    {
        $this->dateBl = $dateBl;

        return $this;
    }

    public function getBl(): ?int
    {
        return $this->bl;
    }

    public function setBl(?int $bl): self
    {
        $this->bl = $bl;

        return $this;
    }
}
