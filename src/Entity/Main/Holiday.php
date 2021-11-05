<?php

namespace App\Entity\Main;

use App\Repository\Main\HolidayRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HolidayRepository::class)
 */
class Holiday
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=Users::class, inversedBy="holidays")
     */
    private $user;

    
    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $details;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $treatmentedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="UserTreatmentholidays")
     */
    private $treatmentedBy;

    /**
     * @ORM\ManyToOne(targetEntity=HolidayTypes::class, inversedBy="holidays")
     * @ORM\JoinColumn(nullable=false)
     */
    private $holidayType;

    /**
     * @ORM\ManyToOne(targetEntity=statusHoliday::class, inversedBy="holidays")
     * @ORM\JoinColumn(nullable=false)
     */
    private $holidayStatus;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sliceStart;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sliceEnd;

    /**
     * @ORM\Column(type="datetime")
     */
    private $start;

    /**
     * @ORM\Column(type="datetime")
     */
    private $end;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=1)
     */
    private $nbJours;

    
    public function __construct()
    {
        $this->user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Users[]
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(Users $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
        }

        return $this;
    }

    public function removeUser(Users $user): self
    {
        $this->user->removeElement($user);

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

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function getTreatmentedAt(): ?\DateTimeInterface
    {
        return $this->treatmentedAt;
    }

    public function setTreatmentedAt(?\DateTimeInterface $treatmentedAt): self
    {
        $this->treatmentedAt = $treatmentedAt;

        return $this;
    }

    public function getTreatmentedBy(): ?Users
    {
        return $this->treatmentedBy;
    }

    public function setTreatmentedBy(?Users $treatmentedBy): self
    {
        $this->treatmentedBy = $treatmentedBy;

        return $this;
    }

    public function getHolidayType(): ?HolidayTypes
    {
        return $this->holidayType;
    }

    public function setHolidayType(?HolidayTypes $holidayType): self
    {
        $this->holidayType = $holidayType;

        return $this;
    }

    public function getHolidayStatus(): ?statusHoliday
    {
        return $this->holidayStatus;
    }

    public function setHolidayStatus(?statusHoliday $holidayStatus): self
    {
        $this->holidayStatus = $holidayStatus;

        return $this;
    }

    public function getSliceStart(): ?string
    {
        return $this->sliceStart;
    }

    public function setSliceStart(string $sliceStart): self
    {
        $this->sliceStart = $sliceStart;

        return $this;
    }

    public function getSliceEnd(): ?string
    {
        return $this->sliceEnd;
    }

    public function setSliceEnd(string $sliceEnd): self
    {
        $this->sliceEnd = $sliceEnd;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getNbJours(): ?string
    {
        return $this->nbJours;
    }

    public function setNbJours(string $nbJours): self
    {
        $this->nbJours = $nbJours;

        return $this;
    }

}
