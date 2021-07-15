<?php

namespace App\Entity\Main;

use App\Repository\Main\CopyFouRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CopyFouRepository::class)
 */
class CopyFou
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
    private $tiers;

    /**
     * @ORM\Column(type="integer")
     */
    private $dos;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $closedAt;

    /**
     * @ORM\ManyToMany(targetEntity=Decisionnel::class, mappedBy="Fournisseurs")
     */
    private $decisionnels;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function __construct()
    {
        $this->decisionnels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDos(): ?int
    {
        return $this->dos;
    }

    public function setDos(int $dos): self
    {
        $this->dos = $dos;

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

    public function getClosedAt(): ?\DateTimeInterface
    {
        return $this->closedAt;
    }

    public function setClosedAt(?\DateTimeInterface $closedAt): self
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    /**
     * @return Collection|Decisionnel[]
     */
    public function getDecisionnels(): Collection
    {
        return $this->decisionnels;
    }

    public function addDecisionnel(Decisionnel $decisionnel): self
    {
        if (!$this->decisionnels->contains($decisionnel)) {
            $this->decisionnels[] = $decisionnel;
            $decisionnel->addFournisseur($this);
        }

        return $this;
    }

    public function removeDecisionnel(Decisionnel $decisionnel): self
    {
        if ($this->decisionnels->removeElement($decisionnel)) {
            $decisionnel->removeFournisseur($this);
        }

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
