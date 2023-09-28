<?php

namespace App\Entity\Main;

use App\Repository\Main\ListCmdTraiteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ListCmdTraiteRepository::class)]
class ListCmdTraite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $numero;

    #[ORM\Column(type: "datetime")]
    private $createdAt;

    #[ORM\Column(type: "string", length: 255)]
    private $dossier;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "listCmdTraites")]
    private $treatedBy;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

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

    public function getDossier(): ?string
    {
        return $this->dossier;
    }

    public function setDossier(string $dossier): self
    {
        $this->dossier = $dossier;

        return $this;
    }

    public function getTreatedBy(): ?Users
    {
        return $this->treatedBy;
    }

    public function setTreatedBy(?Users $treatedBy): self
    {
        $this->treatedBy = $treatedBy;

        return $this;
    }
}
