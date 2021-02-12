<?php

namespace App\Entity\Main;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\Main\PermissionsRepository;

/**
 * @ORM\Entity(repositoryClass=PermissionsRepository::class)
 */
class Permissions
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="permissions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Features::class, inversedBy="permissions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $feature;

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

    public function getFeature(): ?Features
    {
        return $this->feature;
    }

    public function setFeature(?Features $feature): self
    {
        $this->feature = $feature;

        return $this;
    }
}
