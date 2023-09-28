<?php

namespace App\Entity\Main;

use App\Repository\Main\CommentairesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentairesRepository::class)]
class Commentaires
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]

    private $id;

    #[ORM\Column(type: "datetime")]
    private $createdAt;

    #[ORM\Column(type: "text")]
    private $content;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "commentaires")]
    private $user;

    #[ORM\Column(type: "string", length: 255)]
    private $Tables;

    #[ORM\Column(type: "integer")]
    private $identifiant;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    public function getTables(): ?string
    {
        return $this->Tables;
    }

    public function setTables(string $Tables): self
    {
        $this->Tables = $Tables;

        return $this;
    }

    public function getIdentifiant(): ?int
    {
        return $this->identifiant;
    }

    public function setIdentifiant(int $identifiant): self
    {
        $this->identifiant = $identifiant;

        return $this;
    }
}
