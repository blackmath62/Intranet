<?php

namespace App\Entity\Main;

use App\Repository\Main\AffairePieceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AffairePieceRepository::class)
 */
class AffairePiece
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $entId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresse;

    /**
     * @ORM\Column(type="integer")
     */
    private $typePiece;

    /**
     * @ORM\Column(type="integer")
     */
    private $piece;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $op;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $transport;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $etat;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $affaire;

    /**
     * @ORM\ManyToMany(targetEntity=InterventionMonteurs::class, mappedBy="pieces")
     */
    private $interventionMonteurs;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $closedAt;

    public function __construct()
    {
        $this->interventionMonteurs = new ArrayCollection();
    }

    public function getId():  ? int
    {
        return $this->id;
    }

    public function getEntId() :  ? int
    {
        return $this->entId;
    }

    public function setEntId(int $entId) : self
    {
        $this->entId = $entId;

        return $this;
    }

    public function getAdresse():  ? string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse) : self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getTypePiece():  ? int
    {
        return $this->typePiece;
    }

    public function setTypePiece(int $typePiece) : self
    {
        $this->typePiece = $typePiece;

        return $this;
    }

    public function getPiece():  ? int
    {
        return $this->piece;
    }

    public function setPiece(int $piece) : self
    {
        $this->piece = $piece;

        return $this;
    }

    public function getOp():  ? string
    {
        return $this->op;
    }

    public function setOp(string $op) : self
    {
        $this->op = $op;

        return $this;
    }

    public function getTransport():  ? string
    {
        return $this->transport;
    }

    public function setTransport( ? string $transport) : self
    {
        $this->transport = $transport;

        return $this;
    }

    public function getEtat() :  ? string
    {
        return $this->etat;
    }

    public function setEtat( ? string $etat) : self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getAffaire() :  ? string
    {
        return $this->affaire;
    }

    public function setAffaire( ? string $affaire) : self
    {
        $this->affaire = $affaire;

        return $this;
    }

    /**
     * @return Collection<int, InterventionMonteurs>
     */
    public function getInterventionMonteurs(): Collection
    {
        return $this->interventionMonteurs;
    }

    public function addInterventionMonteur(InterventionMonteurs $interventionMonteur): self
    {
        if (!$this->interventionMonteurs->contains($interventionMonteur)) {
            $this->interventionMonteurs[] = $interventionMonteur;
            $interventionMonteur->addPiece($this);
        }

        return $this;
    }

    public function removeInterventionMonteur(InterventionMonteurs $interventionMonteur): self
    {
        if ($this->interventionMonteurs->removeElement($interventionMonteur)) {
            $interventionMonteur->removePiece($this);
        }

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
}
