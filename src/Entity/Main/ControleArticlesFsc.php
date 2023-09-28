<?php

namespace App\Entity\Main;

use App\Repository\Main\ControleArticlesFscRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ControleArticlesFscRepository::class)]
class ControleArticlesFsc
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "datetime")]
    private $createdAt;

    #[ORM\Column(type: "datetime")]
    private $UpdatedAt;

    #[ORM\Column(type: "string", length: 255)]
    private $products;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "controleArticlesFscs")]
    private $controledBy;

    #[ORM\Column(type: "boolean")]
    private $status;

    #[ORM\Column(type: "string", length: 255)]
    private $LastOrder;

    #[ORM\Column(type: "datetime")]
    private $LastOrderAt;

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->UpdatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $UpdatedAt) : self
    {
        $this->UpdatedAt = $UpdatedAt;

        return $this;
    }

    public function getProducts(): ?string
    {
        return $this->products;
    }

    public function setProducts(string $products): self
    {
        $this->products = $products;

        return $this;
    }

    public function getControledBy(): ?Users
    {
        return $this->controledBy;
    }

    public function setControledBy(?Users $controledBy): self
    {
        $this->controledBy = $controledBy;

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

    public function getLastOrder(): ?string
    {
        return $this->LastOrder;
    }

    public function setLastOrder(string $LastOrder): self
    {
        $this->LastOrder = $LastOrder;

        return $this;
    }

    public function getLastOrderAt(): ?\DateTimeInterface
    {
        return $this->LastOrderAt;
    }

    public function setLastOrderAt(\DateTimeInterface $LastOrderAt) : self
    {
        $this->LastOrderAt = $LastOrderAt;

        return $this;
    }
}
