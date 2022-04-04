<?php

namespace App\Entity\Main;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Main\TypeDocumentFscRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=TypeDocumentFscRepository::class)
  * @UniqueEntity("title",
 *     message="Ce nom est déjà utilisé.")
 */
class TypeDocumentFsc
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $color;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $icone;

    /**
     * @ORM\OneToMany(targetEntity=documentsFsc::class, mappedBy="TypeDoc")
     */
    private $documentsFscs;

    public function __construct()
    {
        $this->documentsFscs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getIcone(): ?string
    {
        return $this->icone;
    }

    public function setIcone(?string $icone): self
    {
        $this->icone = $icone;

        return $this;
    }

    /**
     * @return Collection|documentsFsc[]
     */
    public function getDocumentsFscs(): Collection
    {
        return $this->documentsFscs;
    }

    public function addDocumentsFsc(documentsFsc $documentsFsc): self
    {
        if (!$this->documentsFscs->contains($documentsFsc)) {
            $this->documentsFscs[] = $documentsFsc;
            $documentsFsc->setTypeDoc($this);
        }

        return $this;
    }

    public function removeDocumentsFsc(documentsFsc $documentsFsc): self
    {
        if ($this->documentsFscs->removeElement($documentsFsc)) {
            // set the owning side to null (unless already changed)
            if ($documentsFsc->getTypeDoc() === $this) {
                $documentsFsc->setTypeDoc(null);
            }
        }

        return $this;
    }
}
