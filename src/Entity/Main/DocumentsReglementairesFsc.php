<?php

namespace App\Entity\Main;

use App\Entity\Main\Users;
use App\Repository\Main\DocumentsReglementairesFscRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentsReglementairesFscRepository::class)]

class DocumentsReglementairesFsc
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]

    private $id;

    #[ORM\Column(type: "datetime")]
    private $createdAt;

    #[ORM\Column(type: "string", length: 255)]
    private $files;

    #[ORM\Column(type: "string", length: 255)]
    private $years;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "addBys")]
    private $addBy;

    #[ORM\Column(type: "string", length: 255)]
    private $type;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFiles(): ?string
    {
        return $this->files;
    }

    public function setFiles(string $files): self
    {
        $this->files = $files;

        return $this;
    }

    public function getYears(): ?string
    {
        return $this->years;
    }

    public function setYears(string $years): self
    {
        $this->years = $years;

        return $this;
    }

    public function getAddBy(): ?Users
    {
        return $this->addBy;
    }

    public function setAddBy(?Users $addBy): self
    {
        $this->addBy = $addBy;

        return $this;
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

    public function getUser(): ?Users
    {
        return $this->addBy;
    }

    public function setUser(?Users $user): self
    {
        $this->addBy = $user;

        return $this;
    }
}
