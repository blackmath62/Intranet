<?php

namespace App\Entity\Main;

use App\Repository\Main\fscListMovementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=fscListMovementRepository::class)
 */
class fscListMovement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $utilisateur;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numCmd;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateCmd;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numBl;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateBl;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numFact;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateFact;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tiers;

    /**
     * @ORM\Column(type="integer")
     */
    private $codePiece;

    /**
     * @ORM\OneToMany(targetEntity=documentsFsc::class, mappedBy="fscListMovement",cascade={"persist"})
     */
    private $file;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $notreRef;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Probleme;

    public function __construct()
    {
        $this->file = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?string
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?string $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getNumCmd(): ?int
    {
        return $this->numCmd;
    }

    public function setNumCmd(?int $numCmd): self
    {
        $this->numCmd = $numCmd;

        return $this;
    }

    public function getDateCmd(): ?\DateTimeInterface
    {
        return $this->dateCmd;
    }

    public function setDateCmd(?\DateTimeInterface $dateCmd): self
    {
        $this->dateCmd = $dateCmd;

        return $this;
    }

    public function getNumBl(): ?int
    {
        return $this->numBl;
    }

    public function setNumBl(?int $numBl): self
    {
        $this->numBl = $numBl;

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

    public function getNumFact(): ?int
    {
        return $this->numFact;
    }

    public function setNumFact(?int $numFact): self
    {
        $this->numFact = $numFact;

        return $this;
    }

    public function getDateFact(): ?\DateTimeInterface
    {
        return $this->dateFact;
    }

    public function setDateFact(?\DateTimeInterface $dateFact): self
    {
        $this->dateFact = $dateFact;

        return $this;
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

    public function getTiers(): ?string
    {
        return $this->tiers;
    }

    public function setTiers(string $tiers): self
    {
        $this->tiers = $tiers;

        return $this;
    }

    public function getCodePiece(): ?int
    {
        return $this->codePiece;
    }

    public function setCodePiece(int $codePiece): self
    {
        $this->codePiece = $codePiece;

        return $this;
    }

    /**
     * @return Collection|documentsFsc[]
     */
    public function getFile(): Collection
    {
        return $this->file;
    }

    public function addFile(documentsFsc $file): self
    {
        if (!$this->file->contains($file)) {
            $this->file[] = $file;
            $file->setFscListMovement($this);
        }

        return $this;
    }

    public function removeFile(documentsFsc $file): self
    {
        if ($this->file->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getFscListMovement() === $this) {
                $file->setFscListMovement(null);
            }
        }

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

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getProbleme(): ?bool
    {
        return $this->Probleme;
    }

    public function setProbleme(bool $Probleme): self
    {
        $this->Probleme = $Probleme;

        return $this;
    }
}
