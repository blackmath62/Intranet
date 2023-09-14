<?php

namespace App\Entity\Main;

use App\Repository\Main\CmdRobyDelaiAccepteReporteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CmdRobyDelaiAccepteReporteRepository::class)
 */
class CmdRobyDelaiAccepteReporte
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
    private $identification;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $statut;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $modifiedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="cmdRobyDelaiAccepteReportesModifiedBy")
     */
    private $modifiedBy;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tiers;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Nom;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCmd;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $notreRef;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $delaiAccepte;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $delaiReporte;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cmd;

    /**
     * @ORM\OneToMany(targetEntity=Note::class, mappedBy="cmdRobyDelaiAccepteReporte")
     */
    private $note;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ht;

    public function __construct()
    {
        $this->note = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentification(): ?string
    {
        return $this->identification;
    }

    public function setIdentification(string $identification): self
    {
        $this->identification = $identification;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt) : self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getModifiedAt(): ?\DateTimeInterface
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(?\DateTimeInterface $modifiedAt) : self
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    public function getModifiedBy(): ?Users
    {
        return $this->modifiedBy;
    }

    public function setModifiedBy(?Users $modifiedBy): self
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
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

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): self
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getDateCmd(): ?\DateTimeInterface
    {
        return $this->dateCmd;
    }

    public function setDateCmd(\DateTimeInterface $dateCmd) : self
    {
        $this->dateCmd = $dateCmd;

        return $this;
    }

    public function getNotreRef(): ?string
    {
        return $this->notreRef;
    }

    public function setNotreRef(?string $notreRef): self
    {
        $this->notreRef = $notreRef;

        return $this;
    }

    public function getDelaiAccepte(): ?\DateTimeInterface
    {
        return $this->delaiAccepte;
    }

    public function setDelaiAccepte(?\DateTimeInterface $delaiAccepte) : self
    {
        $this->delaiAccepte = $delaiAccepte;

        return $this;
    }

    public function getDelaiReporte(): ?\DateTimeInterface
    {
        return $this->delaiReporte;
    }

    public function setDelaiReporte(?\DateTimeInterface $delaiReporte) : self
    {
        $this->delaiReporte = $delaiReporte;

        return $this;
    }

    public function getCmd(): ?string
    {
        return $this->cmd;
    }

    public function setCmd(string $cmd): self
    {
        $this->cmd = $cmd;

        return $this;
    }

    /**
     * @return Collection|Note[]
     */
    public function getNote(): Collection
    {
        return $this->note;
    }

    public function addNote(Note $note): self
    {
        if (!$this->note->contains($note)) {
            $this->note[] = $note;
            $note->setCmdRobyDelaiAccepteReporte($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->note->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getCmdRobyDelaiAccepteReporte() === $this) {
                $note->setCmdRobyDelaiAccepteReporte(null);
            }
        }

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getHt(): ?string
    {
        return $this->ht;
    }

    public function setHt(?string $ht): self
    {
        $this->ht = $ht;

        return $this;
    }

}
