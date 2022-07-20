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
     * @ORM\ManyToMany(targetEntity=Holiday::class, mappedBy="user")
     */
    private $holidays;

    /**
     * @ORM\OneToMany(targetEntity=Holiday::class, mappedBy="treatmentedBy")
     */
    private $UserTreatmentholidays;

    /**
     * @ORM\ManyToOne(targetEntity=Services::class, inversedBy="users")
     */
    private $service;

    /**
     * @ORM\OneToMany(targetEntity=News::class, mappedBy="user")
     */
    private $news;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $closedAt;

    /**
     * @ORM\OneToMany(targetEntity=ListCmdTraite::class, mappedBy="treatedBy")
     */
    private $listCmdTraites;

    /**
     * @ORM\OneToMany(targetEntity=Note::class, mappedBy="user")
     */
    private $notes;

    /**
     * @ORM\OneToMany(targetEntity=Commentaires::class, mappedBy="user")
     */
    private $commentaires;

    /**
     * @ORM\OneToMany(targetEntity=DocumentsReglementairesFsc::class, mappedBy="user")
     */
    private $addBys;

    /**
     * @ORM\OneToMany(targetEntity=ControleArticlesFsc::class, mappedBy="controledBy")
     */
    private $controleArticlesFscs;

    /**
     * @ORM\OneToMany(targetEntity=PaysBanFsc::class, mappedBy="CreatedBy")
     */
    private $paysBanFscs;

    /**
     * @ORM\OneToMany(targetEntity=MovBillFsc::class, mappedBy="createdBy")
     */
    private $movBillFscs;

    /**
     * @ORM\OneToMany(targetEntity=fscListMovement::class, mappedBy="userChangePerimetreBoisFsc")
     */
    private $updatePerimetreBoisFsc;

    /**
     * @ORM\OneToMany(targetEntity=ConduiteDeTravauxMe::class, mappedBy="updatedBy")
     */
    private $conduiteDeTravauxMes;

    /**
     * @ORM\OneToMany(targetEntity=OthersDocuments::class, mappedBy="user")
     */
    private $othersDocuments;

    /**
     * @ORM\OneToMany(targetEntity=ConduiteTravauxAddPiece::class, mappedBy="createdBy")
     */
    private $conduiteTravauxAddPieces;
   
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
        $this->holidays = new ArrayCollection();
        $this->UserTreatmentholidays = new ArrayCollection();
        $this->news = new ArrayCollection();
        $this->listCmdTraites = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->addBy = new ArrayCollection();
        $this->controleArticlesFscs = new ArrayCollection();
        $this->paysBanFscs = new ArrayCollection();
        $this->movBillFscs = new ArrayCollection();
        $this->updatePerimetreBoisFsc = new ArrayCollection();
        $this->conduiteDeTravauxMes = new ArrayCollection();
        $this->othersDocuments = new ArrayCollection();
        $this->conduiteTravauxAddPieces = new ArrayCollection();
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
     * @return Collection|Holiday[]
     */
    public function getUserTreatmentholidays(): Collection
    {
        return $this->UserTreatmentholidays;
    }

    public function addUserTreatmentholiday(Holiday $userTreatmentholiday): self
    {
        if (!$this->UserTreatmentholidays->contains($userTreatmentholiday)) {
            $this->UserTreatmentholidays[] = $userTreatmentholiday;
            $userTreatmentholiday->setTreatmentedBy($this);
        }

        return $this;
    }

    public function removeUserTreatmentholiday(Holiday $userTreatmentholiday): self
    {
        if ($this->UserTreatmentholidays->removeElement($userTreatmentholiday)) {
            // set the owning side to null (unless already changed)
            if ($userTreatmentholiday->getTreatmentedBy() === $this) {
                $userTreatmentholiday->setTreatmentedBy(null);
            }
        }

        return $this;
    }

    public function getService(): ?Services
    {
        return $this->service;
    }

    public function setService(?Services $service): self
    {
        $this->service = $service;

        return $this;
    }

    /**
     * @return Collection|News[]
     */
    public function getNews(): Collection
    {
        return $this->news;
    }

    public function addNews(News $news): self
    {
        if (!$this->news->contains($news)) {
            $this->news[] = $news;
            $news->setUser($this);
        }

        return $this;
    }

    public function removeNews(News $news): self
    {
        if ($this->news->removeElement($news)) {
            // set the owning side to null (unless already changed)
            if ($news->getUser() === $this) {
                $news->setUser(null);
            }
        }

        return $this;
    }

    public function getClosedAt(): ?\DateTimeInterface
    {
        return $this->closedAt;
    }

    public function setClosedAt(?\DateTimeInterface $closedAt): self
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    /**
     * @return Collection|ListCmdTraite[]
     */
    public function getListCmdTraites(): Collection
    {
        return $this->listCmdTraites;
    }

    public function addListCmdTraite(ListCmdTraite $listCmdTraite): self
    {
        if (!$this->listCmdTraites->contains($listCmdTraite)) {
            $this->listCmdTraites[] = $listCmdTraite;
            $listCmdTraite->setTreatedBy($this);
        }

        return $this;
    }

    public function removeListCmdTraite(ListCmdTraite $listCmdTraite): self
    {
        if ($this->listCmdTraites->removeElement($listCmdTraite)) {
            // set the owning side to null (unless already changed)
            if ($listCmdTraite->getTreatedBy() === $this) {
                $listCmdTraite->setTreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Note[]
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setUser($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getUser() === $this) {
                $note->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Commentaires[]
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaires $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setUser($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaires $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getUser() === $this) {
                $commentaire->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|DocumentsReglementairesFsc[]
     */
    public function getAddbys(): Collection
    {
        return $this->addBys;
    }

    public function addAddBy(DocumentsReglementairesFsc $addBy): self
    {
        if (!$this->addBys->contains($addBy)) {
            $this->addBys[] = $addBy;
            $addBy->setUser($this);
        }

        return $this;
    }

    public function removeAddBy(DocumentsReglementairesFsc $addBy): self
    {
        if ($this->addBys->removeElement($addBy)) {
            // set the owning side to null (unless already changed)
            if ($addBy->getUser() === $this) {
                $addBy->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ControleArticlesFsc[]
     */
    public function getControleArticlesFscs(): Collection
    {
        return $this->controleArticlesFscs;
    }

    public function addControleArticlesFsc(ControleArticlesFsc $controleArticlesFsc): self
    {
        if (!$this->controleArticlesFscs->contains($controleArticlesFsc)) {
            $this->controleArticlesFscs[] = $controleArticlesFsc;
            $controleArticlesFsc->setControledBy($this);
        }

        return $this;
    }

    public function removeControleArticlesFsc(ControleArticlesFsc $controleArticlesFsc): self
    {
        if ($this->controleArticlesFscs->removeElement($controleArticlesFsc)) {
            // set the owning side to null (unless already changed)
            if ($controleArticlesFsc->getControledBy() === $this) {
                $controleArticlesFsc->setControledBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PaysBanFsc[]
     */
    public function getPaysBanFscs(): Collection
    {
        return $this->paysBanFscs;
    }

    public function addPaysBanFsc(PaysBanFsc $paysBanFsc): self
    {
        if (!$this->paysBanFscs->contains($paysBanFsc)) {
            $this->paysBanFscs[] = $paysBanFsc;
            $paysBanFsc->setCreatedBy($this);
        }

        return $this;
    }

    public function removePaysBanFsc(PaysBanFsc $paysBanFsc): self
    {
        if ($this->paysBanFscs->removeElement($paysBanFsc)) {
            // set the owning side to null (unless already changed)
            if ($paysBanFsc->getCreatedBy() === $this) {
                $paysBanFsc->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|MovBillFsc[]
     */
    public function getMovBillFscs(): Collection
    {
        return $this->movBillFscs;
    }

    public function addMovBillFsc(MovBillFsc $movBillFsc): self
    {
        if (!$this->movBillFscs->contains($movBillFsc)) {
            $this->movBillFscs[] = $movBillFsc;
            $movBillFsc->setCreatedBy($this);
        }

        return $this;
    }

    public function removeMovBillFsc(MovBillFsc $movBillFsc): self
    {
        if ($this->movBillFscs->removeElement($movBillFsc)) {
            // set the owning side to null (unless already changed)
            if ($movBillFsc->getCreatedBy() === $this) {
                $movBillFsc->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|fscListMovement[]
     */
    public function getUpdatePerimetreBoisFsc(): Collection
    {
        return $this->updatePerimetreBoisFsc;
    }

    public function addUpdatePerimetreBoisFsc(fscListMovement $updatePerimetreBoisFsc): self
    {
        if (!$this->updatePerimetreBoisFsc->contains($updatePerimetreBoisFsc)) {
            $this->updatePerimetreBoisFsc[] = $updatePerimetreBoisFsc;
            $updatePerimetreBoisFsc->setUserChangePerimetreBoisFsc($this);
        }

        return $this;
    }

    public function removeUpdatePerimetreBoisFsc(fscListMovement $updatePerimetreBoisFsc): self
    {
        if ($this->updatePerimetreBoisFsc->removeElement($updatePerimetreBoisFsc)) {
            // set the owning side to null (unless already changed)
            if ($updatePerimetreBoisFsc->getUserChangePerimetreBoisFsc() === $this) {
                $updatePerimetreBoisFsc->setUserChangePerimetreBoisFsc(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ConduiteDeTravauxMe[]
     */
    public function getConduiteDeTravauxMes(): Collection
    {
        return $this->conduiteDeTravauxMes;
    }

    public function addConduiteDeTravauxMe(ConduiteDeTravauxMe $conduiteDeTravauxMe): self
    {
        if (!$this->conduiteDeTravauxMes->contains($conduiteDeTravauxMe)) {
            $this->conduiteDeTravauxMes[] = $conduiteDeTravauxMe;
            $conduiteDeTravauxMe->setUpdatedBy($this);
        }

        return $this;
    }

    public function removeConduiteDeTravauxMe(ConduiteDeTravauxMe $conduiteDeTravauxMe): self
    {
        if ($this->conduiteDeTravauxMes->removeElement($conduiteDeTravauxMe)) {
            // set the owning side to null (unless already changed)
            if ($conduiteDeTravauxMe->getUpdatedBy() === $this) {
                $conduiteDeTravauxMe->setUpdatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|OthersDocuments[]
     */
    public function getOthersDocuments(): Collection
    {
        return $this->othersDocuments;
    }

    public function addOthersDocument(OthersDocuments $othersDocument): self
    {
        if (!$this->othersDocuments->contains($othersDocument)) {
            $this->othersDocuments[] = $othersDocument;
            $othersDocument->setUser($this);
        }

        return $this;
    }

    public function removeOthersDocument(OthersDocuments $othersDocument): self
    {
        if ($this->othersDocuments->removeElement($othersDocument)) {
            // set the owning side to null (unless already changed)
            if ($othersDocument->getUser() === $this) {
                $othersDocument->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ConduiteTravauxAddPiece[]
     */
    public function getConduiteTravauxAddPieces(): Collection
    {
        return $this->conduiteTravauxAddPieces;
    }

    public function addConduiteTravauxAddPiece(ConduiteTravauxAddPiece $conduiteTravauxAddPiece): self
    {
        if (!$this->conduiteTravauxAddPieces->contains($conduiteTravauxAddPiece)) {
            $this->conduiteTravauxAddPieces[] = $conduiteTravauxAddPiece;
            $conduiteTravauxAddPiece->setCreatedBy($this);
        }

        return $this;
    }

    public function removeConduiteTravauxAddPiece(ConduiteTravauxAddPiece $conduiteTravauxAddPiece): self
    {
        if ($this->conduiteTravauxAddPieces->removeElement($conduiteTravauxAddPiece)) {
            // set the owning side to null (unless already changed)
            if ($conduiteTravauxAddPiece->getCreatedBy() === $this) {
                $conduiteTravauxAddPiece->setCreatedBy(null);
            }
        }

        return $this;
    }

}
