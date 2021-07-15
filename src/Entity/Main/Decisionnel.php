<?php

namespace App\Entity\Main;

use App\Entity\Divalto\Art;
use App\Repository\Main\DecisionnelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DecisionnelRepository::class)
 */
class Decisionnel
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
    private $nom;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="decisionnels")
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity=CopyFou::class, inversedBy="decisionnels")
     */
    private $Fournisseurs;

    /**
     * @ORM\ManyToMany(targetEntity=Art::class, inversedBy="decisionnels")
     */
    private $articles;

    public function __construct()
    {
        $this->Fournisseurs = new ArrayCollection();
        $this->articles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

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

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|CopyFou[]
     */
    public function getFournisseurs(): Collection
    {
        return $this->Fournisseurs;
    }

    public function addFournisseur(CopyFou $fournisseur): self
    {
        if (!$this->Fournisseurs->contains($fournisseur)) {
            $this->Fournisseurs[] = $fournisseur;
        }

        return $this;
    }

    public function removeFournisseur(CopyFou $fournisseur): self
    {
        $this->Fournisseurs->removeElement($fournisseur);

        return $this;
    }

    /**
     * @return Collection|Art[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Art $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
        }

        return $this;
    }

    public function removeArticle(Art $article): self
    {
        $this->articles->removeElement($article);

        return $this;
    }
}
