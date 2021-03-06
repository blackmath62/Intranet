<?php

namespace App\Entity\Main;

use App\Entity\Main\FAQ;
use App\Entity\Main\Trackings;
use App\Entity\Main\FinDeJournee;
use Doctrine\ORM\Mapping as ORM;

use App\Repository\Main\UsersRepository;
use Doctrine\Common\Collections\Collection;
use Proxies\__CG__\App\Entity\Main\IdeaBox;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UsersRepository::class)
 * 
 */
class Users implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Veuillez remplir ce champs")
     * @Assert\Email
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
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    private $pseudo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    private $commercial;

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

    /**
     * @ORM\OneToMany(targetEntity=FAQ::class, mappedBy="user")
     */
    private $faqs;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $bornAt;

    /**
     * @ORM\OneToMany(targetEntity=Documents::class, mappedBy="user")
     */
    private $ideaBoxs;
    /**
     * @ORM\OneToMany(targetEntity=Trackings::class, mappedBy="user")
     */
    private $trackings;

    /**
     * @ORM\OneToMany(targetEntity=Decisionnel::class, mappedBy="user")
     */
    private $decisionnels;


    public function __construct()
    {
        $this->documents = new ArrayCollection();
        $this->ideaBoxs = new ArrayCollection();
        $this->trackings = new ArrayCollection();
        $this->finDeJournee = new ArrayCollection();
        $this->permissions = new ArrayCollection();
        $this->chats = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->tickets = new ArrayCollection();
        $this->faqs = new ArrayCollection();
        $this->decisionnelAchats = new ArrayCollection();
        $this->decisionnels = new ArrayCollection();
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

    public function getCommercial(): ?string
    {
        return $this->commercial;
    }

    public function setCommercial(?string $commercial): self
    {
        $this->commercial = $commercial;

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
    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    
    /**
     * @return Collection|IdeaBox[]
     */
    public function getIdeaBox(): Collection
    {
        return $this->ideaBoxs;
    }
    /**
     * @return Collection|Trackings[]
     */
    public function getTrackings(): Collection
    {
        return $this->trackings;
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
     * @return Collection|FAQ[]
     */
    public function getfaqs(): Collection
    {
        return $this->tickets;
    }

    public function addfaq(FAQ $faq): self
    {
        if (!$this->faqs->contains($faq)) {
            $this->faqs[] = $faq;
            $faq->setUser($this);
        }

        return $this;
    }

    public function removeFaq(FAQ $faq): self
    {
        if ($this->faqs->contains($faq)) {
            $this->faqs->removeElement($faq);
            // set the owning side to null (unless already changed)
            if ($faq->getUser() === $this) {
                $faq->setUser(null);
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
     /**
     * Returns the roles granted to the user.
     *
     * *@see UserInterface
     */
        public function getRoles():array
         {
            $roles = $this->roles; 
            return array_unique($roles);
           
         }
   
    /**
     * Returns the salt that was originally used to encode the password.
     * 
     * @see UserInterface
     */
    public function getSalt(){
        // pour le hashage des mdp géré automatiquement par symfony
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @see UserInterface
     */
    public function getUsername(){
        return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials(){
        //
    }

    public function getBornAt(): ?\DateTimeInterface
    {
        return $this->bornAt;
    }

    public function setBornAt(?\DateTimeInterface $bornAt): self
    {
        $this->bornAt = $bornAt;

        return $this;
    }

    /**
     * @return Collection|Decisionnel[]
     */
    public function getDecisionnels(): Collection
    {
        return $this->decisionnels;
    }

    public function addDecisionnel(Decisionnel $decisionnel): self
    {
        if (!$this->decisionnels->contains($decisionnel)) {
            $this->decisionnels[] = $decisionnel;
            $decisionnel->setUser($this);
        }

        return $this;
    }

    public function removeDecisionnel(Decisionnel $decisionnel): self
    {
        if ($this->decisionnels->removeElement($decisionnel)) {
            // set the owning side to null (unless already changed)
            if ($decisionnel->getUser() === $this) {
                $decisionnel->setUser(null);
            }
        }

        return $this;
    }

}
