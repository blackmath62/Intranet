<?php

namespace App\Entity\Main;

use App\Entity\Main\ListDivaltoUsers;
use App\Repository\Main\UsersDivaltoByFunctionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UsersDivaltoByFunctionRepository::class)
 */
class UsersDivaltoByFunction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=ListDivaltoUsers::class, inversedBy="usersDivaltoByFunctions")
     */
    private $divaltoId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $functions;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDivaltoId(): ?ListDivaltoUsers
    {
        return $this->divaltoId;
    }

    public function setDivaltoId(?ListDivaltoUsers $divaltoId): self
    {
        $this->divaltoId = $divaltoId;

        return $this;
    }

    public function getFunctions(): ?string
    {
        return $this->functions;
    }

    public function setFunctions(string $functions): self
    {
        $this->functions = $functions;

        return $this;
    }
}
