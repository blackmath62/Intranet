<?php

namespace App\Entity\Main;

use App\Repository\Main\SocieteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SocieteRepository::class)]
#[UniqueEntity("nom")]
class Societe
{
    #[ORM\Id()]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", unique: true)]
    #[Assert\Length(min: 3, max: 255, minMessage: "Le nom de la société doit être au minimum de 3 caractéres", maxMessage: "Le nom de la société dépasse 255 caractéres")]
    private $nom;

    #[ORM\Column(type: "datetime")]
    private $createdAt;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $closedAt;

    #[ORM\OneToMany(targetEntity: Annuaire::class, mappedBy: "societe", orphanRemoval: true)]
    private $annuaires;

    #[ORM\OneToMany(targetEntity: Users::class, mappedBy: "societe")]
    private $users;

    #[ORM\OneToMany(targetEntity: Documents::class, mappedBy: "societe")]
    private $documents;

    #[ORM\OneToMany(targetEntity: Tickets::class, mappedBy: "societe")]
    private $tickets;

    #[ORM\Column(type: "string", length: 255)]
    private $dossier;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $img;

    #[ORM\Column(type: "string", length: 255)]
    private $parameter;

    public function __construct()
    {
        $this->annuaires = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->tickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt) : self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getClosedAt(): ?\DateTimeInterface
    {
        return $this->closedAt;
    }

    public function setClosedAt(?\DateTimeInterface $closedAt) : self
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    /**
     * @return Collection|Annuaire[]
     */
    public function getAnnuaires() : Collection
    {
        return $this->annuaires;
    }

    public function addAnnuaire(Annuaire $annuaire): self
    {
        if (!$this->annuaires->contains($annuaire)) {
            $this->annuaires[] = $annuaire;
            $annuaire->setSociete($this);
        }

        return $this;
    }

    public function removeAnnuaire(Annuaire $annuaire): self
    {
        if ($this->annuaires->contains($annuaire)) {
            $this->annuaires->removeElement($annuaire);
            // set the owning side to null (unless already changed)
            if ($annuaire->getSociete() === $this) {
                $annuaire->setSociete(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Users[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(Users $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setSociete($this);
        }

        return $this;
    }

    public function removeUser(Users $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getSociete() === $this) {
                $user->setSociete(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Documents[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Documents $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setSociete($this);
        }

        return $this;
    }

    public function removeDocument(Documents $document): self
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
            // set the owning side to null (unless already changed)
            if ($document->getSociete() === $this) {
                $document->setSociete(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tickets[]
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Tickets $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets[] = $ticket;
            $ticket->setSociete($this);
        }

        return $this;
    }

    public function removeTicket(Tickets $ticket): self
    {
        if ($this->tickets->contains($ticket)) {
            $this->tickets->removeElement($ticket);
            // set the owning side to null (unless already changed)
            if ($ticket->getSociete() === $this) {
                $ticket->setSociete(null);
            }
        }

        return $this;
    }

    public function getDossier(): ?string
    {
        return $this->dossier;
    }

    public function setDossier(string $dossier): self
    {
        $this->dossier = $dossier;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): self
    {
        $this->img = $img;

        return $this;
    }

    public function getParameter(): ?string
    {
        return $this->parameter;
    }

    public function setParameter(string $parameter): self
    {
        $this->parameter = $parameter;

        return $this;
    }
}
