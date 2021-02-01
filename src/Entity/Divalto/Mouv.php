<?php

namespace App\Entity\Divalto;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\Divalto\MouvRepository;

/**
 * Mouv
 * @ORM\Entity(repositoryClass="App\Repository\Divalto\MouvRepository")
 * @ORM\Table(name="MOUV", indexes={@ORM\Index(name="INDEX_A", columns={"CE2", "DOS", "TICOD", "TIERS", "FADT", "MOUV_ID"}), 
 * @ORM\Index(name="INDEX_B", columns={"CE6", "DOS", "CDENRNO", "MOUV_ID"}), 
 * @ORM\Index(name="INDEX_C", columns={"DOS", "PROJET", "FADT", "MOUV_ID"}), 
 * @ORM\Index(name="INDEX_D", columns={"DOS", "TICOD", "TIERS", "MARCHE", "FADT", "MOUV_ID"}), 
 * @ORM\Index(name="INDEX_F", columns={"CE2", "DOS", "TICOD", "TIERS", "REF", "SREF1", "SREF2", "FADT", "MOUV_ID"}), 
 * @ORM\Index(name="INDEX_G", columns={"DOS", "REF", "SREF1", "SREF2", "BLDT", "SENS", "MOUV_ID"}), 
 * @ORM\Index(name="INDEX_H", columns={"CE5", "DOS", "TICOD", "PREFDVNO", "DVNO", "DVLG", "DVCE4", "DVSLG", "MOUV_ID"}), 
 * @ORM\Index(name="INDEX_I", columns={"CE6", "DOS", "TICOD", "PREFCDNO", "CDNO", "CDLG", "CDCE4", "CDSLG", "MOUV_ID"}), 
 * @ORM\Index(name="INDEX_J", columns={"CE7", "DOS", "TICOD", "PREFBLNO", "BLNO", "BLLG", "BLCE4", "BLSLG", "MOUV_ID"}), 
 * @ORM\Index(name="INDEX_K", columns={"CE8", "DOS", "TICOD", "PREFFANO", "FANO", "FALG", "FACE4", "FASLG", "MOUV_ID"}), 
 * @ORM\Index(name="INDEX_L", columns={"CE2", "DOS", "REF", "SREF1", "SREF2", "FADT", "MOUV_ID"}), 
 * @ORM\Index(name="INDEX_M", columns={"DOS", "ENRNO", "MOUV_ID"}), 
 * @ORM\Index(name="INDEX_N", columns={"CE5", "DOS", "TICOD", "DVCE4", "DVDT", "MOUV_ID"}), 
 * @ORM\Index(name="INDEX_O", columns={"CE6", "DOS", "TICOD", "CDCE4", "CDDT", "MOUV_ID"}), 
 * @ORM\Index(name="INDEX_P", columns={"CE7", "DOS", "TICOD", "BLCE4", "BLDT", "MOUV_ID"}), 
 * @ORM\Index(name="INDEX_Q", columns={"CE8", "DOS", "TICOD", "FACE4", "FADT", "MOUV_ID"}), 
 * @ORM\Index(name="INDEX_S", columns={"DOS", "POSITION", "MOUV_ID"}), 
 * @ORM\Index(name="INDEX_T", columns={"CEA", "DOS", "PREFOFNO", "OFNO", "GAMSEQ", "REF", "SREF1", "SREF2", "MOUV_ID"}), 
 * @ORM\Index(name="INDEX_U", columns={"CEB", "DOS", "RECPTNO", "MOUV_ID"}), 
 * @ORM\Index(name="INDEX_V", columns={"CE5", "DOS", "DVENRNO", "MOUV_ID"}), 
 * @ORM\Index(name="INDEX_W", columns={"CE7", "DOS", "BLENRNO", "MOUV_ID"}), 
 * @ORM\Index(name="INDEX_X", columns={"CEC", "DOS", "PFCNO", "MOUV_ID"}), 
 * @ORM\Index(name="INDEX_Y", columns={"CE2", "DOS", "ELEMNO", "AFRINDICE", "ENRNO", "MOUV_ID"})})
 */
class Mouv
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
     * @ORM\Column(name="CEB", type="string", length=1, nullable=false, options={"fixed"=true,"comment"="CeB"})
     */
    private $ceb;

    /**
     * @var string
     *
     * @ORM\Column(name="CEC", type="string", length=1, nullable=false, options={"fixed"=true,"comment"="CeC"})
     */
    private $cec;

    /**
     * @var string
     *
     * @ORM\Column(name="CED", type="string", length=1, nullable=false, options={"fixed"=true,"comment"="CeD"})
     */
    private $ced;

    /**
     * @var string
     *
     * @ORM\Column(name="CEE", type="string", length=1, nullable=false, options={"fixed"=true,"comment"="CeE"})
     */
    private $cee;

    /**
     * @var string
     *
     * @ORM\Column(name="CEF", type="string", length=1, nullable=false, options={"fixed"=true,"comment"="CeF"})
     */
    private $cef;

    /**
     * @var string
     *
     * @ORM\Column(name="DOS", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Dossier"})
     */
    private $dos;

    //JEROME j'ai ajouté la relation ManyToOne ci dessous

    /**
     * @var string
     * @ORM\ManyToOne(targetEntity=Art::class, inversedBy="Mouv")
     * @ORM\Column(name="REF", type="string", length=25, nullable=false, options={"fixed"=true,"comment"="Référence"})
     */
    private $ref;

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
     * @ORM\Column(name="TICOD", type="string", length=1, nullable=false, options={"fixed"=true,"comment"="Type de tiers"})
     */
    private $ticod;

    /**
     * @var string
     *
     * @ORM\Column(name="PICOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Type de pièce"})
     */
    private $picod;

    /**
     * @var string
     *
     * @ORM\Column(name="TIERS", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Code tiers"})
     */
    private $tiers;

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
     * @var \DateTime|null
     *
     * @ORM\Column(name="DVDT", type="date", nullable=true, options={"comment"="Date de devis"})
     */
    private $dvdt;

    /**
     * @var string
     *
     * @ORM\Column(name="DVLG", type="decimal", precision=4, scale=0, nullable=false, options={"comment"="Devis no ligne"})
     */
    private $dvlg;

    /**
     * @var string
     *
     * @ORM\Column(name="DVSLG", type="decimal", precision=2, scale=0, nullable=false, options={"comment"="Devis no sous-ligne"})
     */
    private $dvslg;

    /**
     * @var string
     *
     * @ORM\Column(name="DVCE4", type="string", length=1, nullable=false, options={"fixed"=true,"comment"="Valeur CE4 en devis"})
     */
    private $dvce4;

    /**
     * @var string
     *
     * @ORM\Column(name="PREFCDNO", type="string", length=10, nullable=false, options={"fixed"=true,"comment"="Préfixe numéro de commande"})
     */
    private $prefcdno;

    /**
     * @var string
     *
     * @ORM\Column(name="CDNO", type="decimal", precision=10, scale=0, nullable=false, options={"comment"="Commande no"})
     */
    private $cdno;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="CDDT", type="date", nullable=true, options={"comment"="Date de commande"})
     */
    private $cddt;

    /**
     * @var string
     *
     * @ORM\Column(name="CDLG", type="decimal", precision=4, scale=0, nullable=false, options={"comment"="Commande no ligne"})
     */
    private $cdlg;

    /**
     * @var string
     *
     * @ORM\Column(name="CDSLG", type="decimal", precision=2, scale=0, nullable=false, options={"comment"="Commande no sous-ligne"})
     */
    private $cdslg;

    /**
     * @var string
     *
     * @ORM\Column(name="CDCE4", type="string", length=1, nullable=false, options={"fixed"=true,"comment"="Valeur CE4 en commande"})
     */
    private $cdce4;

    /**
     * @var string
     *
     * @ORM\Column(name="CDENRNO", type="decimal", precision=14, scale=0, nullable=false, options={"comment"="No enregistrement commande"})
     */
    private $cdenrno;

    /**
     * @var string
     *
     * @ORM\Column(name="PREFBLNO", type="string", length=10, nullable=false, options={"fixed"=true,"comment"="Préfixe numéro de Bl"})
     */
    private $prefblno;

    /**
     * @var string
     *
     * @ORM\Column(name="BLNO", type="decimal", precision=10, scale=0, nullable=false, options={"comment"="No de Bl"})
     */
    private $blno;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="BLDT", type="date", nullable=true, options={"comment"="Date de BL"})
     */
    private $bldt;

    /**
     * @var string
     *
     * @ORM\Column(name="BLLG", type="decimal", precision=4, scale=0, nullable=false, options={"comment"="BL no ligne"})
     */
    private $bllg;

    /**
     * @var string
     *
     * @ORM\Column(name="BLSLG", type="decimal", precision=2, scale=0, nullable=false, options={"comment"="BL no sous-ligne"})
     */
    private $blslg;

    /**
     * @var string
     *
     * @ORM\Column(name="BLCE4", type="string", length=1, nullable=false, options={"fixed"=true,"comment"="Valeur CE4 en BL"})
     */
    private $blce4;

    /**
     * @var string
     *
     * @ORM\Column(name="PREFFANO", type="string", length=10, nullable=false, options={"fixed"=true,"comment"="Préfixe numéro de facture"})
     */
    private $preffano;

    /**
     * @var string
     *
     * @ORM\Column(name="FANO", type="decimal", precision=10, scale=0, nullable=false, options={"comment"="Facture no"})
     */
    private $fano;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FADT", type="date", nullable=true, options={"comment"="Date de facture"})
     */
    private $fadt;

    /**
     * @var string
     *
     * @ORM\Column(name="FALG", type="decimal", precision=4, scale=0, nullable=false, options={"comment"="Facture no de ligne"})
     */
    private $falg;

    /**
     * @var string
     *
     * @ORM\Column(name="FASLG", type="decimal", precision=2, scale=0, nullable=false, options={"comment"="Facture no sous-ligne"})
     */
    private $faslg;

    /**
     * @var string
     *
     * @ORM\Column(name="FACE4", type="string", length=1, nullable=false, options={"fixed"=true,"comment"="Valeur CE4 en facture"})
     */
    private $face4;

    /**
     * @var string
     *
     * @ORM\Column(name="BPNO", type="decimal", precision=10, scale=0, nullable=false, options={"comment"="Bon préparation no"})
     */
    private $bpno;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="BPDT", type="date", nullable=true, options={"comment"="Date bon de préparation"})
     */
    private $bpdt;

    /**
     * @var string
     *
     * @ORM\Column(name="OP", type="string", length=3, nullable=false, options={"fixed"=true,"comment"="Code opération"})
     */
    private $op;

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
     * @ORM\Column(name="DEPO", type="string", length=3, nullable=false, options={"fixed"=true,"comment"="Dépôt"})
     */
    private $depo;

    /**
     * @var string
     *
     * @ORM\Column(name="ETB", type="string", length=3, nullable=false, options={"fixed"=true,"comment"="Etablissement"})
     */
    private $etb;

    /**
     * @var string
     *
     * @ORM\Column(name="PROJET", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Projet"})
     */
    private $projet;

    /**
     * @var string
     *
     * @ORM\Column(name="MARCHE", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Code marché"})
     */
    private $marche;

    /**
     * @var string
     *
     * @ORM\Column(name="DES", type="string", length=80, nullable=false, options={"fixed"=true,"comment"="Désignation"})
     */
    private $des;

    /**
     * @var string
     *
     * @ORM\Column(name="REFFO", type="string", length=40, nullable=false, options={"fixed"=true,"comment"="Référence du fournisseur"})
     */
    private $reffo;

    /**
     * @var string
     *
     * @ORM\Column(name="REPR_0001", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Représentants"})
     */
    private $repr0001;

    /**
     * @var string
     *
     * @ORM\Column(name="REPR_0002", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Représentants"})
     */
    private $repr0002;

    /**
     * @var string
     *
     * @ORM\Column(name="REPR_0003", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Représentants"})
     */
    private $repr0003;

    /**
     * @var string
     *
     * @ORM\Column(name="ENRNO", type="decimal", precision=14, scale=0, nullable=false, options={"comment"="No enregistrement"})
     */
    private $enrno;

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
     * @ORM\Column(name="TAFAMR", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Famille de tarification"})
     */
    private $tafamr;

    /**
     * @var string
     *
     * @ORM\Column(name="TAFAMRX", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Famille tarif exceptionnelle"})
     */
    private $tafamrx;

    /**
     * @var string
     *
     * @ORM\Column(name="REFAMR", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Famille de remise"})
     */
    private $refamr;

    /**
     * @var string
     *
     * @ORM\Column(name="REFAMRX", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Famille remise exceptionnelle"})
     */
    private $refamrx;

    /**
     * @var string
     *
     * @ORM\Column(name="COFAMR", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Famille de commission article"})
     */
    private $cofamr;

    /**
     * @var string
     *
     * @ORM\Column(name="COFAMV_0001", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Famille commission commercial"})
     */
    private $cofamv0001;

    /**
     * @var string
     *
     * @ORM\Column(name="COFAMV_0002", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Famille commission commercial"})
     */
    private $cofamv0002;

    /**
     * @var string
     *
     * @ORM\Column(name="COFAMV_0003", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Famille commission commercial"})
     */
    private $cofamv0003;

    /**
     * @var string
     *
     * @ORM\Column(name="DEV", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Devise"})
     */
    private $dev;

    /**
     * @var string
     *
     * @ORM\Column(name="VENUN", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Unité de vente"})
     */
    private $venun;

    /**
     * @var string
     *
     * @ORM\Column(name="REFUN", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Unité de référence"})
     */
    private $refun;

    /**
     * @var string
     *
     * @ORM\Column(name="PUBUN", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Unité du prix unitaire"})
     */
    private $pubun;

    /**
     * @var string
     *
     * @ORM\Column(name="EMBUN", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Unité emballage"})
     */
    private $embun;

    /**
     * @var string
     *
     * @ORM\Column(name="EDCOD", type="string", length=5, nullable=false, options={"fixed"=true,"comment"="Code édition"})
     */
    private $edcod;

    /**
     * @var string
     *
     * @ORM\Column(name="TXTEDCOD", type="string", length=5, nullable=false, options={"fixed"=true,"comment"="Code édition du texte lié"})
     */
    private $txtedcod;

    /**
     * @var string
     *
     * @ORM\Column(name="PAGCOD", type="string", length=2, nullable=false, options={"fixed"=true,"comment"="Code mise en page"})
     */
    private $pagcod;

    /**
     * @var string
     *
     * @ORM\Column(name="PRIOCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Code priorité"})
     */
    private $priocod;

    /**
     * @var string
     *
     * @ORM\Column(name="AXE_0001", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Axe analytique"})
     */
    private $axe0001;

    /**
     * @var string
     *
     * @ORM\Column(name="AXE_0002", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Axe analytique"})
     */
    private $axe0002;

    /**
     * @var string
     *
     * @ORM\Column(name="AXE_0003", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Axe analytique"})
     */
    private $axe0003;

    /**
     * @var string
     *
     * @ORM\Column(name="AXE_0004", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Axe analytique"})
     */
    private $axe0004;

    /**
     * @var string
     *
     * @ORM\Column(name="CPTV", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Compte de vente"})
     */
    private $cptv;

    /**
     * @var string
     *
     * @ORM\Column(name="POSITION", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Position de la ligne de pièce"})
     */
    private $position;

    /**
     * @var string
     *
     * @ORM\Column(name="SENS", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Sens du mouvement"})
     */
    private $sens;

    /**
     * @var string
     *
     * @ORM\Column(name="AVENANT", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Avenant"})
     */
    private $avenant;

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
     * @ORM\Column(name="GAMSEQ", type="string", length=6, nullable=false, options={"fixed"=true,"comment"="No séquence de la gamme"})
     */
    private $gamseq;

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
     * @ORM\Column(name="PUBTYP", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Type de prix 1=brut, 2=net"})
     */
    private $pubtyp;

    /**
     * @var string
     *
     * @ORM\Column(name="PAFORF", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Prix forfaitaire (1 = Non ; 2 = Oui)"})
     */
    private $paforf;

    /**
     * @var string
     *
     * @ORM\Column(name="PREFOFNO", type="string", length=10, nullable=false, options={"fixed"=true,"comment"="Préfixe du numéro de l ordre de fabrication"})
     */
    private $prefofno;

    /**
     * @var string
     *
     * @ORM\Column(name="OFNO", type="decimal", precision=10, scale=0, nullable=false, options={"comment"="Numéro de l ordre de fabrication"})
     */
    private $ofno;

    /**
     * @var string
     *
     * @ORM\Column(name="PREFCDNOPERE", type="string", length=10, nullable=false, options={"fixed"=true,"comment"="Préfixe du numéro de pièce de l OF père"})
     */
    private $prefcdnopere;

    /**
     * @var string
     *
     * @ORM\Column(name="CDNOPERE", type="decimal", precision=10, scale=0, nullable=false, options={"comment"="Numéro de pièce de l OF père"})
     */
    private $cdnopere;

    /**
     * @var string
     *
     * @ORM\Column(name="LIGNE", type="decimal", precision=4, scale=0, nullable=false, options={"comment"="Numéro de ligne"})
     */
    private $ligne;

    /**
     * @var string
     *
     * @ORM\Column(name="TICKET", type="decimal", precision=10, scale=0, nullable=false, options={"comment"="Numéro de ticket"})
     */
    private $ticket;

    /**
     * @var string
     *
     * @ORM\Column(name="APPREMMT", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Appliquer la remise en montant sur la ligne ou le prix unitaire"})
     */
    private $appremmt;

    /**
     * @var string
     *
     * @ORM\Column(name="APPREMMTUN", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Appliquer la remise en montant forfaitairement"})
     */
    private $appremmtun;

    /**
     * @var string
     *
     * @ORM\Column(name="CONFIGURATEURMONOSTATUS", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Statut de la configuration multiligne"})
     */
    private $configurateurmonostatus;

    /**
     * @var string
     *
     * @ORM\Column(name="CONFIGURATEURMULTISTATUS", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Statut de la configuration multiligne"})
     */
    private $configurateurmultistatus;

    /**
     * @var string
     *
     * @ORM\Column(name="CONFIGURATEURLINO", type="decimal", precision=9, scale=0, nullable=false, options={"comment"="Numéro de ligne pour le configurateur"})
     */
    private $configurateurlino;

    /**
     * @var string
     *
     * @ORM\Column(name="CONFIGURATEURREF", type="string", length=25, nullable=false, options={"fixed"=true,"comment"="Référence Configurateur"})
     */
    private $configurateurref;

    /**
     * @var string
     *
     * @ORM\Column(name="CONFIGURATEURSREF1", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="ConfigurateurSous référence 1"})
     */
    private $configurateursref1;

    /**
     * @var string
     *
     * @ORM\Column(name="CONFIGURATEURSREF2", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Sous référence Configurateur"})
     */
    private $configurateursref2;

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
     * @ORM\Column(name="PUB", type="decimal", precision=13, scale=4, nullable=false, options={"comment"="Prix unitaire brut"})
     */
    private $pub;

    /**
     * @var string
     *
     * @ORM\Column(name="PPAR", type="decimal", precision=12, scale=3, nullable=false, options={"comment"="Prix par"})
     */
    private $ppar;

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
     * @var string
     *
     * @ORM\Column(name="REMMT", type="decimal", precision=9, scale=2, nullable=false, options={"comment"="Remise en montant"})
     */
    private $remmt;

    /**
     * @var string
     *
     * @ORM\Column(name="PROMOTYP", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Promotion ?"})
     */
    private $promotyp;

    /**
     * @var string
     *
     * @ORM\Column(name="PUSTAT", type="decimal", precision=17, scale=6, nullable=false, options={"comment"="Prix unitaire statistique"})
     */
    private $pustat;

    /**
     * @var string
     *
     * @ORM\Column(name="QTE1", type="decimal", precision=12, scale=3, nullable=false, options={"comment"="Quantité 1"})
     */
    private $qte1;

    /**
     * @var string
     *
     * @ORM\Column(name="QTE2", type="decimal", precision=12, scale=3, nullable=false, options={"comment"="Quantité 2"})
     */
    private $qte2;

    /**
     * @var string
     *
     * @ORM\Column(name="QTE3", type="decimal", precision=12, scale=3, nullable=false, options={"comment"="Quantité 3"})
     */
    private $qte3;

    /**
     * @var string
     *
     * @ORM\Column(name="DVQTE", type="decimal", precision=12, scale=3, nullable=false, options={"comment"="Quantité devisée"})
     */
    private $dvqte;

    /**
     * @var string
     *
     * @ORM\Column(name="CDQTE", type="decimal", precision=12, scale=3, nullable=false, options={"comment"="Quantité commandée"})
     */
    private $cdqte;

    /**
     * @var string
     *
     * @ORM\Column(name="BLQTE", type="decimal", precision=12, scale=3, nullable=false, options={"comment"="Quantité livrée"})
     */
    private $blqte;

    /**
     * @var string
     *
     * @ORM\Column(name="FAQTE", type="decimal", precision=12, scale=3, nullable=false, options={"comment"="Quantité facturée"})
     */
    private $faqte;

    /**
     * @var string
     *
     * @ORM\Column(name="REFQTE", type="decimal", precision=12, scale=3, nullable=false, options={"comment"="Quantité en unité de référence"})
     */
    private $refqte;

    /**
     * @var string
     *
     * @ORM\Column(name="EMBQTE", type="decimal", precision=12, scale=3, nullable=false, options={"comment"="Quantité d un emballage"})
     */
    private $embqte;

    /**
     * @var string
     *
     * @ORM\Column(name="COMP_0001", type="decimal", precision=5, scale=2, nullable=false, options={"comment"="Taux de commission"})
     */
    private $comp0001;

    /**
     * @var string
     *
     * @ORM\Column(name="COMP_0002", type="decimal", precision=5, scale=2, nullable=false, options={"comment"="Taux de commission"})
     */
    private $comp0002;

    /**
     * @var string
     *
     * @ORM\Column(name="COMP_0003", type="decimal", precision=5, scale=2, nullable=false, options={"comment"="Taux de commission"})
     */
    private $comp0003;

    /**
     * @var string
     *
     * @ORM\Column(name="COMMT_0001", type="decimal", precision=9, scale=2, nullable=false, options={"comment"="Montant forfaitaire de commission"})
     */
    private $commt0001;

    /**
     * @var string
     *
     * @ORM\Column(name="COMMT_0002", type="decimal", precision=9, scale=2, nullable=false, options={"comment"="Montant forfaitaire de commission"})
     */
    private $commt0002;

    /**
     * @var string
     *
     * @ORM\Column(name="COMMT_0003", type="decimal", precision=9, scale=2, nullable=false, options={"comment"="Montant forfaitaire de commission"})
     */
    private $commt0003;

    /**
     * @var string
     *
     * @ORM\Column(name="MONT", type="decimal", precision=13, scale=2, nullable=false, options={"comment"="Montant de la ligne"})
     */
    private $mont;

    /**
     * @var string
     *
     * @ORM\Column(name="FRAISMT", type="decimal", precision=12, scale=2, nullable=false, options={"comment"="Montant frais à imputer"})
     */
    private $fraismt;

    /**
     * @var string
     *
     * @ORM\Column(name="DECCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Code ligne décomposée"})
     */
    private $deccod;

    /**
     * @var string
     *
     * @ORM\Column(name="PCOD_0001", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Code prix"})
     */
    private $pcod0001;

    /**
     * @var string
     *
     * @ORM\Column(name="PCOD_0002", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Code prix"})
     */
    private $pcod0002;

    /**
     * @var string
     *
     * @ORM\Column(name="PCOD_0003", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Code prix"})
     */
    private $pcod0003;

    /**
     * @var string
     *
     * @ORM\Column(name="PCOD_0004", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Code prix"})
     */
    private $pcod0004;

    /**
     * @var string
     *
     * @ORM\Column(name="PCOD_0005", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Code prix"})
     */
    private $pcod0005;

    /**
     * @var string
     *
     * @ORM\Column(name="STATUS", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Status de la pièce"})
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="STRES", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Réservation du stock ?"})
     */
    private $stres;

    /**
     * @var string
     *
     * @ORM\Column(name="FILLERSENS", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Filler temporaire à supprimer"})
     */
    private $fillersens;

    /**
     * @var string
     *
     * @ORM\Column(name="MVCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Génération d un mouvement de stock"})
     */
    private $mvcod;

    /**
     * @var string
     *
     * @ORM\Column(name="PVCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Code prix de vente"})
     */
    private $pvcod;

    /**
     * @var string
     *
     * @ORM\Column(name="QTETYP", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Type de quantité"})
     */
    private $qtetyp;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="GADT", type="date", nullable=true, options={"comment"="Date fin de garantie"})
     */
    private $gadt;

    /**
     * @var string
     *
     * @ORM\Column(name="CRTOTMT", type="decimal", precision=17, scale=6, nullable=false, options={"comment"="Montant total coût de revient vtl"})
     */
    private $crtotmt;

    /**
     * @var string
     *
     * @ORM\Column(name="CMPTOTMT", type="decimal", precision=17, scale=6, nullable=false, options={"comment"="Montant total cmp vtl"})
     */
    private $cmptotmt;

    /**
     * @var string
     *
     * @ORM\Column(name="TXTCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Indicateur texte lié"})
     */
    private $txtcod;

    /**
     * @var string
     *
     * @ORM\Column(name="TXTNOTE", type="decimal", precision=8, scale=0, nullable=false, options={"comment"="Numéro de note ligne"})
     */
    private $txtnote;

    /**
     * @var string
     *
     * @ORM\Column(name="ENRNOP_0001", type="decimal", precision=14, scale=0, nullable=false, options={"comment"="No enregistrement poste"})
     */
    private $enrnop0001;

    /**
     * @var string
     *
     * @ORM\Column(name="ENRNOP_0002", type="decimal", precision=14, scale=0, nullable=false, options={"comment"="No enregistrement poste"})
     */
    private $enrnop0002;

    /**
     * @var string
     *
     * @ORM\Column(name="ENRNOP_0003", type="decimal", precision=14, scale=0, nullable=false, options={"comment"="No enregistrement poste"})
     */
    private $enrnop0003;

    /**
     * @var string
     *
     * @ORM\Column(name="ENRNOP_0004", type="decimal", precision=14, scale=0, nullable=false, options={"comment"="No enregistrement poste"})
     */
    private $enrnop0004;

    /**
     * @var string
     *
     * @ORM\Column(name="ENRNOC_0001", type="decimal", precision=14, scale=0, nullable=false, options={"comment"="No enregistrement chapitre"})
     */
    private $enrnoc0001;

    /**
     * @var string
     *
     * @ORM\Column(name="ENRNOC_0002", type="decimal", precision=14, scale=0, nullable=false, options={"comment"="No enregistrement chapitre"})
     */
    private $enrnoc0002;

    /**
     * @var string
     *
     * @ORM\Column(name="ENRNOC_0003", type="decimal", precision=14, scale=0, nullable=false, options={"comment"="No enregistrement chapitre"})
     */
    private $enrnoc0003;

    /**
     * @var string
     *
     * @ORM\Column(name="ENRNOC_0004", type="decimal", precision=14, scale=0, nullable=false, options={"comment"="No enregistrement chapitre"})
     */
    private $enrnoc0004;

    /**
     * @var string
     *
     * @ORM\Column(name="REMPIEMT_0001", type="decimal", precision=12, scale=2, nullable=false, options={"comment"="Montant remise pied"})
     */
    private $rempiemt0001;

    /**
     * @var string
     *
     * @ORM\Column(name="REMPIEMT_0002", type="decimal", precision=12, scale=2, nullable=false, options={"comment"="Montant remise pied"})
     */
    private $rempiemt0002;

    /**
     * @var string
     *
     * @ORM\Column(name="REMPIEMT_0003", type="decimal", precision=12, scale=2, nullable=false, options={"comment"="Montant remise pied"})
     */
    private $rempiemt0003;

    /**
     * @var string
     *
     * @ORM\Column(name="REMPIEMT_0004", type="decimal", precision=12, scale=2, nullable=false, options={"comment"="Montant remise pied"})
     */
    private $rempiemt0004;

    /**
     * @var string
     *
     * @ORM\Column(name="MVSTAT", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Mouvement généré en statistique"})
     */
    private $mvstat;

    /**
     * @var string
     *
     * @ORM\Column(name="BLASENRNO", type="decimal", precision=14, scale=0, nullable=false, options={"comment"="No enregistrement du bl associé"})
     */
    private $blasenrno;

    /**
     * @var string
     *
     * @ORM\Column(name="REMPIEPART_0001", type="decimal", precision=12, scale=2, nullable=false, options={"comment"="Remise pied part de la remise en montant"})
     */
    private $rempiepart0001;

    /**
     * @var string
     *
     * @ORM\Column(name="REMPIEPART_0002", type="decimal", precision=12, scale=2, nullable=false, options={"comment"="Remise pied part de la remise en montant"})
     */
    private $rempiepart0002;

    /**
     * @var string
     *
     * @ORM\Column(name="REMPIEPART_0003", type="decimal", precision=12, scale=2, nullable=false, options={"comment"="Remise pied part de la remise en montant"})
     */
    private $rempiepart0003;

    /**
     * @var string
     *
     * @ORM\Column(name="REMPIEPART_0004", type="decimal", precision=12, scale=2, nullable=false, options={"comment"="Remise pied part de la remise en montant"})
     */
    private $rempiepart0004;

    /**
     * @var string
     *
     * @ORM\Column(name="RECPTNO", type="decimal", precision=14, scale=0, nullable=false, options={"comment"="Numéro reception qualité"})
     */
    private $recptno;

    /**
     * @var string
     *
     * @ORM\Column(name="PRGQTE", type="decimal", precision=12, scale=3, nullable=false, options={"comment"="Quantité du programme"})
     */
    private $prgqte;

    /**
     * @var string
     *
     * @ORM\Column(name="PRGREFQTE", type="decimal", precision=12, scale=3, nullable=false, options={"comment"="Quantité du programme en unités de référence"})
     */
    private $prgrefqte;

    /**
     * @var string
     *
     * @ORM\Column(name="DVENRNO", type="decimal", precision=14, scale=0, nullable=false, options={"comment"="Numéro d enregistrement en devis"})
     */
    private $dvenrno;

    /**
     * @var string
     *
     * @ORM\Column(name="BLENRNO", type="decimal", precision=14, scale=0, nullable=false, options={"comment"="Numéro d enregistrement en BL"})
     */
    private $blenrno;

    /**
     * @var string
     *
     * @ORM\Column(name="REBUCOD", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Code rebut"})
     */
    private $rebucod;

    /**
     * @var string
     *
     * @ORM\Column(name="CTMFL", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Contre marque"})
     */
    private $ctmfl;

    /**
     * @var string
     *
     * @ORM\Column(name="PFCNO", type="decimal", precision=9, scale=0, nullable=false, options={"comment"="Numéro de préparation facture contrat"})
     */
    private $pfcno;

    /**
     * @var string
     *
     * @ORM\Column(name="GPAFL", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Gestion de Projet à l Affaire"})
     */
    private $gpafl;

    /**
     * @var string
     *
     * @ORM\Column(name="ELEMNO", type="decimal", precision=14, scale=0, nullable=false, options={"comment"="Numéro d élément"})
     */
    private $elemno;

    /**
     * @var string
     *
     * @ORM\Column(name="AFRINDICE", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Indice affaire"})
     */
    private $afrindice;

    /**
     * @var string
     *
     * @ORM\Column(name="BESOINNO", type="decimal", precision=8, scale=0, nullable=false, options={"comment"="Numéro de besoin sur l élément"})
     */
    private $besoinno;

    /**
     * @var string
     *
     * @ORM\Column(name="TVAART", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Régime TVA article"})
     */
    private $tvaart;

    /**
     * @var string
     *
     * @ORM\Column(name="CONTRATCOD", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Code contrat modèle"})
     */
    private $contratcod;

    /**
     * @var string
     *
     * @ORM\Column(name="CADEAUFL", type="decimal", precision=1, scale=0, nullable=false)
     */
    private $cadeaufl;

    /**
     * @var string
     *
     * @ORM\Column(name="ENRNOCAD", type="decimal", precision=14, scale=0, nullable=false)
     */
    private $enrnocad;

    /**
     * @var string
     *
     * @ORM\Column(name="FRAISFL", type="decimal", precision=1, scale=0, nullable=false)
     */
    private $fraisfl;

    /**
     * @var string
     *
     * @ORM\Column(name="FRAISVALIDTYP", type="decimal", precision=1, scale=0, nullable=false)
     */
    private $fraisvalidtyp;

    /**
     * @var string
     *
     * @ORM\Column(name="GRATUITFL", type="decimal", precision=1, scale=0, nullable=false)
     */
    private $gratuitfl;

    /**
     * @var string
     *
     * @ORM\Column(name="MOTIF", type="string", length=8, nullable=false, options={"fixed"=true})
     */
    private $motif;

    /**
     * @var string
     *
     * @ORM\Column(name="OPTIONFL", type="decimal", precision=1, scale=0, nullable=false)
     */
    private $optionfl;

    /**
     * @var string
     *
     * @ORM\Column(name="OPTIONVALIDEFL", type="decimal", precision=1, scale=0, nullable=false)
     */
    private $optionvalidefl;

    /**
     * @var string
     *
     * @ORM\Column(name="PANACHEFL", type="decimal", precision=1, scale=0, nullable=false)
     */
    private $panachefl;

    /**
     * @var string
     *
     * @ORM\Column(name="PCOD_0006", type="decimal", precision=1, scale=0, nullable=false)
     */
    private $pcod0006;

    /**
     * @var string
     *
     * @ORM\Column(name="PUNETORI", type="decimal", precision=13, scale=4, nullable=false)
     */
    private $punetori;

    /**
     * @var string
     *
     * @ORM\Column(name="REGLECOD", type="string", length=8, nullable=false, options={"fixed"=true})
     */
    private $reglecod;

    /**
     * @var string
     *
     * @ORM\Column(name="REMCODCAD", type="string", length=8, nullable=false, options={"fixed"=true})
     */
    private $remcodcad;

    /**
     * @var string
     *
     * @ORM\Column(name="UNTYP", type="string", length=8, nullable=false, options={"fixed"=true})
     */
    private $untyp;

    /**
     * @var int
     *
     * @ORM\Column(name="MOUV_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $mouvId;

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

    public function getCeb(): ?string
    {
        return $this->ceb;
    }

    public function setCeb(string $ceb): self
    {
        $this->ceb = $ceb;

        return $this;
    }

    public function getCec(): ?string
    {
        return $this->cec;
    }

    public function setCec(string $cec): self
    {
        $this->cec = $cec;

        return $this;
    }

    public function getCed(): ?string
    {
        return $this->ced;
    }

    public function setCed(string $ced): self
    {
        $this->ced = $ced;

        return $this;
    }

    public function getCee(): ?string
    {
        return $this->cee;
    }

    public function setCee(string $cee): self
    {
        $this->cee = $cee;

        return $this;
    }

    public function getCef(): ?string
    {
        return $this->cef;
    }

    public function setCef(string $cef): self
    {
        $this->cef = $cef;

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

    public function getTicod(): ?string
    {
        return $this->ticod;
    }

    public function setTicod(string $ticod): self
    {
        $this->ticod = $ticod;

        return $this;
    }

    public function getPicod(): ?string
    {
        return $this->picod;
    }

    public function setPicod(string $picod): self
    {
        $this->picod = $picod;

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

    public function getDvdt(): ?\DateTimeInterface
    {
        return $this->dvdt;
    }

    public function setDvdt(?\DateTimeInterface $dvdt): self
    {
        $this->dvdt = $dvdt;

        return $this;
    }

    public function getDvlg(): ?string
    {
        return $this->dvlg;
    }

    public function setDvlg(string $dvlg): self
    {
        $this->dvlg = $dvlg;

        return $this;
    }

    public function getDvslg(): ?string
    {
        return $this->dvslg;
    }

    public function setDvslg(string $dvslg): self
    {
        $this->dvslg = $dvslg;

        return $this;
    }

    public function getDvce4(): ?string
    {
        return $this->dvce4;
    }

    public function setDvce4(string $dvce4): self
    {
        $this->dvce4 = $dvce4;

        return $this;
    }

    public function getPrefcdno(): ?string
    {
        return $this->prefcdno;
    }

    public function setPrefcdno(string $prefcdno): self
    {
        $this->prefcdno = $prefcdno;

        return $this;
    }

    public function getCdno(): ?string
    {
        return $this->cdno;
    }

    public function setCdno(string $cdno): self
    {
        $this->cdno = $cdno;

        return $this;
    }

    public function getCddt(): ?\DateTimeInterface
    {
        return $this->cddt;
    }

    public function setCddt(?\DateTimeInterface $cddt): self
    {
        $this->cddt = $cddt;

        return $this;
    }

    public function getCdlg(): ?string
    {
        return $this->cdlg;
    }

    public function setCdlg(string $cdlg): self
    {
        $this->cdlg = $cdlg;

        return $this;
    }

    public function getCdslg(): ?string
    {
        return $this->cdslg;
    }

    public function setCdslg(string $cdslg): self
    {
        $this->cdslg = $cdslg;

        return $this;
    }

    public function getCdce4(): ?string
    {
        return $this->cdce4;
    }

    public function setCdce4(string $cdce4): self
    {
        $this->cdce4 = $cdce4;

        return $this;
    }

    public function getCdenrno(): ?string
    {
        return $this->cdenrno;
    }

    public function setCdenrno(string $cdenrno): self
    {
        $this->cdenrno = $cdenrno;

        return $this;
    }

    public function getPrefblno(): ?string
    {
        return $this->prefblno;
    }

    public function setPrefblno(string $prefblno): self
    {
        $this->prefblno = $prefblno;

        return $this;
    }

    public function getBlno(): ?string
    {
        return $this->blno;
    }

    public function setBlno(string $blno): self
    {
        $this->blno = $blno;

        return $this;
    }

    public function getBldt(): ?\DateTimeInterface
    {
        return $this->bldt;
    }

    public function setBldt(?\DateTimeInterface $bldt): self
    {
        $this->bldt = $bldt;

        return $this;
    }

    public function getBllg(): ?string
    {
        return $this->bllg;
    }

    public function setBllg(string $bllg): self
    {
        $this->bllg = $bllg;

        return $this;
    }

    public function getBlslg(): ?string
    {
        return $this->blslg;
    }

    public function setBlslg(string $blslg): self
    {
        $this->blslg = $blslg;

        return $this;
    }

    public function getBlce4(): ?string
    {
        return $this->blce4;
    }

    public function setBlce4(string $blce4): self
    {
        $this->blce4 = $blce4;

        return $this;
    }

    public function getPreffano(): ?string
    {
        return $this->preffano;
    }

    public function setPreffano(string $preffano): self
    {
        $this->preffano = $preffano;

        return $this;
    }

    public function getFano(): ?string
    {
        return $this->fano;
    }

    public function setFano(string $fano): self
    {
        $this->fano = $fano;

        return $this;
    }

    public function getFadt(): ?\DateTimeInterface
    {
        return $this->fadt;
    }

    public function setFadt(?\DateTimeInterface $fadt): self
    {
        $this->fadt = $fadt;

        return $this;
    }

    public function getFalg(): ?string
    {
        return $this->falg;
    }

    public function setFalg(string $falg): self
    {
        $this->falg = $falg;

        return $this;
    }

    public function getFaslg(): ?string
    {
        return $this->faslg;
    }

    public function setFaslg(string $faslg): self
    {
        $this->faslg = $faslg;

        return $this;
    }

    public function getFace4(): ?string
    {
        return $this->face4;
    }

    public function setFace4(string $face4): self
    {
        $this->face4 = $face4;

        return $this;
    }

    public function getBpno(): ?string
    {
        return $this->bpno;
    }

    public function setBpno(string $bpno): self
    {
        $this->bpno = $bpno;

        return $this;
    }

    public function getBpdt(): ?\DateTimeInterface
    {
        return $this->bpdt;
    }

    public function setBpdt(?\DateTimeInterface $bpdt): self
    {
        $this->bpdt = $bpdt;

        return $this;
    }

    public function getOp(): ?string
    {
        return $this->op;
    }

    public function setOp(string $op): self
    {
        $this->op = $op;

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

    public function getDepo(): ?string
    {
        return $this->depo;
    }

    public function setDepo(string $depo): self
    {
        $this->depo = $depo;

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

    public function getProjet(): ?string
    {
        return $this->projet;
    }

    public function setProjet(string $projet): self
    {
        $this->projet = $projet;

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

    public function getDes(): ?string
    {
        return $this->des;
    }

    public function setDes(string $des): self
    {
        $this->des = $des;

        return $this;
    }

    public function getReffo(): ?string
    {
        return $this->reffo;
    }

    public function setReffo(string $reffo): self
    {
        $this->reffo = $reffo;

        return $this;
    }

    public function getRepr0001(): ?string
    {
        return $this->repr0001;
    }

    public function setRepr0001(string $repr0001): self
    {
        $this->repr0001 = $repr0001;

        return $this;
    }

    public function getRepr0002(): ?string
    {
        return $this->repr0002;
    }

    public function setRepr0002(string $repr0002): self
    {
        $this->repr0002 = $repr0002;

        return $this;
    }

    public function getRepr0003(): ?string
    {
        return $this->repr0003;
    }

    public function setRepr0003(string $repr0003): self
    {
        $this->repr0003 = $repr0003;

        return $this;
    }

    public function getEnrno(): ?string
    {
        return $this->enrno;
    }

    public function setEnrno(string $enrno): self
    {
        $this->enrno = $enrno;

        return $this;
    }

    public function getTacod(): ?string
    {
        return $this->tacod;
    }

    public function setTacod(string $tacod): self
    {
        $this->tacod = $tacod;

        return $this;
    }

    public function getRemcod(): ?string
    {
        return $this->remcod;
    }

    public function setRemcod(string $remcod): self
    {
        $this->remcod = $remcod;

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

    public function getTafamrx(): ?string
    {
        return $this->tafamrx;
    }

    public function setTafamrx(string $tafamrx): self
    {
        $this->tafamrx = $tafamrx;

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

    public function getRefamrx(): ?string
    {
        return $this->refamrx;
    }

    public function setRefamrx(string $refamrx): self
    {
        $this->refamrx = $refamrx;

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

    public function getCofamv0001(): ?string
    {
        return $this->cofamv0001;
    }

    public function setCofamv0001(string $cofamv0001): self
    {
        $this->cofamv0001 = $cofamv0001;

        return $this;
    }

    public function getCofamv0002(): ?string
    {
        return $this->cofamv0002;
    }

    public function setCofamv0002(string $cofamv0002): self
    {
        $this->cofamv0002 = $cofamv0002;

        return $this;
    }

    public function getCofamv0003(): ?string
    {
        return $this->cofamv0003;
    }

    public function setCofamv0003(string $cofamv0003): self
    {
        $this->cofamv0003 = $cofamv0003;

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

    public function getVenun(): ?string
    {
        return $this->venun;
    }

    public function setVenun(string $venun): self
    {
        $this->venun = $venun;

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

    public function getPubun(): ?string
    {
        return $this->pubun;
    }

    public function setPubun(string $pubun): self
    {
        $this->pubun = $pubun;

        return $this;
    }

    public function getEmbun(): ?string
    {
        return $this->embun;
    }

    public function setEmbun(string $embun): self
    {
        $this->embun = $embun;

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

    public function getTxtedcod(): ?string
    {
        return $this->txtedcod;
    }

    public function setTxtedcod(string $txtedcod): self
    {
        $this->txtedcod = $txtedcod;

        return $this;
    }

    public function getPagcod(): ?string
    {
        return $this->pagcod;
    }

    public function setPagcod(string $pagcod): self
    {
        $this->pagcod = $pagcod;

        return $this;
    }

    public function getPriocod(): ?string
    {
        return $this->priocod;
    }

    public function setPriocod(string $priocod): self
    {
        $this->priocod = $priocod;

        return $this;
    }

    public function getAxe0001(): ?string
    {
        return $this->axe0001;
    }

    public function setAxe0001(string $axe0001): self
    {
        $this->axe0001 = $axe0001;

        return $this;
    }

    public function getAxe0002(): ?string
    {
        return $this->axe0002;
    }

    public function setAxe0002(string $axe0002): self
    {
        $this->axe0002 = $axe0002;

        return $this;
    }

    public function getAxe0003(): ?string
    {
        return $this->axe0003;
    }

    public function setAxe0003(string $axe0003): self
    {
        $this->axe0003 = $axe0003;

        return $this;
    }

    public function getAxe0004(): ?string
    {
        return $this->axe0004;
    }

    public function setAxe0004(string $axe0004): self
    {
        $this->axe0004 = $axe0004;

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

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getSens(): ?string
    {
        return $this->sens;
    }

    public function setSens(string $sens): self
    {
        $this->sens = $sens;

        return $this;
    }

    public function getAvenant(): ?string
    {
        return $this->avenant;
    }

    public function setAvenant(string $avenant): self
    {
        $this->avenant = $avenant;

        return $this;
    }

    public function getRelcod0001(): ?string
    {
        return $this->relcod0001;
    }

    public function setRelcod0001(string $relcod0001): self
    {
        $this->relcod0001 = $relcod0001;

        return $this;
    }

    public function getRelcod0002(): ?string
    {
        return $this->relcod0002;
    }

    public function setRelcod0002(string $relcod0002): self
    {
        $this->relcod0002 = $relcod0002;

        return $this;
    }

    public function getRelcod0003(): ?string
    {
        return $this->relcod0003;
    }

    public function setRelcod0003(string $relcod0003): self
    {
        $this->relcod0003 = $relcod0003;

        return $this;
    }

    public function getGamseq(): ?string
    {
        return $this->gamseq;
    }

    public function setGamseq(string $gamseq): self
    {
        $this->gamseq = $gamseq;

        return $this;
    }

    public function getPromotacod(): ?string
    {
        return $this->promotacod;
    }

    public function setPromotacod(string $promotacod): self
    {
        $this->promotacod = $promotacod;

        return $this;
    }

    public function getPromoremcod(): ?string
    {
        return $this->promoremcod;
    }

    public function setPromoremcod(string $promoremcod): self
    {
        $this->promoremcod = $promoremcod;

        return $this;
    }

    public function getPubtyp(): ?string
    {
        return $this->pubtyp;
    }

    public function setPubtyp(string $pubtyp): self
    {
        $this->pubtyp = $pubtyp;

        return $this;
    }

    public function getPaforf(): ?string
    {
        return $this->paforf;
    }

    public function setPaforf(string $paforf): self
    {
        $this->paforf = $paforf;

        return $this;
    }

    public function getPrefofno(): ?string
    {
        return $this->prefofno;
    }

    public function setPrefofno(string $prefofno): self
    {
        $this->prefofno = $prefofno;

        return $this;
    }

    public function getOfno(): ?string
    {
        return $this->ofno;
    }

    public function setOfno(string $ofno): self
    {
        $this->ofno = $ofno;

        return $this;
    }

    public function getPrefcdnopere(): ?string
    {
        return $this->prefcdnopere;
    }

    public function setPrefcdnopere(string $prefcdnopere): self
    {
        $this->prefcdnopere = $prefcdnopere;

        return $this;
    }

    public function getCdnopere(): ?string
    {
        return $this->cdnopere;
    }

    public function setCdnopere(string $cdnopere): self
    {
        $this->cdnopere = $cdnopere;

        return $this;
    }

    public function getLigne(): ?string
    {
        return $this->ligne;
    }

    public function setLigne(string $ligne): self
    {
        $this->ligne = $ligne;

        return $this;
    }

    public function getTicket(): ?string
    {
        return $this->ticket;
    }

    public function setTicket(string $ticket): self
    {
        $this->ticket = $ticket;

        return $this;
    }

    public function getAppremmt(): ?string
    {
        return $this->appremmt;
    }

    public function setAppremmt(string $appremmt): self
    {
        $this->appremmt = $appremmt;

        return $this;
    }

    public function getAppremmtun(): ?string
    {
        return $this->appremmtun;
    }

    public function setAppremmtun(string $appremmtun): self
    {
        $this->appremmtun = $appremmtun;

        return $this;
    }

    public function getConfigurateurmonostatus(): ?string
    {
        return $this->configurateurmonostatus;
    }

    public function setConfigurateurmonostatus(string $configurateurmonostatus): self
    {
        $this->configurateurmonostatus = $configurateurmonostatus;

        return $this;
    }

    public function getConfigurateurmultistatus(): ?string
    {
        return $this->configurateurmultistatus;
    }

    public function setConfigurateurmultistatus(string $configurateurmultistatus): self
    {
        $this->configurateurmultistatus = $configurateurmultistatus;

        return $this;
    }

    public function getConfigurateurlino(): ?string
    {
        return $this->configurateurlino;
    }

    public function setConfigurateurlino(string $configurateurlino): self
    {
        $this->configurateurlino = $configurateurlino;

        return $this;
    }

    public function getConfigurateurref(): ?string
    {
        return $this->configurateurref;
    }

    public function setConfigurateurref(string $configurateurref): self
    {
        $this->configurateurref = $configurateurref;

        return $this;
    }

    public function getConfigurateursref1(): ?string
    {
        return $this->configurateursref1;
    }

    public function setConfigurateursref1(string $configurateursref1): self
    {
        $this->configurateursref1 = $configurateursref1;

        return $this;
    }

    public function getConfigurateursref2(): ?string
    {
        return $this->configurateursref2;
    }

    public function setConfigurateursref2(string $configurateursref2): self
    {
        $this->configurateursref2 = $configurateursref2;

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

    public function getPub(): ?string
    {
        return $this->pub;
    }

    public function setPub(string $pub): self
    {
        $this->pub = $pub;

        return $this;
    }

    public function getPpar(): ?string
    {
        return $this->ppar;
    }

    public function setPpar(string $ppar): self
    {
        $this->ppar = $ppar;

        return $this;
    }

    public function getRem0001(): ?string
    {
        return $this->rem0001;
    }

    public function setRem0001(string $rem0001): self
    {
        $this->rem0001 = $rem0001;

        return $this;
    }

    public function getRem0002(): ?string
    {
        return $this->rem0002;
    }

    public function setRem0002(string $rem0002): self
    {
        $this->rem0002 = $rem0002;

        return $this;
    }

    public function getRem0003(): ?string
    {
        return $this->rem0003;
    }

    public function setRem0003(string $rem0003): self
    {
        $this->rem0003 = $rem0003;

        return $this;
    }

    public function getRemtyp0001(): ?string
    {
        return $this->remtyp0001;
    }

    public function setRemtyp0001(string $remtyp0001): self
    {
        $this->remtyp0001 = $remtyp0001;

        return $this;
    }

    public function getRemtyp0002(): ?string
    {
        return $this->remtyp0002;
    }

    public function setRemtyp0002(string $remtyp0002): self
    {
        $this->remtyp0002 = $remtyp0002;

        return $this;
    }

    public function getRemtyp0003(): ?string
    {
        return $this->remtyp0003;
    }

    public function setRemtyp0003(string $remtyp0003): self
    {
        $this->remtyp0003 = $remtyp0003;

        return $this;
    }

    public function getRemmt(): ?string
    {
        return $this->remmt;
    }

    public function setRemmt(string $remmt): self
    {
        $this->remmt = $remmt;

        return $this;
    }

    public function getPromotyp(): ?string
    {
        return $this->promotyp;
    }

    public function setPromotyp(string $promotyp): self
    {
        $this->promotyp = $promotyp;

        return $this;
    }

    public function getPustat(): ?string
    {
        return $this->pustat;
    }

    public function setPustat(string $pustat): self
    {
        $this->pustat = $pustat;

        return $this;
    }

    public function getQte1(): ?string
    {
        return $this->qte1;
    }

    public function setQte1(string $qte1): self
    {
        $this->qte1 = $qte1;

        return $this;
    }

    public function getQte2(): ?string
    {
        return $this->qte2;
    }

    public function setQte2(string $qte2): self
    {
        $this->qte2 = $qte2;

        return $this;
    }

    public function getQte3(): ?string
    {
        return $this->qte3;
    }

    public function setQte3(string $qte3): self
    {
        $this->qte3 = $qte3;

        return $this;
    }

    public function getDvqte(): ?string
    {
        return $this->dvqte;
    }

    public function setDvqte(string $dvqte): self
    {
        $this->dvqte = $dvqte;

        return $this;
    }

    public function getCdqte(): ?string
    {
        return $this->cdqte;
    }

    public function setCdqte(string $cdqte): self
    {
        $this->cdqte = $cdqte;

        return $this;
    }

    public function getBlqte(): ?string
    {
        return $this->blqte;
    }

    public function setBlqte(string $blqte): self
    {
        $this->blqte = $blqte;

        return $this;
    }

    public function getFaqte(): ?string
    {
        return $this->faqte;
    }

    public function setFaqte(string $faqte): self
    {
        $this->faqte = $faqte;

        return $this;
    }

    public function getRefqte(): ?string
    {
        return $this->refqte;
    }

    public function setRefqte(string $refqte): self
    {
        $this->refqte = $refqte;

        return $this;
    }

    public function getEmbqte(): ?string
    {
        return $this->embqte;
    }

    public function setEmbqte(string $embqte): self
    {
        $this->embqte = $embqte;

        return $this;
    }

    public function getComp0001(): ?string
    {
        return $this->comp0001;
    }

    public function setComp0001(string $comp0001): self
    {
        $this->comp0001 = $comp0001;

        return $this;
    }

    public function getComp0002(): ?string
    {
        return $this->comp0002;
    }

    public function setComp0002(string $comp0002): self
    {
        $this->comp0002 = $comp0002;

        return $this;
    }

    public function getComp0003(): ?string
    {
        return $this->comp0003;
    }

    public function setComp0003(string $comp0003): self
    {
        $this->comp0003 = $comp0003;

        return $this;
    }

    public function getCommt0001(): ?string
    {
        return $this->commt0001;
    }

    public function setCommt0001(string $commt0001): self
    {
        $this->commt0001 = $commt0001;

        return $this;
    }

    public function getCommt0002(): ?string
    {
        return $this->commt0002;
    }

    public function setCommt0002(string $commt0002): self
    {
        $this->commt0002 = $commt0002;

        return $this;
    }

    public function getCommt0003(): ?string
    {
        return $this->commt0003;
    }

    public function setCommt0003(string $commt0003): self
    {
        $this->commt0003 = $commt0003;

        return $this;
    }

    public function getMont(): ?string
    {
        return $this->mont;
    }

    public function setMont(string $mont): self
    {
        $this->mont = $mont;

        return $this;
    }

    public function getFraismt(): ?string
    {
        return $this->fraismt;
    }

    public function setFraismt(string $fraismt): self
    {
        $this->fraismt = $fraismt;

        return $this;
    }

    public function getDeccod(): ?string
    {
        return $this->deccod;
    }

    public function setDeccod(string $deccod): self
    {
        $this->deccod = $deccod;

        return $this;
    }

    public function getPcod0001(): ?string
    {
        return $this->pcod0001;
    }

    public function setPcod0001(string $pcod0001): self
    {
        $this->pcod0001 = $pcod0001;

        return $this;
    }

    public function getPcod0002(): ?string
    {
        return $this->pcod0002;
    }

    public function setPcod0002(string $pcod0002): self
    {
        $this->pcod0002 = $pcod0002;

        return $this;
    }

    public function getPcod0003(): ?string
    {
        return $this->pcod0003;
    }

    public function setPcod0003(string $pcod0003): self
    {
        $this->pcod0003 = $pcod0003;

        return $this;
    }

    public function getPcod0004(): ?string
    {
        return $this->pcod0004;
    }

    public function setPcod0004(string $pcod0004): self
    {
        $this->pcod0004 = $pcod0004;

        return $this;
    }

    public function getPcod0005(): ?string
    {
        return $this->pcod0005;
    }

    public function setPcod0005(string $pcod0005): self
    {
        $this->pcod0005 = $pcod0005;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

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

    public function getFillersens(): ?string
    {
        return $this->fillersens;
    }

    public function setFillersens(string $fillersens): self
    {
        $this->fillersens = $fillersens;

        return $this;
    }

    public function getMvcod(): ?string
    {
        return $this->mvcod;
    }

    public function setMvcod(string $mvcod): self
    {
        $this->mvcod = $mvcod;

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

    public function getQtetyp(): ?string
    {
        return $this->qtetyp;
    }

    public function setQtetyp(string $qtetyp): self
    {
        $this->qtetyp = $qtetyp;

        return $this;
    }

    public function getGadt(): ?\DateTimeInterface
    {
        return $this->gadt;
    }

    public function setGadt(?\DateTimeInterface $gadt): self
    {
        $this->gadt = $gadt;

        return $this;
    }

    public function getCrtotmt(): ?string
    {
        return $this->crtotmt;
    }

    public function setCrtotmt(string $crtotmt): self
    {
        $this->crtotmt = $crtotmt;

        return $this;
    }

    public function getCmptotmt(): ?string
    {
        return $this->cmptotmt;
    }

    public function setCmptotmt(string $cmptotmt): self
    {
        $this->cmptotmt = $cmptotmt;

        return $this;
    }

    public function getTxtcod(): ?string
    {
        return $this->txtcod;
    }

    public function setTxtcod(string $txtcod): self
    {
        $this->txtcod = $txtcod;

        return $this;
    }

    public function getTxtnote(): ?string
    {
        return $this->txtnote;
    }

    public function setTxtnote(string $txtnote): self
    {
        $this->txtnote = $txtnote;

        return $this;
    }

    public function getEnrnop0001(): ?string
    {
        return $this->enrnop0001;
    }

    public function setEnrnop0001(string $enrnop0001): self
    {
        $this->enrnop0001 = $enrnop0001;

        return $this;
    }

    public function getEnrnop0002(): ?string
    {
        return $this->enrnop0002;
    }

    public function setEnrnop0002(string $enrnop0002): self
    {
        $this->enrnop0002 = $enrnop0002;

        return $this;
    }

    public function getEnrnop0003(): ?string
    {
        return $this->enrnop0003;
    }

    public function setEnrnop0003(string $enrnop0003): self
    {
        $this->enrnop0003 = $enrnop0003;

        return $this;
    }

    public function getEnrnop0004(): ?string
    {
        return $this->enrnop0004;
    }

    public function setEnrnop0004(string $enrnop0004): self
    {
        $this->enrnop0004 = $enrnop0004;

        return $this;
    }

    public function getEnrnoc0001(): ?string
    {
        return $this->enrnoc0001;
    }

    public function setEnrnoc0001(string $enrnoc0001): self
    {
        $this->enrnoc0001 = $enrnoc0001;

        return $this;
    }

    public function getEnrnoc0002(): ?string
    {
        return $this->enrnoc0002;
    }

    public function setEnrnoc0002(string $enrnoc0002): self
    {
        $this->enrnoc0002 = $enrnoc0002;

        return $this;
    }

    public function getEnrnoc0003(): ?string
    {
        return $this->enrnoc0003;
    }

    public function setEnrnoc0003(string $enrnoc0003): self
    {
        $this->enrnoc0003 = $enrnoc0003;

        return $this;
    }

    public function getEnrnoc0004(): ?string
    {
        return $this->enrnoc0004;
    }

    public function setEnrnoc0004(string $enrnoc0004): self
    {
        $this->enrnoc0004 = $enrnoc0004;

        return $this;
    }

    public function getRempiemt0001(): ?string
    {
        return $this->rempiemt0001;
    }

    public function setRempiemt0001(string $rempiemt0001): self
    {
        $this->rempiemt0001 = $rempiemt0001;

        return $this;
    }

    public function getRempiemt0002(): ?string
    {
        return $this->rempiemt0002;
    }

    public function setRempiemt0002(string $rempiemt0002): self
    {
        $this->rempiemt0002 = $rempiemt0002;

        return $this;
    }

    public function getRempiemt0003(): ?string
    {
        return $this->rempiemt0003;
    }

    public function setRempiemt0003(string $rempiemt0003): self
    {
        $this->rempiemt0003 = $rempiemt0003;

        return $this;
    }

    public function getRempiemt0004(): ?string
    {
        return $this->rempiemt0004;
    }

    public function setRempiemt0004(string $rempiemt0004): self
    {
        $this->rempiemt0004 = $rempiemt0004;

        return $this;
    }

    public function getMvstat(): ?string
    {
        return $this->mvstat;
    }

    public function setMvstat(string $mvstat): self
    {
        $this->mvstat = $mvstat;

        return $this;
    }

    public function getBlasenrno(): ?string
    {
        return $this->blasenrno;
    }

    public function setBlasenrno(string $blasenrno): self
    {
        $this->blasenrno = $blasenrno;

        return $this;
    }

    public function getRempiepart0001(): ?string
    {
        return $this->rempiepart0001;
    }

    public function setRempiepart0001(string $rempiepart0001): self
    {
        $this->rempiepart0001 = $rempiepart0001;

        return $this;
    }

    public function getRempiepart0002(): ?string
    {
        return $this->rempiepart0002;
    }

    public function setRempiepart0002(string $rempiepart0002): self
    {
        $this->rempiepart0002 = $rempiepart0002;

        return $this;
    }

    public function getRempiepart0003(): ?string
    {
        return $this->rempiepart0003;
    }

    public function setRempiepart0003(string $rempiepart0003): self
    {
        $this->rempiepart0003 = $rempiepart0003;

        return $this;
    }

    public function getRempiepart0004(): ?string
    {
        return $this->rempiepart0004;
    }

    public function setRempiepart0004(string $rempiepart0004): self
    {
        $this->rempiepart0004 = $rempiepart0004;

        return $this;
    }

    public function getRecptno(): ?string
    {
        return $this->recptno;
    }

    public function setRecptno(string $recptno): self
    {
        $this->recptno = $recptno;

        return $this;
    }

    public function getPrgqte(): ?string
    {
        return $this->prgqte;
    }

    public function setPrgqte(string $prgqte): self
    {
        $this->prgqte = $prgqte;

        return $this;
    }

    public function getPrgrefqte(): ?string
    {
        return $this->prgrefqte;
    }

    public function setPrgrefqte(string $prgrefqte): self
    {
        $this->prgrefqte = $prgrefqte;

        return $this;
    }

    public function getDvenrno(): ?string
    {
        return $this->dvenrno;
    }

    public function setDvenrno(string $dvenrno): self
    {
        $this->dvenrno = $dvenrno;

        return $this;
    }

    public function getBlenrno(): ?string
    {
        return $this->blenrno;
    }

    public function setBlenrno(string $blenrno): self
    {
        $this->blenrno = $blenrno;

        return $this;
    }

    public function getRebucod(): ?string
    {
        return $this->rebucod;
    }

    public function setRebucod(string $rebucod): self
    {
        $this->rebucod = $rebucod;

        return $this;
    }

    public function getCtmfl(): ?string
    {
        return $this->ctmfl;
    }

    public function setCtmfl(string $ctmfl): self
    {
        $this->ctmfl = $ctmfl;

        return $this;
    }

    public function getPfcno(): ?string
    {
        return $this->pfcno;
    }

    public function setPfcno(string $pfcno): self
    {
        $this->pfcno = $pfcno;

        return $this;
    }

    public function getGpafl(): ?string
    {
        return $this->gpafl;
    }

    public function setGpafl(string $gpafl): self
    {
        $this->gpafl = $gpafl;

        return $this;
    }

    public function getElemno(): ?string
    {
        return $this->elemno;
    }

    public function setElemno(string $elemno): self
    {
        $this->elemno = $elemno;

        return $this;
    }

    public function getAfrindice(): ?string
    {
        return $this->afrindice;
    }

    public function setAfrindice(string $afrindice): self
    {
        $this->afrindice = $afrindice;

        return $this;
    }

    public function getBesoinno(): ?string
    {
        return $this->besoinno;
    }

    public function setBesoinno(string $besoinno): self
    {
        $this->besoinno = $besoinno;

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

    public function getContratcod(): ?string
    {
        return $this->contratcod;
    }

    public function setContratcod(string $contratcod): self
    {
        $this->contratcod = $contratcod;

        return $this;
    }

    public function getCadeaufl(): ?string
    {
        return $this->cadeaufl;
    }

    public function setCadeaufl(string $cadeaufl): self
    {
        $this->cadeaufl = $cadeaufl;

        return $this;
    }

    public function getEnrnocad(): ?string
    {
        return $this->enrnocad;
    }

    public function setEnrnocad(string $enrnocad): self
    {
        $this->enrnocad = $enrnocad;

        return $this;
    }

    public function getFraisfl(): ?string
    {
        return $this->fraisfl;
    }

    public function setFraisfl(string $fraisfl): self
    {
        $this->fraisfl = $fraisfl;

        return $this;
    }

    public function getFraisvalidtyp(): ?string
    {
        return $this->fraisvalidtyp;
    }

    public function setFraisvalidtyp(string $fraisvalidtyp): self
    {
        $this->fraisvalidtyp = $fraisvalidtyp;

        return $this;
    }

    public function getGratuitfl(): ?string
    {
        return $this->gratuitfl;
    }

    public function setGratuitfl(string $gratuitfl): self
    {
        $this->gratuitfl = $gratuitfl;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }

    public function getOptionfl(): ?string
    {
        return $this->optionfl;
    }

    public function setOptionfl(string $optionfl): self
    {
        $this->optionfl = $optionfl;

        return $this;
    }

    public function getOptionvalidefl(): ?string
    {
        return $this->optionvalidefl;
    }

    public function setOptionvalidefl(string $optionvalidefl): self
    {
        $this->optionvalidefl = $optionvalidefl;

        return $this;
    }

    public function getPanachefl(): ?string
    {
        return $this->panachefl;
    }

    public function setPanachefl(string $panachefl): self
    {
        $this->panachefl = $panachefl;

        return $this;
    }

    public function getPcod0006(): ?string
    {
        return $this->pcod0006;
    }

    public function setPcod0006(string $pcod0006): self
    {
        $this->pcod0006 = $pcod0006;

        return $this;
    }

    public function getPunetori(): ?string
    {
        return $this->punetori;
    }

    public function setPunetori(string $punetori): self
    {
        $this->punetori = $punetori;

        return $this;
    }

    public function getReglecod(): ?string
    {
        return $this->reglecod;
    }

    public function setReglecod(string $reglecod): self
    {
        $this->reglecod = $reglecod;

        return $this;
    }

    public function getRemcodcad(): ?string
    {
        return $this->remcodcad;
    }

    public function setRemcodcad(string $remcodcad): self
    {
        $this->remcodcad = $remcodcad;

        return $this;
    }

    public function getUntyp(): ?string
    {
        return $this->untyp;
    }

    public function setUntyp(string $untyp): self
    {
        $this->untyp = $untyp;

        return $this;
    }

    public function getMouvId(): ?int
    {
        return $this->mouvId;
    }


}
