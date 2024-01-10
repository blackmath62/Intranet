<?php

namespace App\Entity\Main;

use App\Repository\Main\AffairePieceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AffairePieceRepository::class)]

class AffairePiece
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]

    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $adresse;

    #[ORM\Column(type: "string", length: 2)]
    private $op;

    #[ORM\Column(type: "string", length: 5, nullable: true)]
    private $transport;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $etat;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $affaire;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $closedAt;

    #[ORM\Column(nullable: true)]
    private ?int $cdno = null;

    #[ORM\Column(nullable: true)]
    private ?int $blno = null;

    #[ORM\ManyToMany(targetEntity: InterventionMonteurs::class, mappedBy: 'pieces')]
    private Collection $interventionMonteursPieces;

    public function __construct()
    {
        $this->interventionMonteursPieces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getOp(): ?string
    {
        return $this->op;
    }

    public function setOp(string $op): self
    {
        $this->op = $op;

        return $this;
    }

    public function getTransport(): ?string
    {
        return $this->transport;
    }

    public function setTransport(?string $transport): self
    {
        $this->transport = $transport;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getAffaire(): ?string
    {
        return $this->affaire;
    }

    public function setAffaire(?string $affaire): self
    {
        $this->affaire = $affaire;

        return $this;
    }

    public function getClosedAt(): ?\DateTimeInterface
    {
        return $this->closedAt;
    }

    public function setClosedAt(?\DateTimeInterface $closedAt) : self
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    public function getCdno(): ?int
    {
        return $this->cdno;
    }

    public function setCdno(?int $cdno): static
    {
        $this->cdno = $cdno;

        return $this;
    }

    public function getBlno(): ?int
    {
        return $this->blno;
    }

    public function setBlno(?int $blno): static
    {
        $this->blno = $blno;

        return $this;
    }

    /**
     * @return Collection<int, InterventionMonteurs>
     */
    public function getInterventionMonteursPieces(): Collection
    {
        return $this->interventionMonteursPieces;
    }

    public function addInterventionMonteursPiece(InterventionMonteurs $interventionMonteursPiece): static
    {
        if (!$this->interventionMonteursPieces->contains($interventionMonteursPiece)) {
            $this->interventionMonteursPieces->add($interventionMonteursPiece);
            $interventionMonteursPiece->addPiece($this);
        }

        return $this;
    }

    public function removeInterventionMonteursPiece(InterventionMonteurs $interventionMonteursPiece): static
    {
        if ($this->interventionMonteursPieces->removeElement($interventionMonteursPiece)) {
            $interventionMonteursPiece->removePiece($this);
        }

        return $this;
    }
}
