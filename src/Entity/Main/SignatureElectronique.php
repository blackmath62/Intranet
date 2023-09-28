<?php

namespace App\Entity\Main;

use App\Repository\Main\SignatureElectroniqueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SignatureElectroniqueRepository::class)]
class SignatureElectronique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $signatureId;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $documentId;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $signerId;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $pdfSansSignature;

    #[ORM\Column(type: "datetime")]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: InterventionMonteurs::class, inversedBy: "signatureElectroniques")]
    private $intervention;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "signatureElectroniques")]
    private $createdBy;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSignatureId(): ?string
    {
        return $this->signatureId;
    }

    public function setSignatureId(?string $signatureId): self
    {
        $this->signatureId = $signatureId;

        return $this;
    }

    public function getDocumentId(): ?string
    {
        return $this->documentId;
    }

    public function setDocumentId(?string $documentId): self
    {
        $this->documentId = $documentId;

        return $this;
    }

    public function getSignerId(): ?string
    {
        return $this->signerId;
    }

    public function setSignerId(?string $signerId): self
    {
        $this->signerId = $signerId;

        return $this;
    }

    public function getPdfSansSignature(): ?string
    {
        return $this->pdfSansSignature;
    }

    public function setPdfSansSignature(?string $pdfSansSignature): self
    {
        $this->pdfSansSignature = $pdfSansSignature;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt) : self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getIntervention(): ?InterventionMonteurs
    {
        return $this->intervention;
    }

    public function setIntervention(?InterventionMonteurs $intervention): self
    {
        $this->intervention = $intervention;

        return $this;
    }

    public function getCreatedBy(): ?Users
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?Users $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}
