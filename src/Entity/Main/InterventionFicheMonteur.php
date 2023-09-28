<?php

namespace App\Entity\Main;

use App\Repository\Main\InterventionFicheMonteurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InterventionFicheMonteurRepository::class)]
class InterventionFicheMonteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "datetime")]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "interventionFicheMonteursCreatedBy")]
    private $createdBy;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "interventionFicheMonteursIntervenant")]
    private $intervenant;

    #[ORM\OneToOne(targetEntity: Commentaires::class, cascade: ["persist", "remove"])]
    private $commentaire;

    #[ORM\Column(type: "array", nullable: true)]
    private $pension = [];

    #[ORM\OneToMany(targetEntity: InterventionFichesMonteursHeures::class, mappedBy: "interventionFicheMonteur")]
    private $heures;

    #[ORM\ManyToOne(targetEntity: InterventionMonteurs::class, inversedBy: "interventionFicheMonteurs")]
    private $intervention;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $validedAt;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "InterventionFicheMonteursValidedBy")]
    private $validedBy;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $lockedAt;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "interventionFicheMonteursLockedBy")]
    private $lockedBy;

    #[ORM\Column(type: "string", length: 1000, nullable: true)]
    private $comment;

    #[ORM\Column(type: "boolean")]
    private $here;

    function __construct()
    {
        $this->heures = new ArrayCollection();
    }

    function getId(): ?int
    {
        return $this->id;
    }

    function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    function setCreatedAt(\DateTimeInterface $createdAt) : self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    function getCreatedBy(): ?Users
    {
        return $this->createdBy;
    }

    function setCreatedBy(?Users $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    function getIntervenant(): ?Users
    {
        return $this->intervenant;
    }

    function setIntervenant(?Users $intervenant): self
    {
        $this->intervenant = $intervenant;

        return $this;
    }

    function getCommentaire(): ?Commentaires
    {
        return $this->commentaire;
    }

    function setCommentaire(?Commentaires $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    function getPension(): ?array
    {
        return $this->pension;
    }

    function setPension(?array $pension): self
    {
        $this->pension = $pension;

        return $this;
    }

    /**
     * @return Collection<int, InterventionFichesMonteursHeures>
     */
    function getHeures(): Collection
    {
        return $this->heures;
    }

    function addHeure(InterventionFichesMonteursHeures $heure): self
    {
        if (!$this->heures->contains($heure)) {
            $this->heures[] = $heure;
            $heure->setInterventionFicheMonteur($this);
        }

        return $this;
    }

    function removeHeure(InterventionFichesMonteursHeures $heure): self
    {
        if ($this->heures->removeElement($heure)) {
            // set the owning side to null (unless already changed)
            if ($heure->getInterventionFicheMonteur() === $this) {
                $heure->setInterventionFicheMonteur(null);
            }
        }

        return $this;
    }

    function getIntervention(): ?InterventionMonteurs
    {
        return $this->intervention;
    }

    function setIntervention(?InterventionMonteurs $intervention): self
    {
        $this->intervention = $intervention;

        return $this;
    }

    function getValidedAt(): ?\DateTimeInterface
    {
        return $this->validedAt;
    }

    function setValidedAt(?\DateTimeInterface $validedAt) : self
    {
        $this->validedAt = $validedAt;

        return $this;
    }

    function getValidedBy(): ?Users
    {
        return $this->validedBy;
    }

    function setValidedBy(?Users $validedBy): self
    {
        $this->validedBy = $validedBy;

        return $this;
    }

    function getLockedAt(): ?\DateTimeInterface
    {
        return $this->lockedAt;
    }

    function setLockedAt(?\DateTimeInterface $lockedAt) : self
    {
        $this->lockedAt = $lockedAt;

        return $this;
    }

    function getLockedBy(): ?Users
    {
        return $this->lockedBy;
    }

    function setLockedBy(?Users $lockedBy): self
    {
        $this->lockedBy = $lockedBy;

        return $this;
    }

    function getComment(): ?string
    {
        return $this->comment;
    }

    function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    function getHere(): ?bool
    {
        return $this->here;
    }

    function setHere(bool $here): self
    {
        $this->here = $here;

        return $this;
    }

    function isHere(): ?bool
    {
        return $this->here;
    }
}
