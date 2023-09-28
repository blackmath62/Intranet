<?php

namespace App\Entity\Main;

use App\Repository\Main\fscListMovementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: fscListMovementRepository::class)]
class fscListMovement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $updatedAt;

    #[ORM\Column(type: "string", length: 255)]
    private $utilisateur;

    #[ORM\Column(type: "integer", nullable: true)]
    private $numCmd;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $dateCmd;

    #[ORM\Column(type: "integer", nullable: true)]
    private $numBl;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $dateBl;

    #[ORM\Column(type: "integer", nullable: true)]
    private $numFact;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $dateFact;

    #[ORM\Column(type: "datetime")]
    private $createdAt;

    #[ORM\Column(type: "string", length: 255)]
    private $tiers;

    #[ORM\Column(type: "integer")]
    private $codePiece;

    #[ORM\OneToMany(targetEntity: documentsFsc::class, mappedBy: "fscListMovement", cascade: ["persist"])]
    private $file;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $notreRef;

    #[ORM\Column(type: "boolean")]
    private $status;

    #[ORM\Column(type: "boolean")]
    private $Probleme;

    #[ORM\ManyToMany(targetEntity: MovBillFsc::class, mappedBy: "ventilations")]
    private $movBillFscs;

    #[ORM\Column(type: "string", length: 255)]
    private $perimetreBois;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "updatePerimetreBoisFsc")]
    private $userChangePerimetreBoisFsc;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $updatePerimetreBoisFsc;

    function __construct()
    {
        $this->file = new ArrayCollection();
        $this->movBillFscs = new ArrayCollection();
    }

    function getId(): ?int
    {
        return $this->id;
    }

    function getUtilisateur(): ?string
    {
        return $this->utilisateur;
    }

    function setUtilisateur(?string $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    function setUpdatedAt(?\DateTimeInterface $updatedAt) : self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    function getNumCmd(): ?int
    {
        return $this->numCmd;
    }

    function setNumCmd(?int $numCmd): self
    {
        $this->numCmd = $numCmd;

        return $this;
    }

    function getDateCmd(): ?\DateTimeInterface
    {
        return $this->dateCmd;
    }

    function setDateCmd(?\DateTimeInterface $dateCmd) : self
    {
        $this->dateCmd = $dateCmd;

        return $this;
    }

    function getNumBl(): ?int
    {
        return $this->numBl;
    }

    function setNumBl(?int $numBl): self
    {
        $this->numBl = $numBl;

        return $this;
    }

    function getDateBl(): ?\DateTimeInterface
    {
        return $this->dateBl;
    }

    function setDateBl(?\DateTimeInterface $dateBl) : self
    {
        $this->dateBl = $dateBl;

        return $this;
    }

    function getNumFact(): ?int
    {
        return $this->numFact;
    }

    function setNumFact(?int $numFact): self
    {
        $this->numFact = $numFact;

        return $this;
    }

    function getDateFact(): ?\DateTimeInterface
    {
        return $this->dateFact;
    }

    function setDateFact(?\DateTimeInterface $dateFact) : self
    {
        $this->dateFact = $dateFact;

        return $this;
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

    function getTiers(): ?string
    {
        return $this->tiers;
    }

    function setTiers(string $tiers): self
    {
        $this->tiers = $tiers;

        return $this;
    }

    function getCodePiece(): ?int
    {
        return $this->codePiece;
    }

    function setCodePiece(int $codePiece): self
    {
        $this->codePiece = $codePiece;

        return $this;
    }

    /**
     * @return Collection|documentsFsc[]
     */
    function getFile(): Collection
    {
        return $this->file;
    }

    function addFile(documentsFsc $file): self
    {
        if (!$this->file->contains($file)) {
            $this->file[] = $file;
            $file->setFscListMovement($this);
        }

        return $this;
    }

    function removeFile(documentsFsc $file): self
    {
        if ($this->file->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getFscListMovement() === $this) {
                $file->setFscListMovement(null);
            }
        }

        return $this;
    }

    function getNotreRef(): ?string
    {
        return $this->notreRef;
    }

    function setNotreRef(?string $notreRef): self
    {
        $this->notreRef = $notreRef;

        return $this;
    }

    function getStatus(): ?bool
    {
        return $this->status;
    }

    function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    function getProbleme(): ?bool
    {
        return $this->Probleme;
    }

    function setProbleme(bool $Probleme): self
    {
        $this->Probleme = $Probleme;

        return $this;
    }

    /**
     * @return Collection|MovBillFsc[]
     */
    function getMovBillFscs(): Collection
    {
        return $this->movBillFscs;
    }

    function addMovBillFsc(MovBillFsc $movBillFsc): self
    {
        if (!$this->movBillFscs->contains($movBillFsc)) {
            $this->movBillFscs[] = $movBillFsc;
            $movBillFsc->addVentilation($this);
        }

        return $this;
    }

    function removeMovBillFsc(MovBillFsc $movBillFsc): self
    {
        if ($this->movBillFscs->removeElement($movBillFsc)) {
            $movBillFsc->removeVentilation($this);
        }

        return $this;
    }

    function getPerimetreBois(): ?string
    {
        return $this->perimetreBois;
    }

    function setPerimetreBois(string $perimetreBois): self
    {
        $this->perimetreBois = $perimetreBois;

        return $this;
    }

    function getUserChangePerimetreBoisFsc(): ?Users
    {
        return $this->userChangePerimetreBoisFsc;
    }

    function setUserChangePerimetreBoisFsc(?Users $userChangePerimetreBoisFsc): self
    {
        $this->userChangePerimetreBoisFsc = $userChangePerimetreBoisFsc;

        return $this;
    }

    function getUpdatePerimetreBoisFsc(): ?\DateTimeInterface
    {
        return $this->updatePerimetreBoisFsc;
    }

    function setUpdatePerimetreBoisFsc(?\DateTimeInterface $updatePerimetreBoisFsc) : self
    {
        $this->updatePerimetreBoisFsc = $updatePerimetreBoisFsc;

        return $this;
    }
}
