<?php

namespace App\Entity\Main;

use App\Entity\Main\Societe;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Main\AnnuaireRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=AnnuaireRepository::class)
 * @ORM\Entity
 * @UniqueEntity("interne",
 *     message="Ce numéro est déjà utilisé.")
 * @UniqueEntity("nom",
 *     message="Ce nom est déjà utilisé.")
 */
class Annuaire
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $interne;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $exterieur;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fonction;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $portable;

    /**
     * @ORM\ManyToOne(targetEntity=Societe::class, inversedBy="annuaires")
     * @ORM\JoinColumn(nullable=false)
     */
    private $societe;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInterne(): ?int
    {
        return $this->interne;
    }

    public function setInterne(?int $interne): self
    {
        $this->interne = $interne;

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

    public function getExterieur(): ?string
    {
        return $this->exterieur;
    }

    public function setExterieur(?string $exterieur): self
    {
        $this->exterieur = $exterieur;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(?string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(?string $fonction): self
    {
        $this->fonction = $fonction;

        return $this;
    }

    public function getPortable(): ?string
    {
        return $this->portable;
    }

    public function setPortable(?string $portable): self
    {
        $this->portable = $portable;

        return $this;
    }

    public function getSociete(): ?Societe
    {
        return $this->societe;
    }

    public function setSociete(?Societe $societe): self
    {
        $this->societe = $societe;

        return $this;
    }
}
