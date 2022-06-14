<?php

namespace App\Entity\Main;

use App\Repository\Main\PaysBanFscRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PaysBanFscRepository::class)
 */
class PaysBanFsc
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\Length(min=2,max=2, minMessage="Le Pays doit être composé de 2 caractéres comme dans Divalto",maxMessage="Le Pays doit être composé de 2 caractéres comme dans Divalto")
     */
    private $pays;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="paysBanFscs")
     */
    private $CreatedBy;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): self
    {
        $this->pays = $pays;

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

    public function getCreatedBy(): ?Users
    {
        return $this->CreatedBy;
    }

    public function setCreatedBy(?Users $CreatedBy): self
    {
        $this->CreatedBy = $CreatedBy;

        return $this;
    }
}
