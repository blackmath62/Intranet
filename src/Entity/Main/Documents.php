<?php

namespace App\Entity\Main;

use App\Repository\Main\DocumentsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentsRepository::class)]
class Documents
{
    #[ORM\Id()]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: "integer")]

    private $id;

    #[ORM\ManyToOne(targetEntity: Societe::class, inversedBy: "documents")]
    private $societe;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "documents")]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: "datetime")]
    private $createdAt;

    #[ORM\Column(type: "string", length: 255)]
    private $title;

    #[ORM\Column(type: "string", length: 255)]
    private $url;

    #[ORM\Column(type: "datetime")]
    private $beginningDate;

    #[ORM\Column(type: "datetime")]
    private $endDate;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getBeginningDate(): ?\DateTimeInterface
    {
        return $this->beginningDate;
    }

    public function setBeginningDate(\DateTimeInterface $beginningDate) : self
    {
        $this->beginningDate = $beginningDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate) : self
    {
        $this->endDate = $endDate;

        return $this;
    }
}
