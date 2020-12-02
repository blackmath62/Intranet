<?php

namespace DivaltoSvg\Entity;

use App\Repository\ARTRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ARTRepository::class)
 * @ORM\Entity
*/ 
class ART
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $ART_ID;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $REF;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $DES;

    public function getREF(): ?string
    {
        return $this->REF;
    }

    public function getDES(): ?string
    {
        return $this->DES;
    }

    
}
