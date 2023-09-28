<?php

namespace App\Entity\Main;

use App\Entity\Main\FAQ;
use App\Repository\Main\LogicielRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: LogicielRepository::class)]
#[UniqueEntity("nom")]
class Logiciel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $nom;

    #[ORM\Column(type: "datetime")]
    private $createdAt;

    #[ORM\Column(type: "string", length: 255)]
    private $textColor;

    #[ORM\Column(type: "string", length: 255)]
    private $backgroungColor;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $icon;

    #[ORM\Column(type: "datetime")]
    private $closedAt;

    #[ORM\OneToMany(targetEntity: FAQ::class, mappedBy: "logiciel")]
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
            $faq->setLogiciel($this);
        }

        return $this;
    }

    public function removeFaq(FAQ $faq): self
    {
        if ($this->faqs->contains($faq)) {
            $this->faqs->removeElement($faq);
            // set the owning side to null (unless already changed)
            if ($faq->getLogiciel() === $this) {
                $faq->setLogiciel(null);
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

    public function getTextColor(): ?string
    {
        return $this->textColor;
    }

    public function setTextColor(string $textColor): self
    {
        $this->textColor = $textColor;

        return $this;
    }

    public function getBackgroungColor(): ?string
    {
        return $this->backgroungColor;
    }

    public function setBackgroungColor(string $backgroungColor): self
    {
        $this->backgroungColor = $backgroungColor;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getClosedAt(): ?\DateTimeInterface
    {
        return $this->closedAt;
    }

    public function setClosedAt(\DateTimeInterface $closedAt) : self
    {
        $this->closedAt = $closedAt;

        return $this;
    }
}
