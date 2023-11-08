<?php

namespace App\Entity\Main;

use App\Repository\Main\StatutsGenerauxRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatutsGenerauxRepository::class)]
class StatutsGeneraux
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $textColor = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $backgroundColor = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $faIconsClass = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type : Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $closedAt = null;

    #[ORM\Column(length : 255, nullable: true)]
    private ?string $entity = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pilotage = null;

    #[ORM\OneToMany(mappedBy: 'typeIntervention', targetEntity: InterventionMonteurs::class)]
    private Collection $statutInverventionMonteurs;

    public function __construct()
    {
        $this->statutInverventionMonteurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getTextColor(): ?string
    {
        return $this->textColor;
    }

    public function setTextColor(?string $textColor): static
    {
        $this->textColor = $textColor;

        return $this;
    }

    public function getBackgroundColor(): ?string
    {
        return $this->backgroundColor;
    }

    public function setBackgroundColor(?string $backgroundColor): static
    {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }

    public function getFaIconsClass(): ?string
    {
        return $this->faIconsClass;
    }

    public function setFaIconsClass(?string $faIconsClass): static
    {
        $this->faIconsClass = $faIconsClass;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getClosedAt(): ?\DateTimeInterface
    {
        return $this->closedAt;
    }

    public function setClosedAt(?\DateTimeInterface $closedAt): static
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    public function getEntity(): ?string
    {
        return $this->entity;
    }

    public function setEntity(?string $entity): static
    {
        $this->entity = $entity;

        return $this;
    }

    public function getPilotage(): ?string
    {
        return $this->pilotage;
    }

    public function setPilotage(?string $pilotage): static
    {
        $this->pilotage = $pilotage;

        return $this;
    }

    /**
     * @return Collection<int, InterventionMonteurs>
     */
    public function getStatutInverventionMonteurs(): Collection
    {
        return $this->statutInverventionMonteurs;
    }

    public function addStatutInverventionMonteur(InterventionMonteurs $statutInverventionMonteur): static
    {
        if (!$this->statutInverventionMonteurs->contains($statutInverventionMonteur)) {
            $this->statutInverventionMonteurs->add($statutInverventionMonteur);
            $statutInverventionMonteur->setTypeIntervention($this);
        }

        return $this;
    }

    public function removeStatutInverventionMonteur(InterventionMonteurs $statutInverventionMonteur): static
    {
        if ($this->statutInverventionMonteurs->removeElement($statutInverventionMonteur)) {
            // set the owning side to null (unless already changed)
            if ($statutInverventionMonteur->getTypeIntervention() === $this) {
                $statutInverventionMonteur->setTypeIntervention(null);
            }
        }

        return $this;
    }
}
