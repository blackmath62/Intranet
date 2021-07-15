<?php

namespace App\Entity\Divalto;

use App\Entity\Main\Decisionnel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Divalto\ArtRepository;

/**
 * Art
 * 
 * @ORM\Entity(repositoryClass=ArtRepository::class)
 * @ORM\Table(name="ART", indexes={@ORM\Index(name="INDEX_A", columns={"DOS", "REF", "ART_ID"}), @ORM\Index(name="INDEX_A_MINI", columns={"CE9", "DOS", "REF", "ART_ID"}), @ORM\Index(name="INDEX_B", columns={"DOS", "DESABR", "ART_ID"}), @ORM\Index(name="INDEX_I", columns={"DOS", "EAN", "ART_ID"}), @ORM\Index(name="INDEX_J", columns={"DOS", "FAM_0001", "REF", "ART_ID"}), @ORM\Index(name="INDEX_K", columns={"DOS", "FAM_0002", "REF", "ART_ID"}), @ORM\Index(name="INDEX_L", columns={"DOS", "FAM_0003", "REF", "ART_ID"}), @ORM\Index(name="INDEX_M", columns={"DOS", "TIERS", "REF", "ART_ID"}), @ORM\Index(name="INDEX_Z", columns={"DOS", "PRODNAT", "REF", "ART_ID"})})
 */
class Art
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

    //JEROME j'ai ajouté la relation OneToMany ci dessous

    /**
     * @var string
     * @ORM\OneToMany(targetEntity=Mouv::class, mappedBy="Art")
     * @ORM\Column(name="REF", type="string", length=25, nullable=false, options={"fixed"=true,"comment"="Référence"})
     */
    private $ref;

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
     * @ORM\Column(name="DES", type="string", length=80, nullable=false, options={"fixed"=true,"comment"="Désignation"})
     */
    private $des;

    /**
     * @var string
     *
     * @ORM\Column(name="DESABR", type="string", length=25, nullable=false, options={"fixed"=true,"comment"="Désignation abrégée"})
     */
    private $desabr;

    /**
     * @var string
     *
     * @ORM\Column(name="EAN", type="string", length=13, nullable=false, options={"fixed"=true,"comment"="Code EAN"})
     */
    private $ean;

    /**
     * @var string
     *
     * @ORM\Column(name="TIERS", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Code tiers"})
     */
    private $tiers;

    /**
     * @var string
     *
     * @ORM\Column(name="TAREF", type="string", length=25, nullable=false, options={"fixed"=true,"comment"="Référence de tarification"})
     */
    private $taref;

    /**
     * @var string
     *
     * @ORM\Column(name="REFRPL", type="string", length=25, nullable=false, options={"fixed"=true,"comment"="Référence de remplacement"})
     */
    private $refrpl;

    /**
     * @var string
     *
     * @ORM\Column(name="TAFAMRX", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Famille tarif exceptionnelle"})
     */
    private $tafamrx;

    /**
     * @var string
     *
     * @ORM\Column(name="TAFAMR", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Famille de tarification"})
     */
    private $tafamr;

    /**
     * @var string
     *
     * @ORM\Column(name="REFAMRX", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Famille remise exceptionnelle"})
     */
    private $refamrx;

    /**
     * @var string
     *
     * @ORM\Column(name="REFAMR", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Famille de remise"})
     */
    private $refamr;

    /**
     * @var string
     *
     * @ORM\Column(name="COFAMR", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Famille de commission article"})
     */
    private $cofamr;

    /**
     * @var string
     *
     * @ORM\Column(name="FAM_0001", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Famille statistique article"})
     */
    private $fam0001;

    /**
     * @var string
     *
     * @ORM\Column(name="FAM_0002", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Famille statistique article"})
     */
    private $fam0002;

    /**
     * @var string
     *
     * @ORM\Column(name="FAM_0003", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Famille statistique article"})
     */
    private $fam0003;

    /**
     * @var string
     *
     * @ORM\Column(name="PRODNAT", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Nature du produit"})
     */
    private $prodnat;

    /**
     * @var string
     *
     * @ORM\Column(name="REFUN", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Unité de référence"})
     */
    private $refun;

    /**
     * @var string
     *
     * @ORM\Column(name="ACHUN", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Unité d achat"})
     */
    private $achun;

    /**
     * @var string
     *
     * @ORM\Column(name="STUN", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Unité de stockage"})
     */
    private $stun;

    /**
     * @var string
     *
     * @ORM\Column(name="VENUN", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Unité de vente"})
     */
    private $venun;

    /**
     * @var string
     *
     * @ORM\Column(name="POIUN", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Unité du poids"})
     */
    private $poiun;

    /**
     * @var string
     *
     * @ORM\Column(name="VOLUN", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Unité du volume"})
     */
    private $volun;

    /**
     * @var string
     *
     * @ORM\Column(name="DIMUN", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Unité des dimensions"})
     */
    private $dimun;

    /**
     * @var string
     *
     * @ORM\Column(name="CPTV", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Compte de vente"})
     */
    private $cptv;

    /**
     * @var string
     *
     * @ORM\Column(name="CPTA", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Compte d achat"})
     */
    private $cpta;

    /**
     * @var string
     *
     * @ORM\Column(name="CPTS", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Compte de stock"})
     */
    private $cpts;

    /**
     * @var string
     *
     * @ORM\Column(name="TPFR_0001", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Code TPF article"})
     */
    private $tpfr0001;

    /**
     * @var string
     *
     * @ORM\Column(name="TPFR_0002", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Code TPF article"})
     */
    private $tpfr0002;

    /**
     * @var string
     *
     * @ORM\Column(name="TPFR_0003", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Code TPF article"})
     */
    private $tpfr0003;

    /**
     * @var string
     *
     * @ORM\Column(name="TVANOM", type="string", length=13, nullable=false, options={"fixed"=true,"comment"="Tva nomenclature douane"})
     */
    private $tvanom;

    /**
     * @var string
     *
     * @ORM\Column(name="TVAUN", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Tva unité douanière"})
     */
    private $tvaun;

    /**
     * @var string
     *
     * @ORM\Column(name="TVARGCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="TVA code régime échange"})
     */
    private $tvargcod;

    /**
     * @var string
     *
     * @ORM\Column(name="EDCOD", type="string", length=5, nullable=false, options={"fixed"=true,"comment"="Code édition"})
     */
    private $edcod;

    /**
     * @var string
     *
     * @ORM\Column(name="GRICOD", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Code grille sous-référence"})
     */
    private $gricod;

    /**
     * @var string
     *
     * @ORM\Column(name="ZONA", type="string", length=40, nullable=false, options={"fixed"=true,"comment"="Zone alpha libre"})
     */
    private $zona;

    /**
     * @var string
     *
     * @ORM\Column(name="MEDIA", type="string", length=40, nullable=false, options={"fixed"=true,"comment"="Image multimédia"})
     */
    private $media;

    /**
     * @var string
     *
     * @ORM\Column(name="HTML", type="string", length=255, nullable=false, options={"fixed"=true,"comment"="Nom de la page html"})
     */
    private $html;

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
     * @ORM\Column(name="ABCCOD", type="string", length=1, nullable=false, options={"fixed"=true,"comment"="Code abc"})
     */
    private $abccod;

    /**
     * @var string
     *
     * @ORM\Column(name="QUESTION", type="string", length=32, nullable=false, options={"fixed"=true,"comment"="Questionnaire implicite dynamique"})
     */
    private $question;

    /**
     * @var string
     *
     * @ORM\Column(name="CBNGESCOD", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Code règle de gestion du CBN"})
     */
    private $cbngescod;

    /**
     * @var string
     *
     * @ORM\Column(name="COMPETCOD", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Code compétence"})
     */
    private $competcod;

    /**
     * @var string
     *
     * @ORM\Column(name="CPTACES", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Compte d achat inter établissement"})
     */
    private $cptaces;

    /**
     * @var string
     *
     * @ORM\Column(name="CPTVCES", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Compte de vente inter établissement"})
     */
    private $cptvces;

    /**
     * @var string
     *
     * @ORM\Column(name="ACHTAFAMRX", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Famille de tarification promotion article exceptionnelle"})
     */
    private $achtafamrx;

    /**
     * @var string
     *
     * @ORM\Column(name="ACHTAFAMR", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Famille de tarification"})
     */
    private $achtafamr;

    /**
     * @var string
     *
     * @ORM\Column(name="ACHREFAMRX", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Classe de remise article achat exceptionnelle"})
     */
    private $achrefamrx;

    /**
     * @var string
     *
     * @ORM\Column(name="ACHREFAMR", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Classe de remise article achat"})
     */
    private $achrefamr;

    /**
     * @var string
     *
     * @ORM\Column(name="SURFUN", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Unité de surface"})
     */
    private $surfun;

    /**
     * @var string
     *
     * @ORM\Column(name="COEFPTS", type="decimal", precision=8, scale=3, nullable=false, options={"comment"="Coefficient diviseur fidélité"})
     */
    private $coefpts;

    /**
     * @var string
     *
     * @ORM\Column(name="CONFIGURATEURFORMULAIRE", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Formulaire du configurateur"})
     */
    private $configurateurformulaire;

    /**
     * @var string
     *
     * @ORM\Column(name="CONFIGURATEURCHEMINCOD", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Chemin du configurateur"})
     */
    private $configurateurchemincod;

    /**
     * @var string
     *
     * @ORM\Column(name="SMCFL", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Flag ordonnancement SMC"})
     */
    private $smcfl;

    /**
     * @var string
     *
     * @ORM\Column(name="SMCVISUFL", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Flag visualisation SMC"})
     */
    private $smcvisufl;

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
     * @var \DateTime|null
     *
     * @ORM\Column(name="DOPDH", type="datetime", nullable=true, options={"comment"="Date et heure de dernière opération"})
     */
    private $dopdh;

    /**
     * @var string
     *
     * @ORM\Column(name="CENOTE", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="=2 si note<> 0"})
     */
    private $cenote;

    /**
     * @var string
     *
     * @ORM\Column(name="NOTE", type="decimal", precision=8, scale=0, nullable=false, options={"comment"="Numéro de note"})
     */
    private $note;

    /**
     * @var string
     *
     * @ORM\Column(name="POIN", type="decimal", precision=8, scale=3, nullable=false, options={"comment"="Masse nette"})
     */
    private $poin;

    /**
     * @var string
     *
     * @ORM\Column(name="POIB", type="decimal", precision=8, scale=3, nullable=false, options={"comment"="Poids brut"})
     */
    private $poib;

    /**
     * @var string
     *
     * @ORM\Column(name="GRISAIS", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Saisie en mode grille"})
     */
    private $grisais;

    /**
     * @var string
     *
     * @ORM\Column(name="RESTOTQTE", type="decimal", precision=13, scale=3, nullable=false, options={"comment"="Quantité en réservation client"})
     */
    private $restotqte;

    /**
     * @var string
     *
     * @ORM\Column(name="CDECLQTE", type="decimal", precision=13, scale=3, nullable=false, options={"comment"="Quantité en commande client"})
     */
    private $cdeclqte;

    /**
     * @var string
     *
     * @ORM\Column(name="CDEFOQTE", type="decimal", precision=13, scale=3, nullable=false, options={"comment"="Quantité en commande fournisseur"})
     */
    private $cdefoqte;

    /**
     * @var string
     *
     * @ORM\Column(name="STTOTQTE", type="decimal", precision=13, scale=3, nullable=false, options={"comment"="Quantité en stock cumulée"})
     */
    private $sttotqte;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="INVDT", type="date", nullable=true, options={"comment"="Date d inventaire"})
     */
    private $invdt;

    /**
     * @var string
     *
     * @ORM\Column(name="GARJRNB", type="decimal", precision=4, scale=0, nullable=false, options={"comment"="Durée de garantie"})
     */
    private $garjrnb;

    /**
     * @var string
     *
     * @ORM\Column(name="STCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Code stock / hors stock"})
     */
    private $stcod;

    /**
     * @var string
     *
     * @ORM\Column(name="GICOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Code global / individuel"})
     */
    private $gicod;

    /**
     * @var string
     *
     * @ORM\Column(name="VOL", type="decimal", precision=10, scale=3, nullable=false, options={"comment"="Volume"})
     */
    private $vol;

    /**
     * @var string
     *
     * @ORM\Column(name="DIM_0001", type="decimal", precision=8, scale=3, nullable=false, options={"comment"="Dimensions"})
     */
    private $dim0001;

    /**
     * @var string
     *
     * @ORM\Column(name="DIM_0002", type="decimal", precision=8, scale=3, nullable=false, options={"comment"="Dimensions"})
     */
    private $dim0002;

    /**
     * @var string
     *
     * @ORM\Column(name="DIM_0003", type="decimal", precision=8, scale=3, nullable=false, options={"comment"="Dimensions"})
     */
    private $dim0003;

    /**
     * @var string
     *
     * @ORM\Column(name="SREFCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Gérer sous-référence non/oui"})
     */
    private $srefcod;

    /**
     * @var string
     *
     * @ORM\Column(name="OPSAIS_0001", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Option saisie vente"})
     */
    private $opsais0001;

    /**
     * @var string
     *
     * @ORM\Column(name="OPSAIS_0002", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Option saisie vente"})
     */
    private $opsais0002;

    /**
     * @var string
     *
     * @ORM\Column(name="OPSAIS_0003", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Option saisie vente"})
     */
    private $opsais0003;

    /**
     * @var string
     *
     * @ORM\Column(name="ZONN", type="decimal", precision=16, scale=3, nullable=false, options={"comment"="Zone libre numérique"})
     */
    private $zonn;

    /**
     * @var string
     *
     * @ORM\Column(name="MGTX", type="decimal", precision=3, scale=0, nullable=false, options={"comment"="Taux de marge"})
     */
    private $mgtx;

    /**
     * @var string
     *
     * @ORM\Column(name="PERJRNB", type="decimal", precision=4, scale=0, nullable=false, options={"comment"="Péremption en nombre de jours"})
     */
    private $perjrnb;

    /**
     * @var string
     *
     * @ORM\Column(name="STVALCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Méthode de valorisation du stock"})
     */
    private $stvalcod;

    /**
     * @var string
     *
     * @ORM\Column(name="STSORCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Mode de sortie du stock"})
     */
    private $stsorcod;

    /**
     * @var string
     *
     * @ORM\Column(name="WEBCDECOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Prise de commande par internet ?"})
     */
    private $webcdecod;

    /**
     * @var string
     *
     * @ORM\Column(name="PVCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Code prix de vente"})
     */
    private $pvcod;

    /**
     * @var string
     *
     * @ORM\Column(name="LGTYP", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Type de ligne liée"})
     */
    private $lgtyp;

    /**
     * @var string
     *
     * @ORM\Column(name="STRES", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Réservation du stock ?"})
     */
    private $stres;

    /**
     * @var string
     *
     * @ORM\Column(name="CDEINQTE", type="decimal", precision=13, scale=3, nullable=false, options={"comment"="Quantité en OF"})
     */
    private $cdeinqte;

    /**
     * @var string
     *
     * @ORM\Column(name="CEJOINT", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="CE si fichier joint =2 si joint <> 0"})
     */
    private $cejoint;

    /**
     * @var string
     *
     * @ORM\Column(name="JOINT", type="decimal", precision=8, scale=0, nullable=false, options={"comment"="Numéro fichier joint"})
     */
    private $joint;

    /**
     * @var string
     *
     * @ORM\Column(name="REJALOF", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Autoriser le rejalonnement des OFs"})
     */
    private $rejalof;

    /**
     * @var string
     *
     * @ORM\Column(name="REJALCDE", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Autoriser le rejalonnement des commandes"})
     */
    private $rejalcde;

    /**
     * @var string
     *
     * @ORM\Column(name="REJALOFJRNB", type="decimal", precision=3, scale=0, nullable=false, options={"comment"="Horizon de rejalonnement des OFs"})
     */
    private $rejalofjrnb;

    /**
     * @var string
     *
     * @ORM\Column(name="REJALCDEJRNB", type="decimal", precision=3, scale=0, nullable=false, options={"comment"="Horizon de rejalonnement des commandes"})
     */
    private $rejalcdejrnb;

    /**
     * @var string
     *
     * @ORM\Column(name="TOLERANCETX", type="decimal", precision=5, scale=2, nullable=false, options={"comment"="Taux de tolérance"})
     */
    private $tolerancetx;

    /**
     * @var string
     *
     * @ORM\Column(name="COMSAIS", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Autoriser saisie de commission"})
     */
    private $comsais;

    /**
     * @var string
     *
     * @ORM\Column(name="SURF", type="decimal", precision=8, scale=3, nullable=false, options={"comment"="Surface"})
     */
    private $surf;

    /**
     * @var string
     *
     * @ORM\Column(name="COEFOIVOL", type="decimal", precision=6, scale=3, nullable=false, options={"comment"="Coefficient de foisonnement sur le volume"})
     */
    private $coefoivol;

    /**
     * @var string
     *
     * @ORM\Column(name="MANUN", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Unité de manutention"})
     */
    private $manun;

    /**
     * @var string
     *
     * @ORM\Column(name="STLGTABCCOD", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Type de catégorie ABC"})
     */
    private $stlgtabccod;

    /**
     * @var string
     *
     * @ORM\Column(name="STLGTHGCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Hors gabarit oui/non"})
     */
    private $stlgthgcod;

    /**
     * @var string
     *
     * @ORM\Column(name="STLGTHGCOLINB", type="decimal", precision=6, scale=3, nullable=false, options={"comment"="Nombre de colis nécessaire hors gabarit"})
     */
    private $stlgthgcolinb;

    /**
     * @var string
     *
     * @ORM\Column(name="RANGABC", type="decimal", precision=8, scale=0, nullable=false, options={"comment"="Rang calcul ABC"})
     */
    private $rangabc;

    /**
     * @var string
     *
     * @ORM\Column(name="PDP", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Indicateur PDP Master Product"})
     */
    private $pdp;

    /**
     * @var string
     *
     * @ORM\Column(name="PDPPERCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Période PDP"})
     */
    private $pdppercod;

    /**
     * @var string
     *
     * @ORM\Column(name="PDPDELOBT", type="decimal", precision=3, scale=0, nullable=false, options={"comment"="Délai d obtention PDP"})
     */
    private $pdpdelobt;

    /**
     * @var string
     *
     * @ORM\Column(name="PDPFERMEPERNB", type="decimal", precision=3, scale=0, nullable=false, options={"comment"="Nombre de périodes PDP ferme"})
     */
    private $pdpfermepernb;

    /**
     * @var string
     *
     * @ORM\Column(name="PDPNEGOPERNB", type="decimal", precision=3, scale=0, nullable=false, options={"comment"="Nombre de périodes PDP négociable"})
     */
    private $pdpnegopernb;

    /**
     * @var string
     *
     * @ORM\Column(name="PDPLOTQTE", type="decimal", precision=13, scale=3, nullable=false, options={"comment"="Taille de lot PDP"})
     */
    private $pdplotqte;

    /**
     * @var string
     *
     * @ORM\Column(name="PDPREGPERNB", type="decimal", precision=3, scale=0, nullable=false, options={"comment"="Nombre de périodes PDP regroupement"})
     */
    private $pdpregpernb;

    /**
     * @var string
     *
     * @ORM\Column(name="PDPECARTMAX", type="decimal", precision=4, scale=0, nullable=false, options={"comment"="Taux d écart maximum en % pour message PDP"})
     */
    private $pdpecartmax;

    /**
     * @var string
     *
     * @ORM\Column(name="TVAART", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Régime TVA article"})
     */
    private $tvaart;

    /**
     * @var string
     *
     * @ORM\Column(name="TVAARTA", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Régime TVA article achat"})
     */
    private $tvaarta;

    /**
     * @var string
     *
     * @ORM\Column(name="WMQTEIMP", type="decimal", precision=9, scale=0, nullable=false, options={"comment"="Quantité seuil méthode de réservation en volume important"})
     */
    private $wmqteimp;

    /**
     * @var string
     *
     * @ORM\Column(name="WMREAPFROIDFL", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Flag réapprovisionnement à froid autorisé"})
     */
    private $wmreapfroidfl;

    /**
     * @var string
     *
     * @ORM\Column(name="SREF1", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Sous-référence 1"})
     */
    private $sref1;

    /**
     * @var string
     *
     * @ORM\Column(name="SREF2", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Sous-référence 2"})
     */
    private $sref2;

    /**
     * @var string
     *
     * @ORM\Column(name="PREFDVNO", type="string", length=10, nullable=false, options={"fixed"=true,"comment"="Préfixe numéro de devis"})
     */
    private $prefdvno;

    /**
     * @var string
     *
     * @ORM\Column(name="DVNO", type="decimal", precision=10, scale=0, nullable=false, options={"comment"="Devis no"})
     */
    private $dvno;

    /**
     * @var string
     *
     * @ORM\Column(name="DTIND", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Indice gamme devis technique"})
     */
    private $dtind;

    /**
     * @var string
     *
     * @ORM\Column(name="TIERSCLI", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Code client associé au code prospect"})
     */
    private $tierscli;

    /**
     * @var string
     *
     * @ORM\Column(name="REVUART", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Indicateur article en revue"})
     */
    private $revuart;

    /**
     * @var string
     *
     * @ORM\Column(name="ICPFL", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Element synchronisé en inter-compagnies"})
     */
    private $icpfl;

    /**
     * @var string
     *
     * @ORM\Column(name="BUDGETCOD", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Code budget"})
     */
    private $budgetcod;

    /**
     * @var string
     *
     * @ORM\Column(name="TRAVUN", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Unité de travail logistique"})
     */
    private $travun;

    /**
     * @var string
     *
     * @ORM\Column(name="PRIXMOY", type="decimal", precision=13, scale=4, nullable=false, options={"comment"="Prix moyen"})
     */
    private $prixmoy;

    /**
     * @var string
     *
     * @ORM\Column(name="CONTRATCOD", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Code contrat modèle"})
     */
    private $contratcod;

    /**
     * @var string
     *
     * @ORM\Column(name="CONTRATFAM", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Famille contrat"})
     */
    private $contratfam;

    /**
     * @var string
     *
     * @ORM\Column(name="EANAUTOFL", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="EAN généré automatiquement"})
     */
    private $eanautofl;

    /**
     * @var string
     *
     * @ORM\Column(name="TYPEARTCOD", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Code type article"})
     */
    private $typeartcod;

    /**
     * @var string
     *
     * @ORM\Column(name="RESJRNB", type="decimal", precision=3, scale=0, nullable=false, options={"comment"="Nombre de jours horizon réservation"})
     */
    private $resjrnb;

    /**
     * @var string
     *
     * @ORM\Column(name="MARCHE", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Code marché"})
     */
    private $marche;

    /**
     * @var string
     *
     * @ORM\Column(name="QTE", type="decimal", precision=12, scale=3, nullable=false, options={"comment"="Quantité"})
     */
    private $qte;

    /**
     * @var string
     *
     * @ORM\Column(name="TIERSREF", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Code fournisseur pour tarif de référence"})
     */
    private $tiersref;

    /**
     * @var string
     *
     * @ORM\Column(name="EPHEMEREFL", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Article éphémère"})
     */
    private $ephemerefl;

    /**
     * @var string
     *
     * @ORM\Column(name="ACHUNREF", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Unité d achat pour tarif de référence"})
     */
    private $achunref;

    /**
     * @var string
     *
     * @ORM\Column(name="QTEMIN", type="decimal", precision=12, scale=3, nullable=false, options={"comment"="Quantité minimale"})
     */
    private $qtemin;

    /**
     * @var string
     *
     * @ORM\Column(name="QTEPAR", type="decimal", precision=8, scale=0, nullable=false, options={"comment"="Quantité multiple de"})
     */
    private $qtepar;

    /**
     * @var string
     *
     * @ORM\Column(name="CPTI", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Compte d immobilisation"})
     */
    private $cpti;

    /**
     * @var string
     *
     * @ORM\Column(name="VISA_0001", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Visa"})
     */
    private $visa0001;

    /**
     * @var string
     *
     * @ORM\Column(name="VISA_0002", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Visa"})
     */
    private $visa0002;

    /**
     * @var string
     *
     * @ORM\Column(name="VISA_0003", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Visa"})
     */
    private $visa0003;

    /**
     * @var string
     *
     * @ORM\Column(name="VISA_0004", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Visa"})
     */
    private $visa0004;

    /**
     * @var string
     *
     * @ORM\Column(name="VISA_0005", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Visa"})
     */
    private $visa0005;

    /**
     * @var int
     *
     * @ORM\Column(name="ART_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $artId;

    /**
     * @ORM\ManyToMany(targetEntity=Decisionnel::class, mappedBy="articles")
     */
    private $decisionnels;

    public function __construct()
    {
        $this->decisionnels = new ArrayCollection();
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

    public function getRef(): ?string
    {
        return $this->ref;
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

    public function getDes(): ?string
    {
        return $this->des;
    }

    public function getDesabr(): ?string
    {
        return $this->desabr;
    }

    public function getEan(): ?string
    {
        return $this->ean;
    }

    public function getTiers(): ?string
    {
        return $this->tiers;
    }

    public function getTaref(): ?string
    {
        return $this->taref;
    }

    public function getRefrpl(): ?string
    {
        return $this->refrpl;
    }

    public function getTafamrx(): ?string
    {
        return $this->tafamrx;
    }

    public function getTafamr(): ?string
    {
        return $this->tafamr;
    }

    public function getRefamrx(): ?string
    {
        return $this->refamrx;
    }

    public function getRefamr(): ?string
    {
        return $this->refamr;
    }

    public function getCofamr(): ?string
    {
        return $this->cofamr;
    }

    public function getFam0001(): ?string
    {
        return $this->fam0001;
    }

    public function getFam0002(): ?string
    {
        return $this->fam0002;
    }

    public function getFam0003(): ?string
    {
        return $this->fam0003;
    }

    public function getProdnat(): ?string
    {
        return $this->prodnat;
    }

    public function getRefun(): ?string
    {
        return $this->refun;
    }

    public function getAchun(): ?string
    {
        return $this->achun;
    }

    public function getStun(): ?string
    {
        return $this->stun;
    }

    public function getVenun(): ?string
    {
        return $this->venun;
    }

    public function getPoiun(): ?string
    {
        return $this->poiun;
    }

    public function getVolun(): ?string
    {
        return $this->volun;
    }

    public function getDimun(): ?string
    {
        return $this->dimun;
    }

    public function getCptv(): ?string
    {
        return $this->cptv;
    }

    public function getCpta(): ?string
    {
        return $this->cpta;
    }

    public function getCpts(): ?string
    {
        return $this->cpts;
    }

    public function getTpfr0001(): ?string
    {
        return $this->tpfr0001;
    }

    public function getTpfr0002(): ?string
    {
        return $this->tpfr0002;
    }

    public function getTpfr0003(): ?string
    {
        return $this->tpfr0003;
    }

    public function getTvanom(): ?string
    {
        return $this->tvanom;
    }

    public function getTvaun(): ?string
    {
        return $this->tvaun;
    }

    public function getTvargcod(): ?string
    {
        return $this->tvargcod;
    }

    public function getEdcod(): ?string
    {
        return $this->edcod;
    }

    public function getGricod(): ?string
    {
        return $this->gricod;
    }

    public function getZona(): ?string
    {
        return $this->zona;
    }

    public function getMedia(): ?string
    {
        return $this->media;
    }

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function getAxemsk(): ?string
    {
        return $this->axemsk;
    }

    public function getAxeno(): ?string
    {
        return $this->axeno;
    }

    public function getAbccod(): ?string
    {
        return $this->abccod;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function getCbngescod(): ?string
    {
        return $this->cbngescod;
    }

    public function getCompetcod(): ?string
    {
        return $this->competcod;
    }

    public function getCptaces(): ?string
    {
        return $this->cptaces;
    }

    public function getCptvces(): ?string
    {
        return $this->cptvces;
    }

    public function getAchtafamrx(): ?string
    {
        return $this->achtafamrx;
    }

    public function getAchtafamr(): ?string
    {
        return $this->achtafamr;
    }

    public function getAchrefamrx(): ?string
    {
        return $this->achrefamrx;
    }

    public function getAchrefamr(): ?string
    {
        return $this->achrefamr;
    }

    public function getSurfun(): ?string
    {
        return $this->surfun;
    }

    public function getCoefpts(): ?string
    {
        return $this->coefpts;
    }

    public function getConfigurateurformulaire(): ?string
    {
        return $this->configurateurformulaire;
    }

    public function getConfigurateurchemincod(): ?string
    {
        return $this->configurateurchemincod;
    }

    public function getSmcfl(): ?string
    {
        return $this->smcfl;
    }

    public function getSmcvisufl(): ?string
    {
        return $this->smcvisufl;
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

    public function getDopdh(): ?\DateTimeInterface
    {
        return $this->dopdh;
    }

    public function getCenote(): ?string
    {
        return $this->cenote;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function getPoin(): ?string
    {
        return $this->poin;
    }

    public function getPoib(): ?string
    {
        return $this->poib;
    }

    public function getGrisais(): ?string
    {
        return $this->grisais;
    }

    public function getRestotqte(): ?string
    {
        return $this->restotqte;
    }

    public function getCdeclqte(): ?string
    {
        return $this->cdeclqte;
    }

    public function getCdefoqte(): ?string
    {
        return $this->cdefoqte;
    }

    public function getSttotqte(): ?string
    {
        return $this->sttotqte;
    }

    public function getInvdt(): ?\DateTimeInterface
    {
        return $this->invdt;
    }

    public function getGarjrnb(): ?string
    {
        return $this->garjrnb;
    }

    public function getStcod(): ?string
    {
        return $this->stcod;
    }

    public function getGicod(): ?string
    {
        return $this->gicod;
    }

    public function getVol(): ?string
    {
        return $this->vol;
    }

    public function getDim0001(): ?string
    {
        return $this->dim0001;
    }

    public function getDim0002(): ?string
    {
        return $this->dim0002;
    }

    public function getDim0003(): ?string
    {
        return $this->dim0003;
    }

    public function getSrefcod(): ?string
    {
        return $this->srefcod;
    }

    public function getOpsais0001(): ?string
    {
        return $this->opsais0001;
    }

    public function getOpsais0002(): ?string
    {
        return $this->opsais0002;
    }

    public function getOpsais0003(): ?string
    {
        return $this->opsais0003;
    }

    public function getZonn(): ?string
    {
        return $this->zonn;
    }

    public function getMgtx(): ?string
    {
        return $this->mgtx;
    }

    public function getPerjrnb(): ?string
    {
        return $this->perjrnb;
    }

    public function getStvalcod(): ?string
    {
        return $this->stvalcod;
    }

    public function getStsorcod(): ?string
    {
        return $this->stsorcod;
    }

    public function getWebcdecod(): ?string
    {
        return $this->webcdecod;
    }

    public function getPvcod(): ?string
    {
        return $this->pvcod;
    }

    public function getLgtyp(): ?string
    {
        return $this->lgtyp;
    }

    public function getStres(): ?string
    {
        return $this->stres;
    }

    public function getCdeinqte(): ?string
    {
        return $this->cdeinqte;
    }

    public function getCejoint(): ?string
    {
        return $this->cejoint;
    }

    public function getJoint(): ?string
    {
        return $this->joint;
    }

    public function getRejalof(): ?string
    {
        return $this->rejalof;
    }

    public function getRejalcde(): ?string
    {
        return $this->rejalcde;
    }

    public function getRejalofjrnb(): ?string
    {
        return $this->rejalofjrnb;
    }

    public function getRejalcdejrnb(): ?string
    {
        return $this->rejalcdejrnb;
    }

    public function getTolerancetx(): ?string
    {
        return $this->tolerancetx;
    }

    public function getComsais(): ?string
    {
        return $this->comsais;
    }

    public function getSurf(): ?string
    {
        return $this->surf;
    }

    public function getCoefoivol(): ?string
    {
        return $this->coefoivol;
    }

    public function getManun(): ?string
    {
        return $this->manun;
    }

    public function getStlgtabccod(): ?string
    {
        return $this->stlgtabccod;
    }

    public function getStlgthgcod(): ?string
    {
        return $this->stlgthgcod;
    }

    public function getStlgthgcolinb(): ?string
    {
        return $this->stlgthgcolinb;
    }

    public function getRangabc(): ?string
    {
        return $this->rangabc;
    }

    public function getPdp(): ?string
    {
        return $this->pdp;
    }

    public function getPdppercod(): ?string
    {
        return $this->pdppercod;
    }

    public function getPdpdelobt(): ?string
    {
        return $this->pdpdelobt;
    }

    public function getPdpfermepernb(): ?string
    {
        return $this->pdpfermepernb;
    }

    public function getPdpnegopernb(): ?string
    {
        return $this->pdpnegopernb;
    }

    public function getPdplotqte(): ?string
    {
        return $this->pdplotqte;
    }

    public function getPdpregpernb(): ?string
    {
        return $this->pdpregpernb;
    }

    public function getPdpecartmax(): ?string
    {
        return $this->pdpecartmax;
    }

    public function getTvaart(): ?string
    {
        return $this->tvaart;
    }

    public function getTvaarta(): ?string
    {
        return $this->tvaarta;
    }

    public function getWmqteimp(): ?string
    {
        return $this->wmqteimp;
    }

    public function getWmreapfroidfl(): ?string
    {
        return $this->wmreapfroidfl;
    }

    public function getSref1(): ?string
    {
        return $this->sref1;
    }

    public function getSref2(): ?string
    {
        return $this->sref2;
    }

    public function getPrefdvno(): ?string
    {
        return $this->prefdvno;
    }

    public function getDvno(): ?string
    {
        return $this->dvno;
    }

    public function getDtind(): ?string
    {
        return $this->dtind;
    }

    public function getTierscli(): ?string
    {
        return $this->tierscli;
    }

    public function getRevuart(): ?string
    {
        return $this->revuart;
    }

    public function getIcpfl(): ?string
    {
        return $this->icpfl;
    }

    public function getBudgetcod(): ?string
    {
        return $this->budgetcod;
    }

    public function getTravun(): ?string
    {
        return $this->travun;
    }

    public function getPrixmoy(): ?string
    {
        return $this->prixmoy;
    }

    public function getContratcod(): ?string
    {
        return $this->contratcod;
    }

    public function getContratfam(): ?string
    {
        return $this->contratfam;
    }

    public function getEanautofl(): ?string
    {
        return $this->eanautofl;
    }

    public function getTypeartcod(): ?string
    {
        return $this->typeartcod;
    }

    public function getResjrnb(): ?string
    {
        return $this->resjrnb;
    }

    public function getMarche(): ?string
    {
        return $this->marche;
    }

    public function getQte(): ?string
    {
        return $this->qte;
    }

    public function getTiersref(): ?string
    {
        return $this->tiersref;
    }

    public function getEphemerefl(): ?string
    {
        return $this->ephemerefl;
    }

    public function getAchunref(): ?string
    {
        return $this->achunref;
    }

    public function getQtemin(): ?string
    {
        return $this->qtemin;
    }

    public function getQtepar(): ?string
    {
        return $this->qtepar;
    }

    public function getCpti(): ?string
    {
        return $this->cpti;
    }

    public function getVisa0001(): ?string
    {
        return $this->visa0001;
    }

    public function getVisa0002(): ?string
    {
        return $this->visa0002;
    }

    public function getVisa0003(): ?string
    {
        return $this->visa0003;
    }

    public function getVisa0004(): ?string
    {
        return $this->visa0004;
    }

    public function getVisa0005(): ?string
    {
        return $this->visa0005;
    }

    public function getArtId(): ?int
    {
        return $this->artId;
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
            $decisionnel->addArticle($this);
        }

        return $this;
    }

    public function removeDecisionnel(Decisionnel $decisionnel): self
    {
        if ($this->decisionnels->removeElement($decisionnel)) {
            $decisionnel->removeArticle($this);
        }

        return $this;
    }


}
