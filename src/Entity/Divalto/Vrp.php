<?php

namespace App\Entity\Divalto;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\VrpRepository;

/**
 * Vrp
 * @ORM\Entity
 * @ORM\Table(name="VRP", indexes={@ORM\Index(name="INDEX_B_VRP", columns={"DOS", "CE1", "NOMABR", "VRP_ID"}), @ORM\Index(name="INDEX_C_VRP", columns={"DOS", "CE1", "TIERS", "VRP_ID"}), @ORM\Index(name="INDEX_E_VRP", columns={"DOS", "CE1", "PAY", "CPOSTAL", "NOMABR", "VRP_ID"}), @ORM\Index(name="INDEX_I_VRP", columns={"DOS", "CE1", "TEL", "VRP_ID"}), @ORM\Index(name="INDEX_T", columns={"DOS", "SALCOD", "VRP_ID"}), @ORM\Index(name="INDEX_W_VRP", columns={"DOS", "CE1", "TELCLE", "VRP_ID"}), @ORM\Index(name="INDEX_X_VRP", columns={"DOS", "CE1", "EMAIL", "VRP_ID"}), @ORM\Index(name="INDEX_Y", columns={"DOS", "CE1", "TELGSMCLE", "VRP_ID"})})
 */
class Vrp
{
    /**
     * @var string
     *
     * @ORM\Column(name="CE1", type="string", length=1, nullable=false, options={"fixed"=true,"comment"="Ce1"})
     */
    private $ce1;

    /**
     * @var string
     *
     * @ORM\Column(name="CE2", type="string", length=1, nullable=false, options={"fixed"=true,"comment"="Ce2"})
     */
    private $ce2;

    /**
     * @var string
     *
     * @ORM\Column(name="CE3", type="string", length=1, nullable=false, options={"fixed"=true,"comment"="Ce3"})
     */
    private $ce3;

    /**
     * @var string
     *
     * @ORM\Column(name="CE4", type="string", length=1, nullable=false, options={"fixed"=true,"comment"="Ce4"})
     */
    private $ce4;

    /**
     * @var string
     *
     * @ORM\Column(name="CE5", type="string", length=1, nullable=false, options={"fixed"=true,"comment"="Ce5"})
     */
    private $ce5;

    /**
     * @var string
     *
     * @ORM\Column(name="CE6", type="string", length=1, nullable=false, options={"fixed"=true,"comment"="Ce6"})
     */
    private $ce6;

    /**
     * @var string
     *
     * @ORM\Column(name="CE7", type="string", length=1, nullable=false, options={"fixed"=true,"comment"="Ce7"})
     */
    private $ce7;

    /**
     * @var string
     *
     * @ORM\Column(name="CE8", type="string", length=1, nullable=false, options={"fixed"=true,"comment"="Ce8"})
     */
    private $ce8;

    /**
     * @var string
     *
     * @ORM\Column(name="CE9", type="string", length=1, nullable=false, options={"fixed"=true,"comment"="Ce9"})
     */
    private $ce9;

    /**
     * @var string
     *
     * @ORM\Column(name="CEA", type="string", length=1, nullable=false, options={"fixed"=true,"comment"="CeA"})
     */
    private $cea;

    /**
     * @var string
     *
     * @ORM\Column(name="DOS", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Dossier"})
     */
    private $dos;

    /**
     * @var string
     *
     * @ORM\Column(name="TIERS", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Code tiers"})
     */
    private $tiers;

    /**
     * @var string
     *
     * @ORM\Column(name="USERCR", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Auteur de la création"})
     */
    private $usercr;

    /**
     * @var string
     *
     * @ORM\Column(name="USERMO", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Auteur de la modification"})
     */
    private $usermo;

    /**
     * @var string
     *
     * @ORM\Column(name="CONF", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Code confidentiel"})
     */
    private $conf;

    /**
     * @var string
     *
     * @ORM\Column(name="VISA", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Visa"})
     */
    private $visa;

    /**
     * @var string
     *
     * @ORM\Column(name="NOMABR", type="string", length=25, nullable=false, options={"fixed"=true,"comment"="Nom abrégé"})
     */
    private $nomabr;

    /**
     * @var string
     *
     * @ORM\Column(name="NOM", type="string", length=80, nullable=false, options={"fixed"=true,"comment"="Nom"})
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="ADRCPL1", type="string", length=50, nullable=false, options={"fixed"=true,"comment"="Adresse complément 1"})
     */
    private $adrcpl1;

    /**
     * @var string
     *
     * @ORM\Column(name="ADRCPL2", type="string", length=50, nullable=false, options={"fixed"=true,"comment"="Adresse complément 2"})
     */
    private $adrcpl2;

    /**
     * @var string
     *
     * @ORM\Column(name="RUE", type="string", length=50, nullable=false, options={"fixed"=true,"comment"="Rue"})
     */
    private $rue;

    /**
     * @var string
     *
     * @ORM\Column(name="LOC", type="string", length=50, nullable=false, options={"fixed"=true,"comment"="Localité"})
     */
    private $loc;

    /**
     * @var string
     *
     * @ORM\Column(name="VIL", type="string", length=50, nullable=false, options={"fixed"=true,"comment"="Ville"})
     */
    private $vil;

    /**
     * @var string
     *
     * @ORM\Column(name="PAY", type="string", length=3, nullable=false, options={"fixed"=true,"comment"="Pays"})
     */
    private $pay;

    /**
     * @var string
     *
     * @ORM\Column(name="CPOSTAL", type="string", length=10, nullable=false, options={"fixed"=true,"comment"="Code postal"})
     */
    private $cpostal;

    /**
     * @var string
     *
     * @ORM\Column(name="ZIPCOD", type="string", length=50, nullable=false, options={"fixed"=true,"comment"="Code de distribuition etranger"})
     */
    private $zipcod;

    /**
     * @var string
     *
     * @ORM\Column(name="REGIONCOD", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Code de région administrative"})
     */
    private $regioncod;

    /**
     * @var string
     *
     * @ORM\Column(name="INSEECOD", type="string", length=5, nullable=false, options={"fixed"=true,"comment"="Code INSEE"})
     */
    private $inseecod;

    /**
     * @var string
     *
     * @ORM\Column(name="TEL", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Téléphone"})
     */
    private $tel;

    /**
     * @var string
     *
     * @ORM\Column(name="FAX", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Fax"})
     */
    private $fax;

    /**
     * @var string
     *
     * @ORM\Column(name="WEB", type="string", length=40, nullable=false, options={"fixed"=true,"comment"="Adresse web"})
     */
    private $web;

    /**
     * @var string
     *
     * @ORM\Column(name="EMAIL", type="string", length=80, nullable=false, options={"fixed"=true,"comment"="Adresse e-mail"})
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="NAF", type="string", length=5, nullable=false, options={"fixed"=true,"comment"="Naf"})
     */
    private $naf;

    /**
     * @var string
     *
     * @ORM\Column(name="TIT", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Titre (MR MME SA SARL )"})
     */
    private $tit;

    /**
     * @var string
     *
     * @ORM\Column(name="REGL", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Mode de règlement"})
     */
    private $regl;

    /**
     * @var string
     *
     * @ORM\Column(name="DEV", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Devise"})
     */
    private $dev;

    /**
     * @var string
     *
     * @ORM\Column(name="LANG", type="string", length=2, nullable=false, options={"fixed"=true,"comment"="Langue"})
     */
    private $lang;

    /**
     * @var string
     *
     * @ORM\Column(name="CPT", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Compte comptable"})
     */
    private $cpt;

    /**
     * @var string
     *
     * @ORM\Column(name="CPTMSK", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Masque compte vente"})
     */
    private $cptmsk;

    /**
     * @var string
     *
     * @ORM\Column(name="SELCOD", type="string", length=80, nullable=false, options={"fixed"=true,"comment"="Critère de sélection"})
     */
    private $selcod;

    /**
     * @var string
     *
     * @ORM\Column(name="SIRET", type="string", length=15, nullable=false, options={"fixed"=true,"comment"="Siret"})
     */
    private $siret;

    /**
     * @var string
     *
     * @ORM\Column(name="ETB", type="string", length=3, nullable=false, options={"fixed"=true,"comment"="Etablissement"})
     */
    private $etb;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="USERCRDH", type="datetime", nullable=true, options={"comment"="Utilisateur date et heure de création"})
     */
    private $usercrdh;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="USERMODH", type="datetime", nullable=true, options={"comment"="Utilisateur date et heure de modification"})
     */
    private $usermodh;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="HSDT", type="date", nullable=true, options={"comment"="Date de fin de validité"})
     */
    private $hsdt;

    /**
     * @var string
     *
     * @ORM\Column(name="NOTE", type="decimal", precision=8, scale=0, nullable=false, options={"comment"="Numéro de note"})
     */
    private $note;

    /**
     * @var string
     *
     * @ORM\Column(name="CENOTE", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="=2 si note<> 0"})
     */
    private $cenote;

    /**
     * @var string
     *
     * @ORM\Column(name="JOINT", type="decimal", precision=8, scale=0, nullable=false, options={"comment"="Numéro fichier joint"})
     */
    private $joint;

    /**
     * @var string
     *
     * @ORM\Column(name="CEJOINT", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="CE si fichier joint =2 si joint <> 0"})
     */
    private $cejoint;

    /**
     * @var string
     *
     * @ORM\Column(name="IDCONNECT", type="decimal", precision=9, scale=0, nullable=false, options={"comment"="Identification connection"})
     */
    private $idconnect;

    /**
     * @var string
     *
     * @ORM\Column(name="CLDCOD", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Code calendrier"})
     */
    private $cldcod;

    /**
     * @var string
     *
     * @ORM\Column(name="GLN", type="string", length=13, nullable=false, options={"fixed"=true,"comment"="Global Location Number"})
     */
    private $gln;

    /**
     * @var string
     *
     * @ORM\Column(name="TELCLE", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Téléphone servant de clé d accès"})
     */
    private $telcle;

    /**
     * @var string
     *
     * @ORM\Column(name="ICPFL", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Element synchronisé en inter-compagnies"})
     */
    private $icpfl;

    /**
     * @var string
     *
     * @ORM\Column(name="COFAMV", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Famille commission commercial"})
     */
    private $cofamv;

    /**
     * @var string
     *
     * @ORM\Column(name="TELGSM", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Téléphone gsm portable"})
     */
    private $telgsm;

    /**
     * @var string
     *
     * @ORM\Column(name="SALCOD", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Code salarié"})
     */
    private $salcod;

    /**
     * @var string
     *
     * @ORM\Column(name="COMP", type="decimal", precision=5, scale=2, nullable=false, options={"comment"="Taux de commission"})
     */
    private $comp;

    /**
     * @var string
     *
     * @ORM\Column(name="COMTYP", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Type de commission"})
     */
    private $comtyp;

    /**
     * @var string
     *
     * @ORM\Column(name="COMBASTYP", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Type base commission"})
     */
    private $combastyp;

    /**
     * @var string
     *
     * @ORM\Column(name="REPRTYP", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Représentant principal"})
     */
    private $reprtyp;

    /**
     * @var string
     *
     * @ORM\Column(name="TELGSMCLE", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Téléphone portable servant de clé d accès"})
     */
    private $telgsmcle;

    /**
     * @var int
     *
     * @ORM\Column(name="VRP_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $vrpId;

    public function getCe1(): ?string
    {
        return $this->ce1;
    }

    public function setCe1(string $ce1): self
    {
        $this->ce1 = $ce1;

        return $this;
    }

    public function getCe2(): ?string
    {
        return $this->ce2;
    }

    public function setCe2(string $ce2): self
    {
        $this->ce2 = $ce2;

        return $this;
    }

    public function getCe3(): ?string
    {
        return $this->ce3;
    }

    public function setCe3(string $ce3): self
    {
        $this->ce3 = $ce3;

        return $this;
    }

    public function getCe4(): ?string
    {
        return $this->ce4;
    }

    public function setCe4(string $ce4): self
    {
        $this->ce4 = $ce4;

        return $this;
    }

    public function getCe5(): ?string
    {
        return $this->ce5;
    }

    public function setCe5(string $ce5): self
    {
        $this->ce5 = $ce5;

        return $this;
    }

    public function getCe6(): ?string
    {
        return $this->ce6;
    }

    public function setCe6(string $ce6): self
    {
        $this->ce6 = $ce6;

        return $this;
    }

    public function getCe7(): ?string
    {
        return $this->ce7;
    }

    public function setCe7(string $ce7): self
    {
        $this->ce7 = $ce7;

        return $this;
    }

    public function getCe8(): ?string
    {
        return $this->ce8;
    }

    public function setCe8(string $ce8): self
    {
        $this->ce8 = $ce8;

        return $this;
    }

    public function getCe9(): ?string
    {
        return $this->ce9;
    }

    public function setCe9(string $ce9): self
    {
        $this->ce9 = $ce9;

        return $this;
    }

    public function getCea(): ?string
    {
        return $this->cea;
    }

    public function setCea(string $cea): self
    {
        $this->cea = $cea;

        return $this;
    }

    public function getDos(): ?string
    {
        return $this->dos;
    }

    public function setDos(string $dos): self
    {
        $this->dos = $dos;

        return $this;
    }

    public function getTiers(): ?string
    {
        return $this->tiers;
    }

    public function setTiers(string $tiers): self
    {
        $this->tiers = $tiers;

        return $this;
    }

    public function getUsercr(): ?string
    {
        return $this->usercr;
    }

    public function setUsercr(string $usercr): self
    {
        $this->usercr = $usercr;

        return $this;
    }

    public function getUsermo(): ?string
    {
        return $this->usermo;
    }

    public function setUsermo(string $usermo): self
    {
        $this->usermo = $usermo;

        return $this;
    }

    public function getConf(): ?string
    {
        return $this->conf;
    }

    public function setConf(string $conf): self
    {
        $this->conf = $conf;

        return $this;
    }

    public function getVisa(): ?string
    {
        return $this->visa;
    }

    public function setVisa(string $visa): self
    {
        $this->visa = $visa;

        return $this;
    }

    public function getNomabr(): ?string
    {
        return $this->nomabr;
    }

    public function setNomabr(string $nomabr): self
    {
        $this->nomabr = $nomabr;

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

    public function getAdrcpl1(): ?string
    {
        return $this->adrcpl1;
    }

    public function setAdrcpl1(string $adrcpl1): self
    {
        $this->adrcpl1 = $adrcpl1;

        return $this;
    }

    public function getAdrcpl2(): ?string
    {
        return $this->adrcpl2;
    }

    public function setAdrcpl2(string $adrcpl2): self
    {
        $this->adrcpl2 = $adrcpl2;

        return $this;
    }

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(string $rue): self
    {
        $this->rue = $rue;

        return $this;
    }

    public function getLoc(): ?string
    {
        return $this->loc;
    }

    public function setLoc(string $loc): self
    {
        $this->loc = $loc;

        return $this;
    }

    public function getVil(): ?string
    {
        return $this->vil;
    }

    public function setVil(string $vil): self
    {
        $this->vil = $vil;

        return $this;
    }

    public function getPay(): ?string
    {
        return $this->pay;
    }

    public function setPay(string $pay): self
    {
        $this->pay = $pay;

        return $this;
    }

    public function getCpostal(): ?string
    {
        return $this->cpostal;
    }

    public function setCpostal(string $cpostal): self
    {
        $this->cpostal = $cpostal;

        return $this;
    }

    public function getZipcod(): ?string
    {
        return $this->zipcod;
    }

    public function setZipcod(string $zipcod): self
    {
        $this->zipcod = $zipcod;

        return $this;
    }

    public function getRegioncod(): ?string
    {
        return $this->regioncod;
    }

    public function setRegioncod(string $regioncod): self
    {
        $this->regioncod = $regioncod;

        return $this;
    }

    public function getInseecod(): ?string
    {
        return $this->inseecod;
    }

    public function setInseecod(string $inseecod): self
    {
        $this->inseecod = $inseecod;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(string $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    public function getWeb(): ?string
    {
        return $this->web;
    }

    public function setWeb(string $web): self
    {
        $this->web = $web;

        return $this;
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

    public function getNaf(): ?string
    {
        return $this->naf;
    }

    public function setNaf(string $naf): self
    {
        $this->naf = $naf;

        return $this;
    }

    public function getTit(): ?string
    {
        return $this->tit;
    }

    public function setTit(string $tit): self
    {
        $this->tit = $tit;

        return $this;
    }

    public function getRegl(): ?string
    {
        return $this->regl;
    }

    public function setRegl(string $regl): self
    {
        $this->regl = $regl;

        return $this;
    }

    public function getDev(): ?string
    {
        return $this->dev;
    }

    public function setDev(string $dev): self
    {
        $this->dev = $dev;

        return $this;
    }

    public function getLang(): ?string
    {
        return $this->lang;
    }

    public function setLang(string $lang): self
    {
        $this->lang = $lang;

        return $this;
    }

    public function getCpt(): ?string
    {
        return $this->cpt;
    }

    public function setCpt(string $cpt): self
    {
        $this->cpt = $cpt;

        return $this;
    }

    public function getCptmsk(): ?string
    {
        return $this->cptmsk;
    }

    public function setCptmsk(string $cptmsk): self
    {
        $this->cptmsk = $cptmsk;

        return $this;
    }

    public function getSelcod(): ?string
    {
        return $this->selcod;
    }

    public function setSelcod(string $selcod): self
    {
        $this->selcod = $selcod;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(string $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    public function getEtb(): ?string
    {
        return $this->etb;
    }

    public function setEtb(string $etb): self
    {
        $this->etb = $etb;

        return $this;
    }

    public function getUsercrdh(): ?\DateTimeInterface
    {
        return $this->usercrdh;
    }

    public function setUsercrdh(?\DateTimeInterface $usercrdh): self
    {
        $this->usercrdh = $usercrdh;

        return $this;
    }

    public function getUsermodh(): ?\DateTimeInterface
    {
        return $this->usermodh;
    }

    public function setUsermodh(?\DateTimeInterface $usermodh): self
    {
        $this->usermodh = $usermodh;

        return $this;
    }

    public function getHsdt(): ?\DateTimeInterface
    {
        return $this->hsdt;
    }

    public function setHsdt(?\DateTimeInterface $hsdt): self
    {
        $this->hsdt = $hsdt;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getCenote(): ?string
    {
        return $this->cenote;
    }

    public function setCenote(string $cenote): self
    {
        $this->cenote = $cenote;

        return $this;
    }

    public function getJoint(): ?string
    {
        return $this->joint;
    }

    public function setJoint(string $joint): self
    {
        $this->joint = $joint;

        return $this;
    }

    public function getCejoint(): ?string
    {
        return $this->cejoint;
    }

    public function setCejoint(string $cejoint): self
    {
        $this->cejoint = $cejoint;

        return $this;
    }

    public function getIdconnect(): ?string
    {
        return $this->idconnect;
    }

    public function setIdconnect(string $idconnect): self
    {
        $this->idconnect = $idconnect;

        return $this;
    }

    public function getCldcod(): ?string
    {
        return $this->cldcod;
    }

    public function setCldcod(string $cldcod): self
    {
        $this->cldcod = $cldcod;

        return $this;
    }

    public function getGln(): ?string
    {
        return $this->gln;
    }

    public function setGln(string $gln): self
    {
        $this->gln = $gln;

        return $this;
    }

    public function getTelcle(): ?string
    {
        return $this->telcle;
    }

    public function setTelcle(string $telcle): self
    {
        $this->telcle = $telcle;

        return $this;
    }

    public function getIcpfl(): ?string
    {
        return $this->icpfl;
    }

    public function setIcpfl(string $icpfl): self
    {
        $this->icpfl = $icpfl;

        return $this;
    }

    public function getCofamv(): ?string
    {
        return $this->cofamv;
    }

    public function setCofamv(string $cofamv): self
    {
        $this->cofamv = $cofamv;

        return $this;
    }

    public function getTelgsm(): ?string
    {
        return $this->telgsm;
    }

    public function setTelgsm(string $telgsm): self
    {
        $this->telgsm = $telgsm;

        return $this;
    }

    public function getSalcod(): ?string
    {
        return $this->salcod;
    }

    public function setSalcod(string $salcod): self
    {
        $this->salcod = $salcod;

        return $this;
    }

    public function getComp(): ?string
    {
        return $this->comp;
    }

    public function setComp(string $comp): self
    {
        $this->comp = $comp;

        return $this;
    }

    public function getComtyp(): ?string
    {
        return $this->comtyp;
    }

    public function setComtyp(string $comtyp): self
    {
        $this->comtyp = $comtyp;

        return $this;
    }

    public function getCombastyp(): ?string
    {
        return $this->combastyp;
    }

    public function setCombastyp(string $combastyp): self
    {
        $this->combastyp = $combastyp;

        return $this;
    }

    public function getReprtyp(): ?string
    {
        return $this->reprtyp;
    }

    public function setReprtyp(string $reprtyp): self
    {
        $this->reprtyp = $reprtyp;

        return $this;
    }

    public function getTelgsmcle(): ?string
    {
        return $this->telgsmcle;
    }

    public function setTelgsmcle(string $telgsmcle): self
    {
        $this->telgsmcle = $telgsmcle;

        return $this;
    }

    public function getVrpId(): ?int
    {
        return $this->vrpId;
    }


}
