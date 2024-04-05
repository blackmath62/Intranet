<?php

namespace App\Entity\Main;

use App\Repository\Main\InterventionMonteursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InterventionMonteursRepository::class)]
class InterventionMonteurs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "interventionMonteursUserCr")]
    private $UserCr;

    #[ORM\ManyToMany(targetEntity: Users::class, inversedBy: "interventionMonteursEquipes")]
    private $Equipes;

    #[ORM\Column(type: "datetime")]
    private $start;

    #[ORM\Column(type: "datetime")]
    private $end;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $adresse;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $backgroundColor;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $textColor;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: Affaires::class, inversedBy: "interventionMonteurs")]
    private $code;

    #[ORM\OneToMany(targetEntity: SignatureElectronique::class, mappedBy: "intervention")]
    private $signatureElectroniques;

    #[ORM\OneToMany(targetEntity: InterventionFicheMonteur::class, mappedBy: "intervention")]
    private $interventionFicheMonteurs;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "interventionMonteursLockedBy")]
    private $lockedBy;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $lockedAt;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $sendAt;

    private $champTemporaire;

    #[ORM\ManyToOne(inversedBy: 'statutInverventionMonteurs')]
    private ?StatutsGeneraux $typeIntervention = null;

    #[ORM\ManyToMany(targetEntity: AffairePiece::class, inversedBy: 'interventionMonteursPieces')]
    private Collection $pieces;

    #[ORM\Column]
    private ?bool $allDay = null;

    public function __construct()
    {
        $this->Equipes = new ArrayCollection();
        $this->signatureElectroniques = new ArrayCollection();
        $this->interventionFicheMonteurs = new ArrayCollection();
        $this->pieces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserCr(): ?Users
    {
        return $this->UserCr;
    }

    public function setUserCr(?Users $UserCr): self
    {
        $this->UserCr = $UserCr;

        return $this;
    }

    /**
     * @return Collection<int, Users>
     */
    public function getEquipes(): Collection
    {
        return $this->Equipes;
    }

    public function addEquipe(Users $equipe): self
    {
        if (!$this->Equipes->contains($equipe)) {
            $this->Equipes[] = $equipe;
        }

        return $this;
    }

    public function removeEquipe(Users $equipe): self
    {
        $this->Equipes->removeElement($equipe);

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start) : self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end) : self
    {
        $this->end = $end;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getBackgroundColor(): ?string
    {
        return $this->backgroundColor;
    }

    public function setBackgroundColor(?string $backgroundColor): self
    {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }

    public function getTextColor(): ?string
    {
        return $this->textColor;
    }

    public function setTextColor(?string $textColor): self
    {
        $this->textColor = $textColor;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt) : self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCode(): ?Affaires
    {
        return $this->code;
    }

    public function setCode(?Affaires $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection<int, SignatureElectronique>
     */
    public function getSignatureElectroniques(): Collection
    {
        return $this->signatureElectroniques;
    }

    public function addSignatureElectronique(SignatureElectronique $signatureElectronique): self
    {
        if (!$this->signatureElectroniques->contains($signatureElectronique)) {
            $this->signatureElectroniques[] = $signatureElectronique;
            $signatureElectronique->setIntervention($this);
        }

        return $this;
    }

    public function removeSignatureElectronique(SignatureElectronique $signatureElectronique): self
    {
        if ($this->signatureElectroniques->removeElement($signatureElectronique)) {
            // set the owning side to null (unless already changed)
            if ($signatureElectronique->getIntervention() === $this) {
                $signatureElectronique->setIntervention(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, InterventionFicheMonteur>
     */
    public function getInterventionFicheMonteurs(): Collection
    {
        return $this->interventionFicheMonteurs;
    }

    public function addInterventionFicheMonteur(InterventionFicheMonteur $interventionFicheMonteur): self
    {
        if (!$this->interventionFicheMonteurs->contains($interventionFicheMonteur)) {
            $this->interventionFicheMonteurs[] = $interventionFicheMonteur;
            $interventionFicheMonteur->setIntervention($this);
        }

        return $this;
    }

    public function removeInterventionFicheMonteur(InterventionFicheMonteur $interventionFicheMonteur): self
    {
        if ($this->interventionFicheMonteurs->removeElement($interventionFicheMonteur)) {
            // set the owning side to null (unless already changed)
            if ($interventionFicheMonteur->getIntervention() === $this) {
                $interventionFicheMonteur->setIntervention(null);
            }
        }

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

    public function getLockedAt(): ?\DateTimeInterface
    {
        return $this->lockedAt;
    }

    public function setLockedAt(?\DateTimeInterface $lockedAt) : self
    {
        $this->lockedAt = $lockedAt;

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

    public function getChampTemporaire()
    {
        return $this->champTemporaire;
    }

    public function setChampTemporaire($champTemporaire)
    {
        $this->champTemporaire = $champTemporaire;
    }

    public function getTypeIntervention(): ?StatutsGeneraux
    {
        return $this->typeIntervention;
    }

    public function setTypeIntervention(?StatutsGeneraux $typeIntervention): static
    {
        $this->typeIntervention = $typeIntervention;

        return $this;
    }

    /**
     * @return Collection<int, AffairePiece>
     */
    public function getPieces(): Collection
    {
        return $this->pieces;
    }

    public function addPiece(AffairePiece $piece): static
    {
        if (!$this->pieces->contains($piece)) {
            $this->pieces->add($piece);
        }

        return $this;
    }

    public function removePiece(AffairePiece $piece): static
    {
        $this->pieces->removeElement($piece);

        return $this;
    }

    public function isAllDay(): ?bool
    {
        return $this->allDay;
    }

    public function setAllDay(bool $allDay): static
    {
        $this->allDay = $allDay;

        return $this;
    }
}
