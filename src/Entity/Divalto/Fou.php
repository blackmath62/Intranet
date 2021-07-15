<?php

namespace App\Entity\Divalto;

use App\Entity\Main\DecisionnelAchat;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Divalto\FouRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Fou
 * @ORM\Entity
 * @ORM\Table(name="FOU", indexes={@ORM\Index(name="INDEX_B_FOU", columns={"DOS", "CE1", "NOMABR", "FOU_ID"}), @ORM\Index(name="INDEX_C_FOU", columns={"DOS", "CE1", "TIERS", "FOU_ID"}), @ORM\Index(name="INDEX_E_FOU", columns={"DOS", "CE1", "PAY", "CPOSTAL", "NOMABR", "FOU_ID"}), @ORM\Index(name="INDEX_F_FOU", columns={"CE4", "DOS", "CE1", "STAT_0001", "NOMABR", "FOU_ID"}), @ORM\Index(name="INDEX_G_FOU", columns={"CE4", "DOS", "CE1", "STAT_0001", "TIERS", "FOU_ID"}), @ORM\Index(name="INDEX_H_FOU", columns={"CE4", "DOS", "CE1", "STAT_0001", "PAY", "CPOSTAL", "FOU_ID"}), @ORM\Index(name="INDEX_I_FOU", columns={"DOS", "CE1", "TEL", "FOU_ID"}), @ORM\Index(name="INDEX_J_FOU", columns={"CE3", "DOS", "CE1", "TIERSGRP", "TIERS", "FOU_ID"}), @ORM\Index(name="INDEX_K_FOU", columns={"CE5", "DOS", "CE1", "STAT_0002", "NOMABR", "FOU_ID"}), @ORM\Index(name="INDEX_L_FOU", columns={"CE5", "DOS", "CE1", "STAT_0002", "TIERS", "FOU_ID"}), @ORM\Index(name="INDEX_M_FOU", columns={"CE5", "DOS", "CE1", "STAT_0002", "PAY", "CPOSTAL", "FOU_ID"}), @ORM\Index(name="INDEX_N_FOU", columns={"CE6", "DOS", "CE1", "STAT_0003", "NOMABR", "FOU_ID"}), @ORM\Index(name="INDEX_O_FOU", columns={"CE6", "DOS", "CE1", "STAT_0003", "TIERS", "FOU_ID"}), @ORM\Index(name="INDEX_P_FOU", columns={"CE6", "DOS", "CE1", "STAT_0003", "PAY", "CPOSTAL", "FOU_ID"}), @ORM\Index(name="INDEX_W_FOU", columns={"DOS", "CE1", "TELCLE", "FOU_ID"}), @ORM\Index(name="INDEX_X_FOU", columns={"DOS", "CE1", "EMAIL", "FOU_ID"}), @ORM\Index(name="INDEX_BMINI", columns={"DOS", "TIERSCLI", "FOU_ID"})})
 */
class Fou
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
     * @ORM\Column(name="TACOD", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Code tarif HT"})
     */
    private $tacod;

    /**
     * @var string
     *
     * @ORM\Column(name="REMCOD", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Code remise"})
     */
    private $remcod;

    /**
     * @var string
     *
     * @ORM\Column(name="PROMOTACOD", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Code promotion"})
     */
    private $promotacod;

    /**
     * @var string
     *
     * @ORM\Column(name="PROMOREMCOD", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Code remise promotion"})
     */
    private $promoremcod;

    /**
     * @var string
     *
     * @ORM\Column(name="TAFAM", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Famille tarification"})
     */
    private $tafam;

    /**
     * @var string
     *
     * @ORM\Column(name="TAFAMX", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Famille tarif exceptionnelle"})
     */
    private $tafamx;

    /**
     * @var string
     *
     * @ORM\Column(name="REFAM", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Classe de remise"})
     */
    private $refam;

    /**
     * @var string
     *
     * @ORM\Column(name="REFAMX", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Classe remise tiers exceptionnelle"})
     */
    private $refamx;

    /**
     * @var string
     *
     * @ORM\Column(name="REM_0001", type="decimal", precision=6, scale=2, nullable=false, options={"comment"="Remise"})
     */
    private $rem0001;

    /**
     * @var string
     *
     * @ORM\Column(name="REM_0002", type="decimal", precision=6, scale=2, nullable=false, options={"comment"="Remise"})
     */
    private $rem0002;

    /**
     * @var string
     *
     * @ORM\Column(name="REM_0003", type="decimal", precision=6, scale=2, nullable=false, options={"comment"="Remise"})
     */
    private $rem0003;

    /**
     * @var string
     *
     * @ORM\Column(name="REMTYP_0001", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Type de remise"})
     */
    private $remtyp0001;

    /**
     * @var string
     *
     * @ORM\Column(name="REMTYP_0002", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Type de remise"})
     */
    private $remtyp0002;

    /**
     * @var string
     *
     * @ORM\Column(name="REMTYP_0003", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Type de remise"})
     */
    private $remtyp0003;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="DOPDH", type="datetime", nullable=true, options={"comment"="Date et heure de dernière opération"})
     */
    private $dopdh;

    /**
     * @var string
     *
     * @ORM\Column(name="NBEX_0001", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Nombre exemplaire"})
     */
    private $nbex0001;

    /**
     * @var string
     *
     * @ORM\Column(name="NBEX_0002", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Nombre exemplaire"})
     */
    private $nbex0002;

    /**
     * @var string
     *
     * @ORM\Column(name="NBEX_0003", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Nombre exemplaire"})
     */
    private $nbex0003;

    /**
     * @var string
     *
     * @ORM\Column(name="NBEX_0004", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Nombre exemplaire"})
     */
    private $nbex0004;

    /**
     * @var string
     *
     * @ORM\Column(name="EDITTYP_0001", type="decimal", precision=2, scale=0, nullable=false, options={"comment"="Type d impression"})
     */
    private $edittyp0001;

    /**
     * @var string
     *
     * @ORM\Column(name="EDITTYP_0002", type="decimal", precision=2, scale=0, nullable=false, options={"comment"="Type d impression"})
     */
    private $edittyp0002;

    /**
     * @var string
     *
     * @ORM\Column(name="EDITTYP_0003", type="decimal", precision=2, scale=0, nullable=false, options={"comment"="Type d impression"})
     */
    private $edittyp0003;

    /**
     * @var string
     *
     * @ORM\Column(name="EDITTYP_0004", type="decimal", precision=2, scale=0, nullable=false, options={"comment"="Type d impression"})
     */
    private $edittyp0004;

    /**
     * @var string
     *
     * @ORM\Column(name="ETANO_0001", type="string", length=1, nullable=false, options={"fixed"=true,"comment"="Numéro  d état"})
     */
    private $etano0001;

    /**
     * @var string
     *
     * @ORM\Column(name="ETANO_0002", type="string", length=1, nullable=false, options={"fixed"=true,"comment"="Numéro  d état"})
     */
    private $etano0002;

    /**
     * @var string
     *
     * @ORM\Column(name="ETANO_0003", type="string", length=1, nullable=false, options={"fixed"=true,"comment"="Numéro  d état"})
     */
    private $etano0003;

    /**
     * @var string
     *
     * @ORM\Column(name="ETANO_0004", type="string", length=1, nullable=false, options={"fixed"=true,"comment"="Numéro  d état"})
     */
    private $etano0004;

    /**
     * @var string
     *
     * @ORM\Column(name="AXEMSK", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Masque analytique"})
     */
    private $axemsk;

    /**
     * @var string
     *
     * @ORM\Column(name="AXENO", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="No axe  analytique"})
     */
    private $axeno;

    /**
     * @var string
     *
     * @ORM\Column(name="STLGTGAMCOD", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Code de la gamme"})
     */
    private $stlgtgamcod;

    /**
     * @var string
     *
     * @ORM\Column(name="WMAUDITFL", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Audit colis Oui/non"})
     */
    private $wmauditfl;

    /**
     * @var string
     *
     * @ORM\Column(name="WMDOCEMP", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Emplacement du document (Interne / Externe)"})
     */
    private $wmdocemp;

    /**
     * @var string
     *
     * @ORM\Column(name="WMRESAIMPFL", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Accepte la méthode d’affectation des réservations en volume important"})
     */
    private $wmresaimpfl;

    /**
     * @var string
     *
     * @ORM\Column(name="TRANSPLANCOD", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Code plan de transport"})
     */
    private $transplancod;

    /**
     * @var string
     *
     * @ORM\Column(name="TEXCOD_0001", type="string", length=11, nullable=false, options={"fixed"=true,"comment"="Code texte préenregistré"})
     */
    private $texcod0001;

    /**
     * @var string
     *
     * @ORM\Column(name="TEXCOD_0002", type="string", length=11, nullable=false, options={"fixed"=true,"comment"="Code texte préenregistré"})
     */
    private $texcod0002;

    /**
     * @var string
     *
     * @ORM\Column(name="TEXCOD_0003", type="string", length=11, nullable=false, options={"fixed"=true,"comment"="Code texte préenregistré"})
     */
    private $texcod0003;

    /**
     * @var string
     *
     * @ORM\Column(name="TEXCOD_0004", type="string", length=11, nullable=false, options={"fixed"=true,"comment"="Code texte préenregistré"})
     */
    private $texcod0004;

    /**
     * @var string
     *
     * @ORM\Column(name="TEXCOD_0005", type="string", length=11, nullable=false, options={"fixed"=true,"comment"="Code texte préenregistré"})
     */
    private $texcod0005;

    /**
     * @var string
     *
     * @ORM\Column(name="TEXCOD_0006", type="string", length=11, nullable=false, options={"fixed"=true,"comment"="Code texte préenregistré"})
     */
    private $texcod0006;

    /**
     * @var string
     *
     * @ORM\Column(name="TEXCOD_0007", type="string", length=11, nullable=false, options={"fixed"=true,"comment"="Code texte préenregistré"})
     */
    private $texcod0007;

    /**
     * @var string
     *
     * @ORM\Column(name="TEXCOD_0008", type="string", length=11, nullable=false, options={"fixed"=true,"comment"="Code texte préenregistré"})
     */
    private $texcod0008;

    /**
     * @var string
     *
     * @ORM\Column(name="AFFCLDCOD", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Code calendrier pour affaire"})
     */
    private $affcldcod;

    /**
     * @var string
     *
     * @ORM\Column(name="IDENTITEEXT", type="decimal", precision=9, scale=0, nullable=false, options={"comment"="Identification externe pour Divalto"})
     */
    private $identiteext;

    /**
     * @var string
     *
     * @ORM\Column(name="STAT_0001", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Statistique tiers"})
     */
    private $stat0001;

    /**
     * @var string
     *
     * @ORM\Column(name="STAT_0002", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Statistique tiers"})
     */
    private $stat0002;

    /**
     * @var string
     *
     * @ORM\Column(name="STAT_0003", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Statistique tiers"})
     */
    private $stat0003;

    /**
     * @var string
     *
     * @ORM\Column(name="TPFT", type="string", length=1, nullable=false, options={"fixed"=true,"comment"="Régime TPF tiers"})
     */
    private $tpft;

    /**
     * @var string
     *
     * @ORM\Column(name="TIERSGRP", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Code groupement tiers"})
     */
    private $tiersgrp;

    /**
     * @var string
     *
     * @ORM\Column(name="ZONA", type="string", length=40, nullable=false, options={"fixed"=true,"comment"="Zone alpha libre"})
     */
    private $zona;

    /**
     * @var string
     *
     * @ORM\Column(name="CAMT", type="decimal", precision=10, scale=0, nullable=false, options={"comment"="Chiffre d affaires"})
     */
    private $camt;

    /**
     * @var string
     *
     * @ORM\Column(name="SALNB", type="decimal", precision=4, scale=0, nullable=false, options={"comment"="Nombre de salariés"})
     */
    private $salnb;

    /**
     * @var string
     *
     * @ORM\Column(name="ENMAX_0001", type="decimal", precision=12, scale=2, nullable=false, options={"comment"="Encours maximum"})
     */
    private $enmax0001;

    /**
     * @var string
     *
     * @ORM\Column(name="ENMAX_0002", type="decimal", precision=12, scale=2, nullable=false, options={"comment"="Encours maximum"})
     */
    private $enmax0002;

    /**
     * @var string
     *
     * @ORM\Column(name="ESCP", type="decimal", precision=5, scale=2, nullable=false, options={"comment"="Taux escompte"})
     */
    private $escp;

    /**
     * @var string
     *
     * @ORM\Column(name="ACP", type="decimal", precision=5, scale=2, nullable=false, options={"comment"="Pourcentage acompte"})
     */
    private $acp;

    /**
     * @var string
     *
     * @ORM\Column(name="ZONN", type="decimal", precision=16, scale=3, nullable=false, options={"comment"="Zone libre numérique"})
     */
    private $zonn;

    /**
     * @var string
     *
     * @ORM\Column(name="TVATIE", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Régime TVA tiers"})
     */
    private $tvatie;

    /**
     * @var string
     *
     * @ORM\Column(name="ADRTIERS_0001", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Tiers adresse"})
     */
    private $adrtiers0001;

    /**
     * @var string
     *
     * @ORM\Column(name="ADRTIERS_0002", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Tiers adresse"})
     */
    private $adrtiers0002;

    /**
     * @var string
     *
     * @ORM\Column(name="ADRTIERS_0003", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Tiers adresse"})
     */
    private $adrtiers0003;

    /**
     * @var string
     *
     * @ORM\Column(name="ADRTIERS_0004", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Tiers adresse"})
     */
    private $adrtiers0004;

    /**
     * @var string
     *
     * @ORM\Column(name="ADRTIERS_0005", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Tiers adresse"})
     */
    private $adrtiers0005;

    /**
     * @var string
     *
     * @ORM\Column(name="ADRCOD_0001", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Code adresse"})
     */
    private $adrcod0001;

    /**
     * @var string
     *
     * @ORM\Column(name="ADRCOD_0002", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Code adresse"})
     */
    private $adrcod0002;

    /**
     * @var string
     *
     * @ORM\Column(name="ADRCOD_0003", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Code adresse"})
     */
    private $adrcod0003;

    /**
     * @var string
     *
     * @ORM\Column(name="ADRCOD_0004", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Code adresse"})
     */
    private $adrcod0004;

    /**
     * @var string
     *
     * @ORM\Column(name="ADRCOD_0005", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Code adresse"})
     */
    private $adrcod0005;

    /**
     * @var string
     *
     * @ORM\Column(name="TIERSSTAT", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Code tiers pour statistique"})
     */
    private $tiersstat;

    /**
     * @var string
     *
     * @ORM\Column(name="TIERSR3", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Code tiers pour Divalto Règlements"})
     */
    private $tiersr3;

    /**
     * @var string
     *
     * @ORM\Column(name="RELCOD_0001", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Reliquat ?"})
     */
    private $relcod0001;

    /**
     * @var string
     *
     * @ORM\Column(name="RELCOD_0002", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Reliquat ?"})
     */
    private $relcod0002;

    /**
     * @var string
     *
     * @ORM\Column(name="RELCOD_0003", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Reliquat ?"})
     */
    private $relcod0003;

    /**
     * @var string
     *
     * @ORM\Column(name="BLMOD", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Mode de transport"})
     */
    private $blmod;

    /**
     * @var string
     *
     * @ORM\Column(name="TVANO", type="string", length=14, nullable=false, options={"fixed"=true,"comment"="DEB no identification"})
     */
    private $tvano;

    /**
     * @var string
     *
     * @ORM\Column(name="TVAPAY", type="string", length=3, nullable=false, options={"fixed"=true,"comment"="Tva pays identification"})
     */
    private $tvapay;

    /**
     * @var string
     *
     * @ORM\Column(name="TVABLCOE", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Tva coefficient transport"})
     */
    private $tvablcoe;

    /**
     * @var string
     *
     * @ORM\Column(name="TVABLCD3", type="string", length=3, nullable=false, options={"fixed"=true,"comment"="Tva condition livraison code incoterm 3"})
     */
    private $tvablcd3;

    /**
     * @var string
     *
     * @ORM\Column(name="TVAMAXMT", type="decimal", precision=8, scale=0, nullable=false, options={"comment"="Montant franchise tva"})
     */
    private $tvamaxmt;

    /**
     * @var string
     *
     * @ORM\Column(name="TRANSJRNB", type="decimal", precision=3, scale=0, nullable=false, options={"comment"="Nombre de jours de transport"})
     */
    private $transjrnb;

    /**
     * @var string
     *
     * @ORM\Column(name="RFCCTRCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Contrôler le référencement"})
     */
    private $rfcctrcod;

    /**
     * @var string
     *
     * @ORM\Column(name="PROTOCOL", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Protocole EDI"})
     */
    private $protocol;

    /**
     * @var string
     *
     * @ORM\Column(name="TIERSEXTERNE", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Code tiers de l entreprise chez le tiers"})
     */
    private $tiersexterne;

    /**
     * @var string
     *
     * @ORM\Column(name="FEU", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Feu rouge / feu vert"})
     */
    private $feu;

    /**
     * @var string
     *
     * @ORM\Column(name="RELLIGCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Gestion des reliquats à la ligne"})
     */
    private $relligcod;

    /**
     * @var string
     *
     * @ORM\Column(name="VALLIGCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Masquer les lignes validées"})
     */
    private $valligcod;

    /**
     * @var string
     *
     * @ORM\Column(name="DEMATCOD", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Type de dématérialisation"})
     */
    private $dematcod;

    /**
     * @var string
     *
     * @ORM\Column(name="TIERSPAYER", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Tiers payeur"})
     */
    private $tierspayer;

    /**
     * @var string
     *
     * @ORM\Column(name="BAPSALCOD", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Signataire du bon à payer"})
     */
    private $bapsalcod;

    /**
     * @var string
     *
     * @ORM\Column(name="QUACOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Controle qualité"})
     */
    private $quacod;

    /**
     * @var string
     *
     * @ORM\Column(name="CDARCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Commande accusé réception"})
     */
    private $cdarcod;

    /**
     * @var string
     *
     * @ORM\Column(name="DELFO", type="decimal", precision=3, scale=0, nullable=false, options={"comment"="Délai reapprovisionnement fournisseur"})
     */
    private $delfo;

    /**
     * @var string
     *
     * @ORM\Column(name="PORFRVAL", type="decimal", precision=6, scale=0, nullable=false, options={"comment"="Valeur franco"})
     */
    private $porfrval;

    /**
     * @var string
     *
     * @ORM\Column(name="PORFRCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Franco port code"})
     */
    private $porfrcod;

    /**
     * @var string
     *
     * @ORM\Column(name="SALCOD", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Code salarié"})
     */
    private $salcod;

    /**
     * @var string
     *
     * @ORM\Column(name="DELSECUJR", type="decimal", precision=3, scale=0, nullable=false, options={"comment"="Délai de sécurité jours"})
     */
    private $delsecujr;

    /**
     * @var string
     *
     * @ORM\Column(name="BQCA", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Compte banquaire par défaut - Achats"})
     */
    private $bqca;

    /**
     * @var string
     *
     * @ORM\Column(name="PRIOREG", type="decimal", precision=2, scale=0, nullable=false, options={"comment"="Priorité du décaissement du fournisseur"})
     */
    private $prioreg;

    /**
     * @var string
     *
     * @ORM\Column(name="ESCCOD", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Code escompte"})
     */
    private $esccod;

    /**
     * @var string
     *
     * @ORM\Column(name="CTRLFAVAL", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Nécessite une validation du contrôle facture"})
     */
    private $ctrlfaval;

    /**
     * @var string
     *
     * @ORM\Column(name="SANSACHATFLG", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Indicateur si fournisseur sans achat préalable"})
     */
    private $sansachatflg;

    /**
     * @var string
     *
     * @ORM\Column(name="CTRLTOLP", type="decimal", precision=7, scale=2, nullable=false, options={"comment"="Pourcentage de tolérence"})
     */
    private $ctrltolp;

    /**
     * @var string
     *
     * @ORM\Column(name="ACHSALCOD_0001", type="string", length=20, nullable=false, options={"fixed"=true})
     */
    private $achsalcod0001;

    /**
     * @var string
     *
     * @ORM\Column(name="ACHSALCOD_0002", type="string", length=20, nullable=false, options={"fixed"=true})
     */
    private $achsalcod0002;

    /**
     * @var string
     *
     * @ORM\Column(name="CATFOUCOD", type="string", length=8, nullable=false, options={"fixed"=true})
     */
    private $catfoucod;

    /**
     * @var string
     *
     * @ORM\Column(name="CDMT", type="decimal", precision=12, scale=2, nullable=false)
     */
    private $cdmt;

    /**
     * @var string
     *
     * @ORM\Column(name="SEUILMT", type="decimal", precision=12, scale=2, nullable=false)
     */
    private $seuilmt;

    /**
     * @var string
     *
     * @ORM\Column(name="TIERSCLI", type="string", length=20, nullable=false, options={"fixed"=true})
     */
    private $tierscli;

    /**
     * @var string
     *
     * @ORM\Column(name="TREFCOEFZEROFL_0001", type="decimal", precision=1, scale=0, nullable=false)
     */
    private $trefcoefzerofl0001;

    /**
     * @var string
     *
     * @ORM\Column(name="TREFCOEFZEROFL_0002", type="decimal", precision=1, scale=0, nullable=false)
     */
    private $trefcoefzerofl0002;

    /**
     * @var string
     *
     * @ORM\Column(name="TREFCOEFZEROFL_0003", type="decimal", precision=1, scale=0, nullable=false)
     */
    private $trefcoefzerofl0003;

    /**
     * @var string
     *
     * @ORM\Column(name="TREFCOEF_0001", type="decimal", precision=6, scale=2, nullable=false)
     */
    private $trefcoef0001;

    /**
     * @var string
     *
     * @ORM\Column(name="TREFCOEF_0002", type="decimal", precision=6, scale=2, nullable=false)
     */
    private $trefcoef0002;

    /**
     * @var string
     *
     * @ORM\Column(name="TREFCOEF_0003", type="decimal", precision=6, scale=2, nullable=false)
     */
    private $trefcoef0003;

    /**
     * @var string
     *
     * @ORM\Column(name="TREFFRAISMT_0001", type="decimal", precision=13, scale=2, nullable=false)
     */
    private $treffraismt0001;

    /**
     * @var string
     *
     * @ORM\Column(name="TREFFRAISMT_0002", type="decimal", precision=13, scale=2, nullable=false)
     */
    private $treffraismt0002;

    /**
     * @var string
     *
     * @ORM\Column(name="TREFFRAISMT_0003", type="decimal", precision=13, scale=2, nullable=false)
     */
    private $treffraismt0003;

    /**
     * @var string
     *
     * @ORM\Column(name="TREFFRAISTX_0001", type="decimal", precision=5, scale=2, nullable=false)
     */
    private $treffraistx0001;

    /**
     * @var string
     *
     * @ORM\Column(name="TREFFRAISTX_0002", type="decimal", precision=5, scale=2, nullable=false)
     */
    private $treffraistx0002;

    /**
     * @var string
     *
     * @ORM\Column(name="TREFFRAISTX_0003", type="decimal", precision=5, scale=2, nullable=false)
     */
    private $treffraistx0003;

    /**
     * @var string
     *
     * @ORM\Column(name="TREFFRAISTYP_0001", type="decimal", precision=1, scale=0, nullable=false)
     */
    private $treffraistyp0001;

    /**
     * @var string
     *
     * @ORM\Column(name="TREFFRAISTYP_0002", type="decimal", precision=1, scale=0, nullable=false)
     */
    private $treffraistyp0002;

    /**
     * @var string
     *
     * @ORM\Column(name="TREFFRAISTYP_0003", type="decimal", precision=1, scale=0, nullable=false)
     */
    private $treffraistyp0003;

    /**
     * @var string
     *
     * @ORM\Column(name="TREFFRAISZEROFL_0001", type="decimal", precision=1, scale=0, nullable=false)
     */
    private $treffraiszerofl0001;

    /**
     * @var string
     *
     * @ORM\Column(name="TREFFRAISZEROFL_0002", type="decimal", precision=1, scale=0, nullable=false)
     */
    private $treffraiszerofl0002;

    /**
     * @var string
     *
     * @ORM\Column(name="TREFFRAISZEROFL_0003", type="decimal", precision=1, scale=0, nullable=false)
     */
    private $treffraiszerofl0003;

    /**
     * @var string
     *
     * @ORM\Column(name="TVATRANSITFL", type="decimal", precision=1, scale=0, nullable=false)
     */
    private $tvatransitfl;

    /**
     * @var string
     *
     * @ORM\Column(name="UNTYP", type="string", length=8, nullable=false, options={"fixed"=true})
     */
    private $untyp;

    /**
     * @var int
     *
     * @ORM\Column(name="FOU_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $fouId;

    public function __construct()
    {
        $this->decisionnelAchats = new ArrayCollection();
    }

    public function getCe1(): ?string
    {
        return $this->ce1;
    }

    public function getCe2(): ?string
    {
        return $this->ce2;
    }

    public function getCe3(): ?string
    {
        return $this->ce3;
    }

    public function getCe4(): ?string
    {
        return $this->ce4;
    }

    public function getCe5(): ?string
    {
        return $this->ce5;
    }

    public function getCe6(): ?string
    {
        return $this->ce6;
    }

    public function getCe7(): ?string
    {
        return $this->ce7;
    }

    public function getCe8(): ?string
    {
        return $this->ce8;
    }

    public function getCe9(): ?string
    {
        return $this->ce9;
    }

    public function getCea(): ?string
    {
        return $this->cea;
    }

    public function getDos(): ?string
    {
        return $this->dos;
    }

    public function getTiers(): ?string
    {
        return $this->tiers;
    }

    public function getUsercr(): ?string
    {
        return $this->usercr;
    }

    public function getUsermo(): ?string
    {
        return $this->usermo;
    }

    public function getConf(): ?string
    {
        return $this->conf;
    }

    public function getVisa(): ?string
    {
        return $this->visa;
    }

    public function getNomabr(): ?string
    {
        return $this->nomabr;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function getAdrcpl1(): ?string
    {
        return $this->adrcpl1;
    }

    public function getAdrcpl2(): ?string
    {
        return $this->adrcpl2;
    }

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function getLoc(): ?string
    {
        return $this->loc;
    }

    public function getVil(): ?string
    {
        return $this->vil;
    }

    public function getPay(): ?string
    {
        return $this->pay;
    }

    public function getCpostal(): ?string
    {
        return $this->cpostal;
    }

    public function getZipcod(): ?string
    {
        return $this->zipcod;
    }

    public function getRegioncod(): ?string
    {
        return $this->regioncod;
    }

    public function getInseecod(): ?string
    {
        return $this->inseecod;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function getWeb(): ?string
    {
        return $this->web;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getNaf(): ?string
    {
        return $this->naf;
    }

    public function getTit(): ?string
    {
        return $this->tit;
    }

    public function getRegl(): ?string
    {
        return $this->regl;
    }

    public function getDev(): ?string
    {
        return $this->dev;
    }

    public function getLang(): ?string
    {
        return $this->lang;
    }

    public function getCpt(): ?string
    {
        return $this->cpt;
    }

    public function getCptmsk(): ?string
    {
        return $this->cptmsk;
    }

    public function getSelcod(): ?string
    {
        return $this->selcod;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function getEtb(): ?string
    {
        return $this->etb;
    }

    public function getUsercrdh(): ?\DateTimeInterface
    {
        return $this->usercrdh;
    }

    public function getUsermodh(): ?\DateTimeInterface
    {
        return $this->usermodh;
    }

    public function getHsdt(): ?\DateTimeInterface
    {
        return $this->hsdt;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function getCenote(): ?string
    {
        return $this->cenote;
    }

    public function getJoint(): ?string
    {
        return $this->joint;
    }

    public function getCejoint(): ?string
    {
        return $this->cejoint;
    }

    public function getIdconnect(): ?string
    {
        return $this->idconnect;
    }

    public function getCldcod(): ?string
    {
        return $this->cldcod;
    }

    public function getGln(): ?string
    {
        return $this->gln;
    }

    public function getTelcle(): ?string
    {
        return $this->telcle;
    }

    public function getIcpfl(): ?string
    {
        return $this->icpfl;
    }

    public function getTacod(): ?string
    {
        return $this->tacod;
    }

    public function getRemcod(): ?string
    {
        return $this->remcod;
    }

    public function getPromotacod(): ?string
    {
        return $this->promotacod;
    }

    public function getPromoremcod(): ?string
    {
        return $this->promoremcod;
    }

    public function getTafam(): ?string
    {
        return $this->tafam;
    }

    public function getTafamx(): ?string
    {
        return $this->tafamx;
    }

    public function getRefam(): ?string
    {
        return $this->refam;
    }

    public function getRefamx(): ?string
    {
        return $this->refamx;
    }

    public function getRem0001(): ?string
    {
        return $this->rem0001;
    }

    public function getRem0002(): ?string
    {
        return $this->rem0002;
    }

    public function getRem0003(): ?string
    {
        return $this->rem0003;
    }

    public function getRemtyp0001(): ?string
    {
        return $this->remtyp0001;
    }

    public function getRemtyp0002(): ?string
    {
        return $this->remtyp0002;
    }

    public function getRemtyp0003(): ?string
    {
        return $this->remtyp0003;
    }

    public function getDopdh(): ?\DateTimeInterface
    {
        return $this->dopdh;
    }

    public function getNbex0001(): ?string
    {
        return $this->nbex0001;
    }

    public function getNbex0002(): ?string
    {
        return $this->nbex0002;
    }

    public function getNbex0003(): ?string
    {
        return $this->nbex0003;
    }

    public function getNbex0004(): ?string
    {
        return $this->nbex0004;
    }

    public function getEdittyp0001(): ?string
    {
        return $this->edittyp0001;
    }

    public function getEdittyp0002(): ?string
    {
        return $this->edittyp0002;
    }

    public function getEdittyp0003(): ?string
    {
        return $this->edittyp0003;
    }

    public function getEdittyp0004(): ?string
    {
        return $this->edittyp0004;
    }

    public function getEtano0001(): ?string
    {
        return $this->etano0001;
    }

    public function getEtano0002(): ?string
    {
        return $this->etano0002;
    }

    public function getEtano0003(): ?string
    {
        return $this->etano0003;
    }

    public function getEtano0004(): ?string
    {
        return $this->etano0004;
    }

    public function getAxemsk(): ?string
    {
        return $this->axemsk;
    }

    public function getAxeno(): ?string
    {
        return $this->axeno;
    }

    public function getStlgtgamcod(): ?string
    {
        return $this->stlgtgamcod;
    }

    public function getWmauditfl(): ?string
    {
        return $this->wmauditfl;
    }

    public function getWmdocemp(): ?string
    {
        return $this->wmdocemp;
    }

    public function getWmresaimpfl(): ?string
    {
        return $this->wmresaimpfl;
    }

    public function getTransplancod(): ?string
    {
        return $this->transplancod;
    }

    public function getTexcod0001(): ?string
    {
        return $this->texcod0001;
    }

    public function getTexcod0002(): ?string
    {
        return $this->texcod0002;
    }

    public function getTexcod0003(): ?string
    {
        return $this->texcod0003;
    }

    public function getTexcod0004(): ?string
    {
        return $this->texcod0004;
    }

    public function getTexcod0005(): ?string
    {
        return $this->texcod0005;
    }

    public function getTexcod0006(): ?string
    {
        return $this->texcod0006;
    }

    public function getTexcod0007(): ?string
    {
        return $this->texcod0007;
    }

    public function getTexcod0008(): ?string
    {
        return $this->texcod0008;
    }

    public function getAffcldcod(): ?string
    {
        return $this->affcldcod;
    }

    public function getIdentiteext(): ?string
    {
        return $this->identiteext;
    }

    public function getStat0001(): ?string
    {
        return $this->stat0001;
    }

    public function getStat0002(): ?string
    {
        return $this->stat0002;
    }

    public function getStat0003(): ?string
    {
        return $this->stat0003;
    }

    public function getTpft(): ?string
    {
        return $this->tpft;
    }

    public function getTiersgrp(): ?string
    {
        return $this->tiersgrp;
    }

    public function getZona(): ?string
    {
        return $this->zona;
    }

    public function getCamt(): ?string
    {
        return $this->camt;
    }

    public function getSalnb(): ?string
    {
        return $this->salnb;
    }

    public function getEnmax0001(): ?string
    {
        return $this->enmax0001;
    }

    public function getEnmax0002(): ?string
    {
        return $this->enmax0002;
    }

    public function getEscp(): ?string
    {
        return $this->escp;
    }

    public function getAcp(): ?string
    {
        return $this->acp;
    }

    public function getZonn(): ?string
    {
        return $this->zonn;
    }

    public function getTvatie(): ?string
    {
        return $this->tvatie;
    }

    public function getAdrtiers0001(): ?string
    {
        return $this->adrtiers0001;
    }

    public function getAdrtiers0002(): ?string
    {
        return $this->adrtiers0002;
    }

    public function getAdrtiers0003(): ?string
    {
        return $this->adrtiers0003;
    }

    public function getAdrtiers0004(): ?string
    {
        return $this->adrtiers0004;
    }

    public function getAdrtiers0005(): ?string
    {
        return $this->adrtiers0005;
    }

    public function getAdrcod0001(): ?string
    {
        return $this->adrcod0001;
    }

    public function getAdrcod0002(): ?string
    {
        return $this->adrcod0002;
    }

    public function getAdrcod0003(): ?string
    {
        return $this->adrcod0003;
    }

    public function getAdrcod0004(): ?string
    {
        return $this->adrcod0004;
    }

    public function getAdrcod0005(): ?string
    {
        return $this->adrcod0005;
    }

    public function getTiersstat(): ?string
    {
        return $this->tiersstat;
    }

    public function getTiersr3(): ?string
    {
        return $this->tiersr3;
    }

    public function getRelcod0001(): ?string
    {
        return $this->relcod0001;
    }

    public function getRelcod0002(): ?string
    {
        return $this->relcod0002;
    }

    public function getRelcod0003(): ?string
    {
        return $this->relcod0003;
    }

    public function getBlmod(): ?string
    {
        return $this->blmod;
    }

    public function getTvano(): ?string
    {
        return $this->tvano;
    }

    public function getTvapay(): ?string
    {
        return $this->tvapay;
    }

    public function getTvablcoe(): ?string
    {
        return $this->tvablcoe;
    }

    public function getTvablcd3(): ?string
    {
        return $this->tvablcd3;
    }

    public function getTvamaxmt(): ?string
    {
        return $this->tvamaxmt;
    }

    public function getTransjrnb(): ?string
    {
        return $this->transjrnb;
    }

    public function getRfcctrcod(): ?string
    {
        return $this->rfcctrcod;
    }

    public function getProtocol(): ?string
    {
        return $this->protocol;
    }

    public function getTiersexterne(): ?string
    {
        return $this->tiersexterne;
    }

    public function getFeu(): ?string
    {
        return $this->feu;
    }

    public function getRelligcod(): ?string
    {
        return $this->relligcod;
    }

    public function getValligcod(): ?string
    {
        return $this->valligcod;
    }

    public function getDematcod(): ?string
    {
        return $this->dematcod;
    }

    public function getTierspayer(): ?string
    {
        return $this->tierspayer;
    }

    public function getBapsalcod(): ?string
    {
        return $this->bapsalcod;
    }

    public function getQuacod(): ?string
    {
        return $this->quacod;
    }

    public function getCdarcod(): ?string
    {
        return $this->cdarcod;
    }

    public function getDelfo(): ?string
    {
        return $this->delfo;
    }

    public function getPorfrval(): ?string
    {
        return $this->porfrval;
    }

    public function getPorfrcod(): ?string
    {
        return $this->porfrcod;
    }

    public function getSalcod(): ?string
    {
        return $this->salcod;
    }

    public function getDelsecujr(): ?string
    {
        return $this->delsecujr;
    }

    public function getBqca(): ?string
    {
        return $this->bqca;
    }

    public function getPrioreg(): ?string
    {
        return $this->prioreg;
    }

    public function getEsccod(): ?string
    {
        return $this->esccod;
    }

    public function getCtrlfaval(): ?string
    {
        return $this->ctrlfaval;
    }

    public function getSansachatflg(): ?string
    {
        return $this->sansachatflg;
    }

    public function getCtrltolp(): ?string
    {
        return $this->ctrltolp;
    }

    public function getAchsalcod0001(): ?string
    {
        return $this->achsalcod0001;
    }

    public function getAchsalcod0002(): ?string
    {
        return $this->achsalcod0002;
    }

    public function getCatfoucod(): ?string
    {
        return $this->catfoucod;
    }

    public function getCdmt(): ?string
    {
        return $this->cdmt;
    }

    public function getSeuilmt(): ?string
    {
        return $this->seuilmt;
    }

    public function getTierscli(): ?string
    {
        return $this->tierscli;
    }

    public function getTrefcoefzerofl0001(): ?string
    {
        return $this->trefcoefzerofl0001;
    }

    public function getTrefcoefzerofl0002(): ?string
    {
        return $this->trefcoefzerofl0002;
    }

    public function getTrefcoefzerofl0003(): ?string
    {
        return $this->trefcoefzerofl0003;
    }

    public function getTrefcoef0001(): ?string
    {
        return $this->trefcoef0001;
    }

    public function getTrefcoef0002(): ?string
    {
        return $this->trefcoef0002;
    }

    public function getTrefcoef0003(): ?string
    {
        return $this->trefcoef0003;
    }

    public function getTreffraismt0001(): ?string
    {
        return $this->treffraismt0001;
    }

    public function getTreffraismt0002(): ?string
    {
        return $this->treffraismt0002;
    }

    public function getTreffraismt0003(): ?string
    {
        return $this->treffraismt0003;
    }

    public function getTreffraistx0001(): ?string
    {
        return $this->treffraistx0001;
    }

    public function getTreffraistx0002(): ?string
    {
        return $this->treffraistx0002;
    }

    public function getTreffraistx0003(): ?string
    {
        return $this->treffraistx0003;
    }

    public function getTreffraistyp0001(): ?string
    {
        return $this->treffraistyp0001;
    }

    public function getTreffraistyp0002(): ?string
    {
        return $this->treffraistyp0002;
    }

    public function getTreffraistyp0003(): ?string
    {
        return $this->treffraistyp0003;
    }

    public function getTreffraiszerofl0001(): ?string
    {
        return $this->treffraiszerofl0001;
    }

    public function getTreffraiszerofl0002(): ?string
    {
        return $this->treffraiszerofl0002;
    }

    public function getTreffraiszerofl0003(): ?string
    {
        return $this->treffraiszerofl0003;
    }

    public function getTvatransitfl(): ?string
    {
        return $this->tvatransitfl;
    }

    public function getUntyp(): ?string
    {
        return $this->untyp;
    }

    public function getFouId(): ?int
    {
        return $this->fouId;
    }

}
