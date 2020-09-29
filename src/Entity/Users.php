<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UsersRepository::class)
 */
class Users
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pseudo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $img;

    /**
     * @ORM\ManyToOne(targetEntity=Societe::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $societe;

    /**
     * @ORM\OneToMany(targetEntity=Documents::class, mappedBy="user")
     */
    private $documents;

    /**
     * @ORM\OneToMany(targetEntity=Permissions::class, mappedBy="user", orphanRemoval=true)
     */
    private $permissions;

    /**
     * @ORM\OneToMany(targetEntity=Chats::class, mappedBy="user")
     */
    private $chats;

    /**
     * @ORM\OneToMany(targetEntity=Comments::class, mappedBy="user")
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=Tickets::class, mappedBy="user")
     */
    private $tickets;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
        $this->permissions = new ArrayCollection();
        $this->chats = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->tickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): self
    {
        $this->pseudo = $pseudo;

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

    public function getSociete(): ?Societe
    {
        return $this->societe;
    }

    public function setSociete(?Societe $societe): self
    {
        $this->societe = $societe;

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
            $document->setUser($this);
        }

        return $this;
    }

    public function removeDocument(Documents $document): self
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
            // set the owning side to null (unless already changed)
            if ($document->getUser() === $this) {
                $document->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Permissions[]
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermission(Permissions $permission): self
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions[] = $permission;
            $permission->setUser($this);
        }

        return $this;
    }

    public function removePermission(Permissions $permission): self
    {
        if ($this->permissions->contains($permission)) {
            $this->permissions->removeElement($permission);
            // set the owning side to null (unless already changed)
            if ($permission->getUser() === $this) {
                $permission->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Chats[]
     */
    public function getChats(): Collection
    {
        return $this->chats;
    }

    public function addChat(Chats $chat): self
    {
        if (!$this->chats->contains($chat)) {
            $this->chats[] = $chat;
            $chat->setUser($this);
        }

        return $this;
    }

    public function removeChat(Chats $chat): self
    {
        if ($this->chats->contains($chat)) {
            $this->chats->removeElement($chat);
            // set the owning side to null (unless already changed)
            if ($chat->getUser() === $this) {
                $chat->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comments[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
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
            $ticket->setUser($this);
        }

        return $this;
    }

    public function removeTicket(Tickets $ticket): self
    {
        if ($this->tickets->contains($ticket)) {
            $this->tickets->removeElement($ticket);
            // set the owning side to null (unless already changed)
            if ($ticket->getUser() === $this) {
                $ticket->setUser(null);
            }
        }

        return $this;
    }
}
