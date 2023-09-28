<?php

namespace App\Entity\Main;

use App\Repository\Main\InterventionFichesMonteursHeuresRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InterventionFichesMonteursHeuresRepository::class)]
class InterventionFichesMonteursHeures
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $type;

    #[ORM\Column(type: "time")]
    private $start;

    #[ORM\Column(type: "time")]
    private $end;

    #[ORM\Column(type: "datetime")]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "interventionFichesMonteursHeures")]
    private $createdBy;

    #[ORM\ManyToOne(targetEntity: InterventionFicheMonteur::class, inversedBy: "heures")]
    private $interventionFicheMonteur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt) : self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedBy(): ?Users
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?Users $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getInterventionFicheMonteur(): ?InterventionFicheMonteur
    {
        return $this->interventionFicheMonteur;
    }

    public function setInterventionFicheMonteur(?InterventionFicheMonteur $interventionFicheMonteur): self
    {
        $this->interventionFicheMonteur = $interventionFicheMonteur;

        return $this;
    }
}
