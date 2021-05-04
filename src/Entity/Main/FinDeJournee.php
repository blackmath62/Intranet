<?php

namespace App\Entity\Main;

use App\Repository\Main\FinDeJourneeRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Main\Users;

/**
 * @ORM\Entity(repositoryClass=FinDeJourneeRepository::class)
 */
class FinDeJournee
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


}
