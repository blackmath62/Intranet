<?php

namespace App\Entity\Main;

use App\Repository\Main\MouvPreparationCmdAdminRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MouvPreparationCmdAdminRepository::class)]
class MouvPreparationCmdAdmin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'mouvPreparationCmdAdminAssignedBy')]
    private ?Users $assignedBy = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $assignedAt = null;

    #[ORM\ManyToOne(inversedBy : 'mouvPreparationCmdAdminPreparedBy')]
    private ?Users $preparedBy = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $preparedAt = null;

    #[ORM\Column]
    private ?int $cdNo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAssignedBy(): ?Users
    {
        return $this->assignedBy;
    }

    public function setAssignedBy(?Users $assignedBy): static
    {
        $this->assignedBy = $assignedBy;

        return $this;
    }

    public function getAssignedAt(): ?\DateTimeInterface
    {
        return $this->assignedAt;
    }

    public function setAssignedAt(?\DateTimeInterface $assignedAt): static
    {
        $this->assignedAt = $assignedAt;

        return $this;
    }

    public function getPreparedBy(): ?Users
    {
        return $this->preparedBy;
    }

    public function setPreparedBy(?Users $preparedBy): static
    {
        $this->preparedBy = $preparedBy;

        return $this;
    }

    public function getPreparedAt(): ?\DateTimeInterface
    {
        return $this->preparedAt;
    }

    public function setPreparedAt(?\DateTimeInterface $preparedAt): static
    {
        $this->preparedAt = $preparedAt;

        return $this;
    }

    public function getCdNo(): ?int
    {
        return $this->cdNo;
    }

    public function setCdNo(int $cdNo): static
    {
        $this->cdNo = $cdNo;

        return $this;
    }
}
