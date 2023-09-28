<?php

namespace App\Entity\Main;

use App\Entity\Main\FAQ;
use App\Repository\Main\SectionSearchRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: SectionSearchRepository::class)]
#[UniqueEntity("nom")]
class SectionSearch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $nom;

    #[ORM\Column(type: "datetime")]
    private $createdAt;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $closedAt;

    #[ORM\Column(type: "string", length: 255)]
    private $textColor;

    #[ORM\Column(type: "string", length: 255)]
    private $backgroundColor;

    #[ORM\Column(type: "string", length: 255)]
    private $fa;

    #[ORM\OneToMany(targetEntity: FAQ::class, mappedBy: "search")]
    private $faqs;

    public function __construct()
    {
        $this->faqs = new ArrayCollection();
    }

    /**
     * @return Collection|FAQ[]
     */
    public function getfaqs(): Collection
    {
        return $this->tickets;
    }

    public function addfaq(FAQ $faq): self
    {
        if (!$this->faqs->contains($faq)) {
            $this->faqs[] = $faq;
            $faq->setSearch($this);
        }

        return $this;
    }

    public function removeFaq(FAQ $faq): self
    {
        if ($this->faqs->contains($faq)) {
            $this->faqs->removeElement($faq);
            // set the owning side to null (unless already changed)
            if ($faq->getSearch() === $this) {
                $faq->setSearch(null);
            }
        }

        return $this;
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

    public function setCreatedAt(\DateTimeInterface $createdAt) : self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getClosedAt(): ?\DateTimeInterface
    {
        return $this->closedAt;
    }

    public function setClosedAt(?\DateTimeInterface $closedAt) : self
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    public function getTextColor(): ?string
    {
        return $this->textColor;
    }

    public function setTextColor(string $textColor): self
    {
        $this->textColor = $textColor;

        return $this;
    }

    public function getBackgroundColor(): ?string
    {
        return $this->backgroundColor;
    }

    public function setBackgroundColor(string $backgroundColor): self
    {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }

    public function getFa(): ?string
    {
        return $this->fa;
    }

    public function setFa(string $fa): self
    {
        $this->fa = $fa;

        return $this;
    }
}
