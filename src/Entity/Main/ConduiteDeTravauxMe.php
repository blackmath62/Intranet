<?php

namespace App\Entity\Main;

use App\Repository\Main\ConduiteDeTravauxMeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConduiteDeTravauxMeRepository::class)
 */
class ConduiteDeTravauxMe
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
    private $codeClient;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresseLivraison;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $affaire;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $modeDeTransport;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $op;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCmd;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numCmd;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateBl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $numeroBl;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateFacture;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $numeroFacture;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $delaiDemande;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $delaiAccepte;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $delaiReporte;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateDebutChantier;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateFinChantier;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $etat;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dureeTravaux;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="conduiteDeTravauxMes")
     */
    private $updatedBy;

    /**
     * @ORM\Column(type="integer")
     */
    private $entId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeClient(): ?string
    {
        return $this->codeClient;
    }

    public function setCodeClient(string $codeClient): self
    {
        $this->codeClient = $codeClient;

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

    public function getAdresseLivraison(): ?string
    {
        return $this->adresseLivraison;
    }

    public function setAdresseLivraison(string $adresseLivraison): self
    {
        $this->adresseLivraison = $adresseLivraison;

        return $this;
    }

    public function getAffaire(): ?string
    {
        return $this->affaire;
    }

    public function setAffaire(string $affaire): self
    {
        $this->affaire = $affaire;

        return $this;
    }

    public function getModeDeTransport(): ?string
    {
        return $this->modeDeTransport;
    }

    public function setModeDeTransport(string $modeDeTransport): self
    {
        $this->modeDeTransport = $modeDeTransport;

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

    public function getDateCmd(): ?\DateTimeInterface
    {
        return $this->dateCmd;
    }

    public function setDateCmd(\DateTimeInterface $dateCmd): self
    {
        $this->dateCmd = $dateCmd;

        return $this;
    }

    public function getNumCmd(): ?string
    {
        return $this->numCmd;
    }

    public function setNumCmd(string $numCmd): self
    {
        $this->numCmd = $numCmd;

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

    public function getNumeroBl(): ?string
    {
        return $this->numeroBl;
    }

    public function setNumeroBl(?string $numeroBl): self
    {
        $this->numeroBl = $numeroBl;

        return $this;
    }

    public function getDateFacture(): ?\DateTimeInterface
    {
        return $this->dateFacture;
    }

    public function setDateFacture(?\DateTimeInterface $dateFacture): self
    {
        $this->dateFacture = $dateFacture;

        return $this;
    }

    public function getNumeroFacture(): ?string
    {
        return $this->numeroFacture;
    }

    public function setNumeroFacture(?string $numeroFacture): self
    {
        $this->numeroFacture = $numeroFacture;

        return $this;
    }

    public function getDelaiDemande(): ?\DateTimeInterface
    {
        return $this->delaiDemande;
    }

    public function setDelaiDemande(?\DateTimeInterface $delaiDemande): self
    {
        $this->delaiDemande = $delaiDemande;

        return $this;
    }

    public function getDelaiAccepte(): ?\DateTimeInterface
    {
        return $this->delaiAccepte;
    }

    public function setDelaiAccepte(?\DateTimeInterface $delaiAccepte): self
    {
        $this->delaiAccepte = $delaiAccepte;

        return $this;
    }

    public function getDelaiReporte(): ?\DateTimeInterface
    {
        return $this->delaiReporte;
    }

    public function setDelaiReporte(?\DateTimeInterface $delaiReporte): self
    {
        $this->delaiReporte = $delaiReporte;

        return $this;
    }

    public function getDateDebutChantier(): ?\DateTimeInterface
    {
        return $this->dateDebutChantier;
    }

    public function setDateDebutChantier(?\DateTimeInterface $dateDebutChantier): self
    {
        $this->dateDebutChantier = $dateDebutChantier;

        return $this;
    }

    public function getDateFinChantier(): ?\DateTimeInterface
    {
        return $this->dateFinChantier;
    }

    public function setDateFinChantier(?\DateTimeInterface $dateFinChantier): self
    {
        $this->dateFinChantier = $dateFinChantier;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getDureeTravaux(): ?string
    {
        return $this->dureeTravaux;
    }

    public function setDureeTravaux(?string $dureeTravaux): self
    {
        $this->dureeTravaux = $dureeTravaux;

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

    public function getUpdatedBy(): ?Users
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?Users $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getEntId(): ?int
    {
        return $this->entId;
    }

    public function setEntId(int $entId): self
    {
        $this->entId = $entId;

        return $this;
    }
}
