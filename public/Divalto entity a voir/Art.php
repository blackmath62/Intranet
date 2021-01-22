<?php

namespace App\Entity\Divalto;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ArtRepository;

/**
 * Art
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

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(string $ref): self
    {
        $this->ref = $ref;

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

    public function getDes(): ?string
    {
        return $this->des;
    }

    public function setDes(string $des): self
    {
        $this->des = $des;

        return $this;
    }

    public function getDesabr(): ?string
    {
        return $this->desabr;
    }

    public function setDesabr(string $desabr): self
    {
        $this->desabr = $desabr;

        return $this;
    }

    public function getEan(): ?string
    {
        return $this->ean;
    }

    public function setEan(string $ean): self
    {
        $this->ean = $ean;

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

    public function getTaref(): ?string
    {
        return $this->taref;
    }

    public function setTaref(string $taref): self
    {
        $this->taref = $taref;

        return $this;
    }

    public function getRefrpl(): ?string
    {
        return $this->refrpl;
    }

    public function setRefrpl(string $refrpl): self
    {
        $this->refrpl = $refrpl;

        return $this;
    }

    public function getTafamrx(): ?string
    {
        return $this->tafamrx;
    }

    public function setTafamrx(string $tafamrx): self
    {
        $this->tafamrx = $tafamrx;

        return $this;
    }

    public function getTafamr(): ?string
    {
        return $this->tafamr;
    }

    public function setTafamr(string $tafamr): self
    {
        $this->tafamr = $tafamr;

        return $this;
    }

    public function getRefamrx(): ?string
    {
        return $this->refamrx;
    }

    public function setRefamrx(string $refamrx): self
    {
        $this->refamrx = $refamrx;

        return $this;
    }

    public function getRefamr(): ?string
    {
        return $this->refamr;
    }

    public function setRefamr(string $refamr): self
    {
        $this->refamr = $refamr;

        return $this;
    }

    public function getCofamr(): ?string
    {
        return $this->cofamr;
    }

    public function setCofamr(string $cofamr): self
    {
        $this->cofamr = $cofamr;

        return $this;
    }

    public function getFam0001(): ?string
    {
        return $this->fam0001;
    }

    public function setFam0001(string $fam0001): self
    {
        $this->fam0001 = $fam0001;

        return $this;
    }

    public function getFam0002(): ?string
    {
        return $this->fam0002;
    }

    public function setFam0002(string $fam0002): self
    {
        $this->fam0002 = $fam0002;

        return $this;
    }

    public function getFam0003(): ?string
    {
        return $this->fam0003;
    }

    public function setFam0003(string $fam0003): self
    {
        $this->fam0003 = $fam0003;

        return $this;
    }

    public function getProdnat(): ?string
    {
        return $this->prodnat;
    }

    public function setProdnat(string $prodnat): self
    {
        $this->prodnat = $prodnat;

        return $this;
    }

    public function getRefun(): ?string
    {
        return $this->refun;
    }

    public function setRefun(string $refun): self
    {
        $this->refun = $refun;

        return $this;
    }

    public function getAchun(): ?string
    {
        return $this->achun;
    }

    public function setAchun(string $achun): self
    {
        $this->achun = $achun;

        return $this;
    }

    public function getStun(): ?string
    {
        return $this->stun;
    }

    public function setStun(string $stun): self
    {
        $this->stun = $stun;

        return $this;
    }

    public function getVenun(): ?string
    {
        return $this->venun;
    }

    public function setVenun(string $venun): self
    {
        $this->venun = $venun;

        return $this;
    }

    public function getPoiun(): ?string
    {
        return $this->poiun;
    }

    public function setPoiun(string $poiun): self
    {
        $this->poiun = $poiun;

        return $this;
    }

    public function getVolun(): ?string
    {
        return $this->volun;
    }

    public function setVolun(string $volun): self
    {
        $this->volun = $volun;

        return $this;
    }

    public function getDimun(): ?string
    {
        return $this->dimun;
    }

    public function setDimun(string $dimun): self
    {
        $this->dimun = $dimun;

        return $this;
    }

    public function getCptv(): ?string
    {
        return $this->cptv;
    }

    public function setCptv(string $cptv): self
    {
        $this->cptv = $cptv;

        return $this;
    }

    public function getCpta(): ?string
    {
        return $this->cpta;
    }

    public function setCpta(string $cpta): self
    {
        $this->cpta = $cpta;

        return $this;
    }

    public function getCpts(): ?string
    {
        return $this->cpts;
    }

    public function setCpts(string $cpts): self
    {
        $this->cpts = $cpts;

        return $this;
    }

    public function getTpfr0001(): ?string
    {
        return $this->tpfr0001;
    }

    public function setTpfr0001(string $tpfr0001): self
    {
        $this->tpfr0001 = $tpfr0001;

        return $this;
    }

    public function getTpfr0002(): ?string
    {
        return $this->tpfr0002;
    }

    public function setTpfr0002(string $tpfr0002): self
    {
        $this->tpfr0002 = $tpfr0002;

        return $this;
    }

    public function getTpfr0003(): ?string
    {
        return $this->tpfr0003;
    }

    public function setTpfr0003(string $tpfr0003): self
    {
        $this->tpfr0003 = $tpfr0003;

        return $this;
    }

    public function getTvanom(): ?string
    {
        return $this->tvanom;
    }

    public function setTvanom(string $tvanom): self
    {
        $this->tvanom = $tvanom;

        return $this;
    }

    public function getTvaun(): ?string
    {
        return $this->tvaun;
    }

    public function setTvaun(string $tvaun): self
    {
        $this->tvaun = $tvaun;

        return $this;
    }

    public function getTvargcod(): ?string
    {
        return $this->tvargcod;
    }

    public function setTvargcod(string $tvargcod): self
    {
        $this->tvargcod = $tvargcod;

        return $this;
    }

    public function getEdcod(): ?string
    {
        return $this->edcod;
    }

    public function setEdcod(string $edcod): self
    {
        $this->edcod = $edcod;

        return $this;
    }

    public function getGricod(): ?string
    {
        return $this->gricod;
    }

    public function setGricod(string $gricod): self
    {
        $this->gricod = $gricod;

        return $this;
    }

    public function getZona(): ?string
    {
        return $this->zona;
    }

    public function setZona(string $zona): self
    {
        $this->zona = $zona;

        return $this;
    }

    public function getMedia(): ?string
    {
        return $this->media;
    }

    public function setMedia(string $media): self
    {
        $this->media = $media;

        return $this;
    }

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function setHtml(string $html): self
    {
        $this->html = $html;

        return $this;
    }

    public function getAxemsk(): ?string
    {
        return $this->axemsk;
    }

    public function setAxemsk(string $axemsk): self
    {
        $this->axemsk = $axemsk;

        return $this;
    }

    public function getAxeno(): ?string
    {
        return $this->axeno;
    }

    public function setAxeno(string $axeno): self
    {
        $this->axeno = $axeno;

        return $this;
    }

    public function getAbccod(): ?string
    {
        return $this->abccod;
    }

    public function setAbccod(string $abccod): self
    {
        $this->abccod = $abccod;

        return $this;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getCbngescod(): ?string
    {
        return $this->cbngescod;
    }

    public function setCbngescod(string $cbngescod): self
    {
        $this->cbngescod = $cbngescod;

        return $this;
    }

    public function getCompetcod(): ?string
    {
        return $this->competcod;
    }

    public function setCompetcod(string $competcod): self
    {
        $this->competcod = $competcod;

        return $this;
    }

    public function getCptaces(): ?string
    {
        return $this->cptaces;
    }

    public function setCptaces(string $cptaces): self
    {
        $this->cptaces = $cptaces;

        return $this;
    }

    public function getCptvces(): ?string
    {
        return $this->cptvces;
    }

    public function setCptvces(string $cptvces): self
    {
        $this->cptvces = $cptvces;

        return $this;
    }

    public function getAchtafamrx(): ?string
    {
        return $this->achtafamrx;
    }

    public function setAchtafamrx(string $achtafamrx): self
    {
        $this->achtafamrx = $achtafamrx;

        return $this;
    }

    public function getAchtafamr(): ?string
    {
        return $this->achtafamr;
    }

    public function setAchtafamr(string $achtafamr): self
    {
        $this->achtafamr = $achtafamr;

        return $this;
    }

    public function getAchrefamrx(): ?string
    {
        return $this->achrefamrx;
    }

    public function setAchrefamrx(string $achrefamrx): self
    {
        $this->achrefamrx = $achrefamrx;

        return $this;
    }

    public function getAchrefamr(): ?string
    {
        return $this->achrefamr;
    }

    public function setAchrefamr(string $achrefamr): self
    {
        $this->achrefamr = $achrefamr;

        return $this;
    }

    public function getSurfun(): ?string
    {
        return $this->surfun;
    }

    public function setSurfun(string $surfun): self
    {
        $this->surfun = $surfun;

        return $this;
    }

    public function getCoefpts(): ?string
    {
        return $this->coefpts;
    }

    public function setCoefpts(string $coefpts): self
    {
        $this->coefpts = $coefpts;

        return $this;
    }

    public function getConfigurateurformulaire(): ?string
    {
        return $this->configurateurformulaire;
    }

    public function setConfigurateurformulaire(string $configurateurformulaire): self
    {
        $this->configurateurformulaire = $configurateurformulaire;

        return $this;
    }

    public function getConfigurateurchemincod(): ?string
    {
        return $this->configurateurchemincod;
    }

    public function setConfigurateurchemincod(string $configurateurchemincod): self
    {
        $this->configurateurchemincod = $configurateurchemincod;

        return $this;
    }

    public function getSmcfl(): ?string
    {
        return $this->smcfl;
    }

    public function setSmcfl(string $smcfl): self
    {
        $this->smcfl = $smcfl;

        return $this;
    }

    public function getSmcvisufl(): ?string
    {
        return $this->smcvisufl;
    }

    public function setSmcvisufl(string $smcvisufl): self
    {
        $this->smcvisufl = $smcvisufl;

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

    public function getDopdh(): ?\DateTimeInterface
    {
        return $this->dopdh;
    }

    public function setDopdh(?\DateTimeInterface $dopdh): self
    {
        $this->dopdh = $dopdh;

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

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getPoin(): ?string
    {
        return $this->poin;
    }

    public function setPoin(string $poin): self
    {
        $this->poin = $poin;

        return $this;
    }

    public function getPoib(): ?string
    {
        return $this->poib;
    }

    public function setPoib(string $poib): self
    {
        $this->poib = $poib;

        return $this;
    }

    public function getGrisais(): ?string
    {
        return $this->grisais;
    }

    public function setGrisais(string $grisais): self
    {
        $this->grisais = $grisais;

        return $this;
    }

    public function getRestotqte(): ?string
    {
        return $this->restotqte;
    }

    public function setRestotqte(string $restotqte): self
    {
        $this->restotqte = $restotqte;

        return $this;
    }

    public function getCdeclqte(): ?string
    {
        return $this->cdeclqte;
    }

    public function setCdeclqte(string $cdeclqte): self
    {
        $this->cdeclqte = $cdeclqte;

        return $this;
    }

    public function getCdefoqte(): ?string
    {
        return $this->cdefoqte;
    }

    public function setCdefoqte(string $cdefoqte): self
    {
        $this->cdefoqte = $cdefoqte;

        return $this;
    }

    public function getSttotqte(): ?string
    {
        return $this->sttotqte;
    }

    public function setSttotqte(string $sttotqte): self
    {
        $this->sttotqte = $sttotqte;

        return $this;
    }

    public function getInvdt(): ?\DateTimeInterface
    {
        return $this->invdt;
    }

    public function setInvdt(?\DateTimeInterface $invdt): self
    {
        $this->invdt = $invdt;

        return $this;
    }

    public function getGarjrnb(): ?string
    {
        return $this->garjrnb;
    }

    public function setGarjrnb(string $garjrnb): self
    {
        $this->garjrnb = $garjrnb;

        return $this;
    }

    public function getStcod(): ?string
    {
        return $this->stcod;
    }

    public function setStcod(string $stcod): self
    {
        $this->stcod = $stcod;

        return $this;
    }

    public function getGicod(): ?string
    {
        return $this->gicod;
    }

    public function setGicod(string $gicod): self
    {
        $this->gicod = $gicod;

        return $this;
    }

    public function getVol(): ?string
    {
        return $this->vol;
    }

    public function setVol(string $vol): self
    {
        $this->vol = $vol;

        return $this;
    }

    public function getDim0001(): ?string
    {
        return $this->dim0001;
    }

    public function setDim0001(string $dim0001): self
    {
        $this->dim0001 = $dim0001;

        return $this;
    }

    public function getDim0002(): ?string
    {
        return $this->dim0002;
    }

    public function setDim0002(string $dim0002): self
    {
        $this->dim0002 = $dim0002;

        return $this;
    }

    public function getDim0003(): ?string
    {
        return $this->dim0003;
    }

    public function setDim0003(string $dim0003): self
    {
        $this->dim0003 = $dim0003;

        return $this;
    }

    public function getSrefcod(): ?string
    {
        return $this->srefcod;
    }

    public function setSrefcod(string $srefcod): self
    {
        $this->srefcod = $srefcod;

        return $this;
    }

    public function getOpsais0001(): ?string
    {
        return $this->opsais0001;
    }

    public function setOpsais0001(string $opsais0001): self
    {
        $this->opsais0001 = $opsais0001;

        return $this;
    }

    public function getOpsais0002(): ?string
    {
        return $this->opsais0002;
    }

    public function setOpsais0002(string $opsais0002): self
    {
        $this->opsais0002 = $opsais0002;

        return $this;
    }

    public function getOpsais0003(): ?string
    {
        return $this->opsais0003;
    }

    public function setOpsais0003(string $opsais0003): self
    {
        $this->opsais0003 = $opsais0003;

        return $this;
    }

    public function getZonn(): ?string
    {
        return $this->zonn;
    }

    public function setZonn(string $zonn): self
    {
        $this->zonn = $zonn;

        return $this;
    }

    public function getMgtx(): ?string
    {
        return $this->mgtx;
    }

    public function setMgtx(string $mgtx): self
    {
        $this->mgtx = $mgtx;

        return $this;
    }

    public function getPerjrnb(): ?string
    {
        return $this->perjrnb;
    }

    public function setPerjrnb(string $perjrnb): self
    {
        $this->perjrnb = $perjrnb;

        return $this;
    }

    public function getStvalcod(): ?string
    {
        return $this->stvalcod;
    }

    public function setStvalcod(string $stvalcod): self
    {
        $this->stvalcod = $stvalcod;

        return $this;
    }

    public function getStsorcod(): ?string
    {
        return $this->stsorcod;
    }

    public function setStsorcod(string $stsorcod): self
    {
        $this->stsorcod = $stsorcod;

        return $this;
    }

    public function getWebcdecod(): ?string
    {
        return $this->webcdecod;
    }

    public function setWebcdecod(string $webcdecod): self
    {
        $this->webcdecod = $webcdecod;

        return $this;
    }

    public function getPvcod(): ?string
    {
        return $this->pvcod;
    }

    public function setPvcod(string $pvcod): self
    {
        $this->pvcod = $pvcod;

        return $this;
    }

    public function getLgtyp(): ?string
    {
        return $this->lgtyp;
    }

    public function setLgtyp(string $lgtyp): self
    {
        $this->lgtyp = $lgtyp;

        return $this;
    }

    public function getStres(): ?string
    {
        return $this->stres;
    }

    public function setStres(string $stres): self
    {
        $this->stres = $stres;

        return $this;
    }

    public function getCdeinqte(): ?string
    {
        return $this->cdeinqte;
    }

    public function setCdeinqte(string $cdeinqte): self
    {
        $this->cdeinqte = $cdeinqte;

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

    public function getJoint(): ?string
    {
        return $this->joint;
    }

    public function setJoint(string $joint): self
    {
        $this->joint = $joint;

        return $this;
    }

    public function getRejalof(): ?string
    {
        return $this->rejalof;
    }

    public function setRejalof(string $rejalof): self
    {
        $this->rejalof = $rejalof;

        return $this;
    }

    public function getRejalcde(): ?string
    {
        return $this->rejalcde;
    }

    public function setRejalcde(string $rejalcde): self
    {
        $this->rejalcde = $rejalcde;

        return $this;
    }

    public function getRejalofjrnb(): ?string
    {
        return $this->rejalofjrnb;
    }

    public function setRejalofjrnb(string $rejalofjrnb): self
    {
        $this->rejalofjrnb = $rejalofjrnb;

        return $this;
    }

    public function getRejalcdejrnb(): ?string
    {
        return $this->rejalcdejrnb;
    }

    public function setRejalcdejrnb(string $rejalcdejrnb): self
    {
        $this->rejalcdejrnb = $rejalcdejrnb;

        return $this;
    }

    public function getTolerancetx(): ?string
    {
        return $this->tolerancetx;
    }

    public function setTolerancetx(string $tolerancetx): self
    {
        $this->tolerancetx = $tolerancetx;

        return $this;
    }

    public function getComsais(): ?string
    {
        return $this->comsais;
    }

    public function setComsais(string $comsais): self
    {
        $this->comsais = $comsais;

        return $this;
    }

    public function getSurf(): ?string
    {
        return $this->surf;
    }

    public function setSurf(string $surf): self
    {
        $this->surf = $surf;

        return $this;
    }

    public function getCoefoivol(): ?string
    {
        return $this->coefoivol;
    }

    public function setCoefoivol(string $coefoivol): self
    {
        $this->coefoivol = $coefoivol;

        return $this;
    }

    public function getManun(): ?string
    {
        return $this->manun;
    }

    public function setManun(string $manun): self
    {
        $this->manun = $manun;

        return $this;
    }

    public function getStlgtabccod(): ?string
    {
        return $this->stlgtabccod;
    }

    public function setStlgtabccod(string $stlgtabccod): self
    {
        $this->stlgtabccod = $stlgtabccod;

        return $this;
    }

    public function getStlgthgcod(): ?string
    {
        return $this->stlgthgcod;
    }

    public function setStlgthgcod(string $stlgthgcod): self
    {
        $this->stlgthgcod = $stlgthgcod;

        return $this;
    }

    public function getStlgthgcolinb(): ?string
    {
        return $this->stlgthgcolinb;
    }

    public function setStlgthgcolinb(string $stlgthgcolinb): self
    {
        $this->stlgthgcolinb = $stlgthgcolinb;

        return $this;
    }

    public function getRangabc(): ?string
    {
        return $this->rangabc;
    }

    public function setRangabc(string $rangabc): self
    {
        $this->rangabc = $rangabc;

        return $this;
    }

    public function getPdp(): ?string
    {
        return $this->pdp;
    }

    public function setPdp(string $pdp): self
    {
        $this->pdp = $pdp;

        return $this;
    }

    public function getPdppercod(): ?string
    {
        return $this->pdppercod;
    }

    public function setPdppercod(string $pdppercod): self
    {
        $this->pdppercod = $pdppercod;

        return $this;
    }

    public function getPdpdelobt(): ?string
    {
        return $this->pdpdelobt;
    }

    public function setPdpdelobt(string $pdpdelobt): self
    {
        $this->pdpdelobt = $pdpdelobt;

        return $this;
    }

    public function getPdpfermepernb(): ?string
    {
        return $this->pdpfermepernb;
    }

    public function setPdpfermepernb(string $pdpfermepernb): self
    {
        $this->pdpfermepernb = $pdpfermepernb;

        return $this;
    }

    public function getPdpnegopernb(): ?string
    {
        return $this->pdpnegopernb;
    }

    public function setPdpnegopernb(string $pdpnegopernb): self
    {
        $this->pdpnegopernb = $pdpnegopernb;

        return $this;
    }

    public function getPdplotqte(): ?string
    {
        return $this->pdplotqte;
    }

    public function setPdplotqte(string $pdplotqte): self
    {
        $this->pdplotqte = $pdplotqte;

        return $this;
    }

    public function getPdpregpernb(): ?string
    {
        return $this->pdpregpernb;
    }

    public function setPdpregpernb(string $pdpregpernb): self
    {
        $this->pdpregpernb = $pdpregpernb;

        return $this;
    }

    public function getPdpecartmax(): ?string
    {
        return $this->pdpecartmax;
    }

    public function setPdpecartmax(string $pdpecartmax): self
    {
        $this->pdpecartmax = $pdpecartmax;

        return $this;
    }

    public function getTvaart(): ?string
    {
        return $this->tvaart;
    }

    public function setTvaart(string $tvaart): self
    {
        $this->tvaart = $tvaart;

        return $this;
    }

    public function getTvaarta(): ?string
    {
        return $this->tvaarta;
    }

    public function setTvaarta(string $tvaarta): self
    {
        $this->tvaarta = $tvaarta;

        return $this;
    }

    public function getWmqteimp(): ?string
    {
        return $this->wmqteimp;
    }

    public function setWmqteimp(string $wmqteimp): self
    {
        $this->wmqteimp = $wmqteimp;

        return $this;
    }

    public function getWmreapfroidfl(): ?string
    {
        return $this->wmreapfroidfl;
    }

    public function setWmreapfroidfl(string $wmreapfroidfl): self
    {
        $this->wmreapfroidfl = $wmreapfroidfl;

        return $this;
    }

    public function getSref1(): ?string
    {
        return $this->sref1;
    }

    public function setSref1(string $sref1): self
    {
        $this->sref1 = $sref1;

        return $this;
    }

    public function getSref2(): ?string
    {
        return $this->sref2;
    }

    public function setSref2(string $sref2): self
    {
        $this->sref2 = $sref2;

        return $this;
    }

    public function getPrefdvno(): ?string
    {
        return $this->prefdvno;
    }

    public function setPrefdvno(string $prefdvno): self
    {
        $this->prefdvno = $prefdvno;

        return $this;
    }

    public function getDvno(): ?string
    {
        return $this->dvno;
    }

    public function setDvno(string $dvno): self
    {
        $this->dvno = $dvno;

        return $this;
    }

    public function getDtind(): ?string
    {
        return $this->dtind;
    }

    public function setDtind(string $dtind): self
    {
        $this->dtind = $dtind;

        return $this;
    }

    public function getTierscli(): ?string
    {
        return $this->tierscli;
    }

    public function setTierscli(string $tierscli): self
    {
        $this->tierscli = $tierscli;

        return $this;
    }

    public function getRevuart(): ?string
    {
        return $this->revuart;
    }

    public function setRevuart(string $revuart): self
    {
        $this->revuart = $revuart;

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

    public function getBudgetcod(): ?string
    {
        return $this->budgetcod;
    }

    public function setBudgetcod(string $budgetcod): self
    {
        $this->budgetcod = $budgetcod;

        return $this;
    }

    public function getTravun(): ?string
    {
        return $this->travun;
    }

    public function setTravun(string $travun): self
    {
        $this->travun = $travun;

        return $this;
    }

    public function getPrixmoy(): ?string
    {
        return $this->prixmoy;
    }

    public function setPrixmoy(string $prixmoy): self
    {
        $this->prixmoy = $prixmoy;

        return $this;
    }

    public function getContratcod(): ?string
    {
        return $this->contratcod;
    }

    public function setContratcod(string $contratcod): self
    {
        $this->contratcod = $contratcod;

        return $this;
    }

    public function getContratfam(): ?string
    {
        return $this->contratfam;
    }

    public function setContratfam(string $contratfam): self
    {
        $this->contratfam = $contratfam;

        return $this;
    }

    public function getEanautofl(): ?string
    {
        return $this->eanautofl;
    }

    public function setEanautofl(string $eanautofl): self
    {
        $this->eanautofl = $eanautofl;

        return $this;
    }

    public function getTypeartcod(): ?string
    {
        return $this->typeartcod;
    }

    public function setTypeartcod(string $typeartcod): self
    {
        $this->typeartcod = $typeartcod;

        return $this;
    }

    public function getResjrnb(): ?string
    {
        return $this->resjrnb;
    }

    public function setResjrnb(string $resjrnb): self
    {
        $this->resjrnb = $resjrnb;

        return $this;
    }

    public function getMarche(): ?string
    {
        return $this->marche;
    }

    public function setMarche(string $marche): self
    {
        $this->marche = $marche;

        return $this;
    }

    public function getQte(): ?string
    {
        return $this->qte;
    }

    public function setQte(string $qte): self
    {
        $this->qte = $qte;

        return $this;
    }

    public function getTiersref(): ?string
    {
        return $this->tiersref;
    }

    public function setTiersref(string $tiersref): self
    {
        $this->tiersref = $tiersref;

        return $this;
    }

    public function getEphemerefl(): ?string
    {
        return $this->ephemerefl;
    }

    public function setEphemerefl(string $ephemerefl): self
    {
        $this->ephemerefl = $ephemerefl;

        return $this;
    }

    public function getAchunref(): ?string
    {
        return $this->achunref;
    }

    public function setAchunref(string $achunref): self
    {
        $this->achunref = $achunref;

        return $this;
    }

    public function getQtemin(): ?string
    {
        return $this->qtemin;
    }

    public function setQtemin(string $qtemin): self
    {
        $this->qtemin = $qtemin;

        return $this;
    }

    public function getQtepar(): ?string
    {
        return $this->qtepar;
    }

    public function setQtepar(string $qtepar): self
    {
        $this->qtepar = $qtepar;

        return $this;
    }

    public function getCpti(): ?string
    {
        return $this->cpti;
    }

    public function setCpti(string $cpti): self
    {
        $this->cpti = $cpti;

        return $this;
    }

    public function getVisa0001(): ?string
    {
        return $this->visa0001;
    }

    public function setVisa0001(string $visa0001): self
    {
        $this->visa0001 = $visa0001;

        return $this;
    }

    public function getVisa0002(): ?string
    {
        return $this->visa0002;
    }

    public function setVisa0002(string $visa0002): self
    {
        $this->visa0002 = $visa0002;

        return $this;
    }

    public function getVisa0003(): ?string
    {
        return $this->visa0003;
    }

    public function setVisa0003(string $visa0003): self
    {
        $this->visa0003 = $visa0003;

        return $this;
    }

    public function getVisa0004(): ?string
    {
        return $this->visa0004;
    }

    public function setVisa0004(string $visa0004): self
    {
        $this->visa0004 = $visa0004;

        return $this;
    }

    public function getVisa0005(): ?string
    {
        return $this->visa0005;
    }

    public function setVisa0005(string $visa0005): self
    {
        $this->visa0005 = $visa0005;

        return $this;
    }

    public function getArtId(): ?int
    {
        return $this->artId;
    }


}
