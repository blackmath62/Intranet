<?php

namespace App\Entity\Main;

use App\Repository\Main\MouvPreprationCommandeSaisieRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MouvPreprationCommandeSaisieRepository::class)]
class MouvPreprationCommandeSaisie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $cmd = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(length: 255)]
    private ?string $preparateur = null;

    #[ORM\Column]
    private ?int $enregistrement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $qte = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emplacement = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $sendAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCmd(): ?int
    {
        return $this->cmd;
    }

    public function setCmd(int $cmd): static
    {
        $this->cmd = $cmd;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getPreparateur(): ?string
    {
        return $this->preparateur;
    }

    public function setPreparateur(string $preparateur): static
    {
        $this->preparateur = $preparateur;

        return $this;
    }

    public function getEnregistrement(): ?int
    {
        return $this->enregistrement;
    }

    public function setEnregistrement(int $enregistrement): static
    {
        $this->enregistrement = $enregistrement;

        return $this;
    }

    public function getQte(): ?string
    {
        return $this->qte;
    }

    public function setQte(?string $qte): static
    {
        $this->qte = $qte;

        return $this;
    }

    public function getEmplacement(): ?string
    {
        return $this->emplacement;
    }

    public function setEmplacement(?string $emplacement): static
    {
        $this->emplacement = $emplacement;

        return $this;
    }

    public function getSendAt(): ?\DateTimeInterface
    {
        return $this->sendAt;
    }

    public function setSendAt(?\DateTimeInterface $sendAt): static
    {
        $this->sendAt = $sendAt;

        return $this;
    }
}
