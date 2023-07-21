<?php

namespace App\Entity\Main;

use App\Repository\Main\InterventionFicheMonteurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InterventionFicheMonteurRepository::class)
 */
class InterventionFicheMonteur
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
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="interventionFicheMonteurs")
     */
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="interventionFicheMonteurs")
     */
    private $intervenant;

    /**
     * @ORM\OneToOne(targetEntity=Commentaires::class, cascade={"persist", "remove"})
     */
    private $commentaire;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $pension = [];

    /**
     * @ORM\OneToMany(targetEntity=InterventionFichesMonteursHeures::class, mappedBy="interventionFicheMonteur")
     */
    private $heures;

    /**
     * @ORM\ManyToOne(targetEntity=InterventionMonteurs::class, inversedBy="interventionFicheMonteurs")
     */
    private $intervention;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $validedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="InterventionFicheMonteursValidedBy")
     */
    private $validedBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lockedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="interventionFicheMonteursLockedBy")
     */
    private $lockedBy;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $comment;

    public function __construct()
    {
        $this->heures = new ArrayCollection();
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

    public function getCreatedBy(): ?Users
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?Users $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getIntervenant(): ?Users
    {
        return $this->intervenant;
    }

    public function setIntervenant(?Users $intervenant): self
    {
        $this->intervenant = $intervenant;

        return $this;
    }

    public function getCommentaire(): ?Commentaires
    {
        return $this->commentaire;
    }

    public function setCommentaire(?Commentaires $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getPension(): ?array
    {
        return $this->pension;
    }

    public function setPension(?array $pension): self
    {
        $this->pension = $pension;

        return $this;
    }

    /**
     * @return Collection<int, InterventionFichesMonteursHeures>
     */
    public function getHeures(): Collection
    {
        return $this->heures;
    }

    public function addHeure(InterventionFichesMonteursHeures $heure): self
    {
        if (!$this->heures->contains($heure)) {
            $this->heures[] = $heure;
            $heure->setInterventionFicheMonteur($this);
        }

        return $this;
    }

    public function removeHeure(InterventionFichesMonteursHeures $heure): self
    {
        if ($this->heures->removeElement($heure)) {
            // set the owning side to null (unless already changed)
            if ($heure->getInterventionFicheMonteur() === $this) {
                $heure->setInterventionFicheMonteur(null);
            }
        }

        return $this;
    }

    public function getIntervention(): ?InterventionMonteurs
    {
        return $this->intervention;
    }

    public function setIntervention(?InterventionMonteurs $intervention): self
    {
        $this->intervention = $intervention;

        return $this;
    }

    public function getValidedAt(): ?\DateTimeInterface
    {
        return $this->validedAt;
    }

    public function setValidedAt(?\DateTimeInterface $validedAt): self
    {
        $this->validedAt = $validedAt;

        return $this;
    }

    public function getValidedBy(): ?Users
    {
        return $this->validedBy;
    }

    public function setValidedBy(?Users $validedBy): self
    {
        $this->validedBy = $validedBy;

        return $this;
    }

    public function getLockedAt(): ?\DateTimeInterface
    {
        return $this->lockedAt;
    }

    public function setLockedAt(?\DateTimeInterface $lockedAt): self
    {
        $this->lockedAt = $lockedAt;

        return $this;
    }

    public function getLockedBy(): ?Users
    {
        return $this->lockedBy;
    }

    public function setLockedBy(?Users $lockedBy): self
    {
        $this->lockedBy = $lockedBy;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
