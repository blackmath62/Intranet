<?php

namespace App\Entity\Main;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\Main\OthersDocumentsRepository;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=OthersDocumentsRepository::class)
 * @UniqueEntity("file",
 *     message="Ce nom est déjà utilisé.")
 */
class OthersDocuments
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="othersDocuments")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $file;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tables;

    /**
     * @ORM\Column(type="integer")
     */
    private $identifiant;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Parametre;

    public function getId(): ?int
    {
        return $this->id;
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

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getTables(): ?string
    {
        return $this->tables;
    }

    public function setTables(string $tables): self
    {
        $this->tables = $tables;

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

    public function getParametre(): ?string
    {
        return $this->Parametre;
    }

    public function setParametre(?string $Parametre): self
    {
        $this->Parametre = $Parametre;

        return $this;
    }
}
