<?php

namespace App\Entity\Main;

use App\Entity\Main\UsersDivaltoByFunction;
use App\Repository\Main\ListDivaltoUsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ListDivaltoUsersRepository::class)]
class ListDivaltoUsers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "integer")]
    private $divalto_id;

    #[ORM\Column(type: "string", length: 255)]
    private $userX;

    #[ORM\Column(type: "string", length: 255)]
    private $nom;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $dos;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $email;

    #[ORM\Column(type: "boolean")]
    private $valid;

    #[ORM\OneToMany(targetEntity: UsersDivaltoByFunction::class, mappedBy: "divaltoId")]
    private $usersDivaltoByFunctions;

    public function __construct()
    {
        $this->usersDivaltoByFunctions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDivaltoId(): ?int
    {
        return $this->divalto_id;
    }

    public function setDivaltoId(int $divalto_id): self
    {
        $this->divalto_id = $divalto_id;

        return $this;
    }

    public function getUserX(): ?string
    {
        return $this->userX;
    }

    public function setUserX(string $userX): self
    {
        $this->userX = $userX;

        return $this;
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

    public function getDos(): ?int
    {
        return $this->dos;
    }

    public function setDos(?int $dos): self
    {
        $this->dos = $dos;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    /**
     * @return Collection|UsersDivaltoByFunction[]
     */
    public function getUsersDivaltoByFunctions(): Collection
    {
        return $this->usersDivaltoByFunctions;
    }

    public function addUsersDivaltoByFunction(UsersDivaltoByFunction $usersDivaltoByFunction): self
    {
        if (!$this->usersDivaltoByFunctions->contains($usersDivaltoByFunction)) {
            $this->usersDivaltoByFunctions[] = $usersDivaltoByFunction;
            $usersDivaltoByFunction->setDivaltoId($this);
        }

        return $this;
    }

    public function removeUsersDivaltoByFunction(UsersDivaltoByFunction $usersDivaltoByFunction): self
    {
        if ($this->usersDivaltoByFunctions->removeElement($usersDivaltoByFunction)) {
            // set the owning side to null (unless already changed)
            if ($usersDivaltoByFunction->getDivaltoId() === $this) {
                $usersDivaltoByFunction->setDivaltoId(null);
            }
        }

        return $this;
    }
}
