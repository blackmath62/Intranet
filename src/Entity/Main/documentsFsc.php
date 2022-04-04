<?php

namespace App\Entity\Main;

use App\Repository\Main\documentsFscRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=documentsFscRepository::class)
 */
class documentsFsc
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
    private $file;

    /**
     * @ORM\ManyToOne(targetEntity=fscListMovement::class, inversedBy="file")
     */
    private $fscListMovement;

    /**
     * @ORM\ManyToOne(targetEntity=TypeDocumentFsc::class, inversedBy="documentsFscs")
     */
    private $TypeDoc;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFscListMovement(): ?fscListMovement
    {
        return $this->fscListMovement;
    }

    public function setFscListMovement(?fscListMovement $fscListMovement): self
    {
        $this->fscListMovement = $fscListMovement;

        return $this;
    }

    public function getTypeDoc(): ?TypeDocumentFsc
    {
        return $this->TypeDoc;
    }

    public function setTypeDoc(?TypeDocumentFsc $TypeDoc): self
    {
        $this->TypeDoc = $TypeDoc;

        return $this;
    }
}
