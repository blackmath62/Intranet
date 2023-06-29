<?php

namespace App\Entity\Main;

use App\Repository\Main\AffairesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AffairesRepository::class)
 */
class Affaires
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
    private $code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $libelle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tiers;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $progress;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $start;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $end;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $textColor;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $backgroundColor;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $etat;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $duration;

    /**
     * @ORM\OneToMany(targetEntity=InterventionMonteurs::class, mappedBy="code")
     */
    private $interventionMonteurs;

    public function __construct()
    {
        $this->interventionMonteurs = new ArrayCollection();
    }

    public function getId():  ? int
    {
        return $this->id;
    }

    public function getCode() :  ? string
    {
        return $this->code;
    }

    public function setCode(string $code) : self
    {
        $this->code = $code;

        return $this;
    }

    public function getLibelle():  ? string
    {
        return $this->libelle;
    }

    public function setLibelle( ? string $libelle) : self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getTiers() :  ? string
    {
        return $this->tiers;
    }

    public function setTiers(string $tiers) : self
    {
        $this->tiers = $tiers;

        return $this;
    }

    public function getNom():  ? string
    {
        return $this->nom;
    }

    public function setNom(string $nom) : self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getProgress():  ? int
    {
        return $this->progress;
    }

    public function setProgress( ? int $progress) : self
    {
        $this->progress = $progress;

        return $this;
    }

    public function getStart() :  ? \DateTimeInterface
    {
        return $this->start;
    }

    public function setStart( ? \DateTimeInterface $start) : self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd() :  ? \DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd( ? \DateTimeInterface $end) : self
    {
        $this->end = $end;

        return $this;
    }

    public function getTextColor() :  ? string
    {
        return $this->textColor;
    }

    public function setTextColor( ? string $textColor) : self
    {
        $this->textColor = $textColor;

        return $this;
    }

    public function getBackgroundColor() :  ? string
    {
        return $this->backgroundColor;
    }

    public function setBackgroundColor( ? string $backgroundColor) : self
    {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }

    public function getEtat() :  ? string
    {
        return $this->etat;
    }

    public function setEtat(string $etat) : self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getDuration():  ? string
    {
        return $this->duration;
    }

    public function setDuration( ? string $duration) : self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return Collection<int, InterventionMonteurs>
     */
    public function getInterventionMonteurs() : Collection
    {
        return $this->interventionMonteurs;
    }

    public function addInterventionMonteur(InterventionMonteurs $interventionMonteur): self
    {
        if (!$this->interventionMonteurs->contains($interventionMonteur)) {
            $this->interventionMonteurs[] = $interventionMonteur;
            $interventionMonteur->setCode($this);
        }

        return $this;
    }

    public function removeInterventionMonteur(InterventionMonteurs $interventionMonteur): self
    {
        if ($this->interventionMonteurs->removeElement($interventionMonteur)) {
            // set the owning side to null (unless already changed)
            if ($interventionMonteur->getCode() === $this) {
                $interventionMonteur->setCode(null);
            }
        }

        return $this;
    }
}
