<?php

namespace App\Entity\Main;

use App\Entity\Main\Logiciel;
use App\Entity\Main\SectionSearch;
use App\Entity\Main\Users;
use App\Repository\Main\FAQRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FAQRepository::class)]
class FAQ
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $title;

    #[ORM\Column(type: "text")]
    private $content;

    #[ORM\Column(type: "datetime")]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "faqs")]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\ManyToOne(targetEntity: Logiciel::class, inversedBy: "faqs")]
    #[ORM\JoinColumn(nullable: false)]
    private $logiciel;

    #[ORM\ManyToOne(targetEntity: SectionSearch::class, inversedBy: "faqs")]
    #[ORM\JoinColumn(nullable: false)]
    private $search;

    public function getLogiciel(): ?Logiciel
    {
        return $this->logiciel;
    }

    public function setLogiciel(?Logiciel $logiciel): self
    {
        $this->logiciel = $logiciel;

        return $this;
    }

    public function getSearch(): ?SectionSearch
    {
        return $this->search;
    }

    public function setSearch(?SectionSearch $search): self
    {
        $this->search = $search;

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

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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
}
