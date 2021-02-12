<?php

namespace App\Entity\Divalto;

use App\Entity\Divalto\Cli;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Divalto\MouvRepository;

/**
 * Mouv
 * @ORM\Entity(repositoryClass="MouvRepository::class")
 * @ORM\Entity
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

    /**
     * @var string
     * @ORM\ManyToOne(targetEntity=Cli::class, inversedBy="tiers")
     * @ORM\JoinColumn(nullable=false, name="TIERS", referencedColumnName="TIERS")
     * @ORM\Column(name="TIERS", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Code tiers"})
     */
    private $tiers;
    
    public function getTiers(): ?Cli
    {
        return $this->tiers;
    }

    public function getMouvId(): ?int
    {
        return $this->mouvId;
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

    public function getCeb(): ?string
    {
        return $this->ceb;
    }

    public function getCec(): ?string
    {
        return $this->cec;
    }

    public function getCed(): ?string
    {
        return $this->ced;
    }

    public function getCee(): ?string
    {
        return $this->cee;
    }

    public function getCef(): ?string
    {
        return $this->cef;
    }

    public function getDos(): ?string
    {
        return $this->dos;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function getSref1(): ?string
    {
        return $this->sref1;
    }

    public function getSref2(): ?string
    {
        return $this->sref2;
    }

    public function getTicod(): ?string
    {
        return $this->ticod;
    }

    public function getPicod(): ?string
    {
        return $this->picod;
    }

    public function getPrefdvno(): ?string
    {
        return $this->prefdvno;
    }

    public function getDvno(): ?string
    {
        return $this->dvno;
    }

    public function getDvdt(): ?\DateTimeInterface
    {
        return $this->dvdt;
    }

    public function getDvlg(): ?string
    {
        return $this->dvlg;
    }

    public function getDvslg(): ?string
    {
        return $this->dvslg;
    }

    public function getDvce4(): ?string
    {
        return $this->dvce4;
    }

    public function getPrefcdno(): ?string
    {
        return $this->prefcdno;
    }

    public function getCdno(): ?string
    {
        return $this->cdno;
    }

    public function getCddt(): ?\DateTimeInterface
    {
        return $this->cddt;
    }

    public function getCdlg(): ?string
    {
        return $this->cdlg;
    }

    public function getCdslg(): ?string
    {
        return $this->cdslg;
    }

    public function getCdce4(): ?string
    {
        return $this->cdce4;
    }

    public function getCdenrno(): ?string
    {
        return $this->cdenrno;
    }

    public function getPrefblno(): ?string
    {
        return $this->prefblno;
    }

    public function getBlno(): ?string
    {
        return $this->blno;
    }

    public function getBldt(): ?\DateTimeInterface
    {
        return $this->bldt;
    }

    public function getBllg(): ?string
    {
        return $this->bllg;
    }

    public function getBlslg(): ?string
    {
        return $this->blslg;
    }

    public function getBlce4(): ?string
    {
        return $this->blce4;
    }

    public function getPreffano(): ?string
    {
        return $this->preffano;
    }

    public function getFano(): ?string
    {
        return $this->fano;
    }

    public function getFadt(): ?\DateTimeInterface
    {
        return $this->fadt;
    }

    public function getFalg(): ?string
    {
        return $this->falg;
    }

    public function getFaslg(): ?string
    {
        return $this->faslg;
    }

    public function getFace4(): ?string
    {
        return $this->face4;
    }

    public function getBpno(): ?string
    {
        return $this->bpno;
    }

    public function getBpdt(): ?\DateTimeInterface
    {
        return $this->bpdt;
    }

    public function getOp(): ?string
    {
        return $this->op;
    }

    public function getUsercr(): ?string
    {
        return $this->usercr;
    }

    public function getUsermo(): ?string
    {
        return $this->usermo;
    }

    public function getDepo(): ?string
    {
        return $this->depo;
    }

    public function getEtb(): ?string
    {
        return $this->etb;
    }

    public function getProjet(): ?string
    {
        return $this->projet;
    }

    public function getMarche(): ?string
    {
        return $this->marche;
    }

    public function getDes(): ?string
    {
        return $this->des;
    }

    public function getReffo(): ?string
    {
        return $this->reffo;
    }

    public function getRepr0001(): ?string
    {
        return $this->repr0001;
    }

    public function getRepr0002(): ?string
    {
        return $this->repr0002;
    }

    public function getRepr0003(): ?string
    {
        return $this->repr0003;
    }

    public function getEnrno(): ?string
    {
        return $this->enrno;
    }

    public function getTacod(): ?string
    {
        return $this->tacod;
    }

    public function getRemcod(): ?string
    {
        return $this->remcod;
    }

    public function getTafamr(): ?string
    {
        return $this->tafamr;
    }

    public function getTafamrx(): ?string
    {
        return $this->tafamrx;
    }

    public function getRefamr(): ?string
    {
        return $this->refamr;
    }

    public function getRefamrx(): ?string
    {
        return $this->refamrx;
    }

    public function getCofamr(): ?string
    {
        return $this->cofamr;
    }

    public function getCofamv0001(): ?string
    {
        return $this->cofamv0001;
    }

    public function getCofamv0002(): ?string
    {
        return $this->cofamv0002;
    }

    public function getCofamv0003(): ?string
    {
        return $this->cofamv0003;
    }

    public function getDev(): ?string
    {
        return $this->dev;
    }

    public function getVenun(): ?string
    {
        return $this->venun;
    }

    public function getRefun(): ?string
    {
        return $this->refun;
    }

    public function getPubun(): ?string
    {
        return $this->pubun;
    }

    public function getEmbun(): ?string
    {
        return $this->embun;
    }

    public function getEdcod(): ?string
    {
        return $this->edcod;
    }

    public function getTxtedcod(): ?string
    {
        return $this->txtedcod;
    }

    public function getPagcod(): ?string
    {
        return $this->pagcod;
    }

    public function getPriocod(): ?string
    {
        return $this->priocod;
    }

    public function getAxe0001(): ?string
    {
        return $this->axe0001;
    }

    public function getAxe0002(): ?string
    {
        return $this->axe0002;
    }

    public function getAxe0003(): ?string
    {
        return $this->axe0003;
    }

    public function getAxe0004(): ?string
    {
        return $this->axe0004;
    }

    public function getCptv(): ?string
    {
        return $this->cptv;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function getSens(): ?string
    {
        return $this->sens;
    }

    public function getAvenant(): ?string
    {
        return $this->avenant;
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

    public function getGamseq(): ?string
    {
        return $this->gamseq;
    }

    public function getPromotacod(): ?string
    {
        return $this->promotacod;
    }

    public function getPromoremcod(): ?string
    {
        return $this->promoremcod;
    }

    public function getPubtyp(): ?string
    {
        return $this->pubtyp;
    }

    public function getPaforf(): ?string
    {
        return $this->paforf;
    }

    public function getPrefofno(): ?string
    {
        return $this->prefofno;
    }

    public function getOfno(): ?string
    {
        return $this->ofno;
    }

    public function getPrefcdnopere(): ?string
    {
        return $this->prefcdnopere;
    }

    public function getCdnopere(): ?string
    {
        return $this->cdnopere;
    }

    public function getLigne(): ?string
    {
        return $this->ligne;
    }

    public function getTicket(): ?string
    {
        return $this->ticket;
    }

    public function getAppremmt(): ?string
    {
        return $this->appremmt;
    }

    public function getAppremmtun(): ?string
    {
        return $this->appremmtun;
    }

    public function getConfigurateurmonostatus(): ?string
    {
        return $this->configurateurmonostatus;
    }

    public function getConfigurateurmultistatus(): ?string
    {
        return $this->configurateurmultistatus;
    }

    public function getConfigurateurlino(): ?string
    {
        return $this->configurateurlino;
    }

    public function getConfigurateurref(): ?string
    {
        return $this->configurateurref;
    }

    public function getConfigurateursref1(): ?string
    {
        return $this->configurateursref1;
    }

    public function getConfigurateursref2(): ?string
    {
        return $this->configurateursref2;
    }

    public function getUsercrdh(): ?\DateTimeInterface
    {
        return $this->usercrdh;
    }

    public function getUsermodh(): ?\DateTimeInterface
    {
        return $this->usermodh;
    }

    public function getCenote(): ?string
    {
        return $this->cenote;
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

    public function getPpar(): ?string
    {
        return $this->ppar;
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

    public function getRemmt(): ?string
    {
        return $this->remmt;
    }

    public function getPromotyp(): ?string
    {
        return $this->promotyp;
    }

    public function getPustat(): ?string
    {
        return $this->pustat;
    }

    public function getQte1(): ?string
    {
        return $this->qte1;
    }

    public function getQte2(): ?string
    {
        return $this->qte2;
    }

    public function getQte3(): ?string
    {
        return $this->qte3;
    }

    public function getDvqte(): ?string
    {
        return $this->dvqte;
    }

    public function getCdqte(): ?string
    {
        return $this->cdqte;
    }

    public function getBlqte(): ?string
    {
        return $this->blqte;
    }

    public function getFaqte(): ?string
    {
        return $this->faqte;
    }

    public function getRefqte(): ?string
    {
        return $this->refqte;
    }

    public function getEmbqte(): ?string
    {
        return $this->embqte;
    }

    public function getComp0001(): ?string
    {
        return $this->comp0001;
    }

    public function getComp0002(): ?string
    {
        return $this->comp0002;
    }

    public function getComp0003(): ?string
    {
        return $this->comp0003;
    }

    public function getCommt0001(): ?string
    {
        return $this->commt0001;
    }

    public function getCommt0002(): ?string
    {
        return $this->commt0002;
    }

    public function getCommt0003(): ?string
    {
        return $this->commt0003;
    }

    public function getMont(): ?string
    {
        return $this->mont;
    }

    public function getFraismt(): ?string
    {
        return $this->fraismt;
    }

    public function getDeccod(): ?string
    {
        return $this->deccod;
    }

    public function getPcod0001(): ?string
    {
        return $this->pcod0001;
    }

    public function getPcod0002(): ?string
    {
        return $this->pcod0002;
    }

    public function getPcod0003(): ?string
    {
        return $this->pcod0003;
    }

    public function getPcod0004(): ?string
    {
        return $this->pcod0004;
    }

    public function getPcod0005(): ?string
    {
        return $this->pcod0005;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getStres(): ?string
    {
        return $this->stres;
    }

    public function getFillersens(): ?string
    {
        return $this->fillersens;
    }

    public function getMvcod(): ?string
    {
        return $this->mvcod;
    }

    public function getPvcod(): ?string
    {
        return $this->pvcod;
    }

    public function getQtetyp(): ?string
    {
        return $this->qtetyp;
    }

    public function getGadt(): ?\DateTimeInterface
    {
        return $this->gadt;
    }

    public function getCrtotmt(): ?string
    {
        return $this->crtotmt;
    }

    public function getCmptotmt(): ?string
    {
        return $this->cmptotmt;
    }

    public function getTxtcod(): ?string
    {
        return $this->txtcod;
    }

    public function getTxtnote(): ?string
    {
        return $this->txtnote;
    }

    public function getEnrnop0001(): ?string
    {
        return $this->enrnop0001;
    }

    public function getEnrnop0002(): ?string
    {
        return $this->enrnop0002;
    }

    public function getEnrnop0003(): ?string
    {
        return $this->enrnop0003;
    }

    public function getEnrnop0004(): ?string
    {
        return $this->enrnop0004;
    }

    public function getEnrnoc0001(): ?string
    {
        return $this->enrnoc0001;
    }

    public function getEnrnoc0002(): ?string
    {
        return $this->enrnoc0002;
    }

    public function getEnrnoc0003(): ?string
    {
        return $this->enrnoc0003;
    }

    public function getEnrnoc0004(): ?string
    {
        return $this->enrnoc0004;
    }

    public function getRempiemt0001(): ?string
    {
        return $this->rempiemt0001;
    }

    public function getRempiemt0002(): ?string
    {
        return $this->rempiemt0002;
    }

    public function getRempiemt0003(): ?string
    {
        return $this->rempiemt0003;
    }

    public function getRempiemt0004(): ?string
    {
        return $this->rempiemt0004;
    }

    public function getMvstat(): ?string
    {
        return $this->mvstat;
    }

    public function getBlasenrno(): ?string
    {
        return $this->blasenrno;
    }

    public function getRempiepart0001(): ?string
    {
        return $this->rempiepart0001;
    }

    public function getRempiepart0002(): ?string
    {
        return $this->rempiepart0002;
    }

    public function getRempiepart0003(): ?string
    {
        return $this->rempiepart0003;
    }

    public function getRempiepart0004(): ?string
    {
        return $this->rempiepart0004;
    }

    public function getRecptno(): ?string
    {
        return $this->recptno;
    }

    public function getPrgqte(): ?string
    {
        return $this->prgqte;
    }

    public function getPrgrefqte(): ?string
    {
        return $this->prgrefqte;
    }

    public function getDvenrno(): ?string
    {
        return $this->dvenrno;
    }

    public function getBlenrno(): ?string
    {
        return $this->blenrno;
    }

    public function getRebucod(): ?string
    {
        return $this->rebucod;
    }

    public function getCtmfl(): ?string
    {
        return $this->ctmfl;
    }

    public function getPfcno(): ?string
    {
        return $this->pfcno;
    }

    public function getGpafl(): ?string
    {
        return $this->gpafl;
    }

    public function getElemno(): ?string
    {
        return $this->elemno;
    }

    public function getAfrindice(): ?string
    {
        return $this->afrindice;
    }

    public function getBesoinno(): ?string
    {
        return $this->besoinno;
    }

    public function getTvaart(): ?string
    {
        return $this->tvaart;
    }

    public function getContratcod(): ?string
    {
        return $this->contratcod;
    }

    public function getCadeaufl(): ?string
    {
        return $this->cadeaufl;
    }

    public function getEnrnocad(): ?string
    {
        return $this->enrnocad;
    }

    public function getFraisfl(): ?string
    {
        return $this->fraisfl;
    }

    public function getFraisvalidtyp(): ?string
    {
        return $this->fraisvalidtyp;
    }

    public function getGratuitfl(): ?string
    {
        return $this->gratuitfl;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function getOptionfl(): ?string
    {
        return $this->optionfl;
    }

    public function getOptionvalidefl(): ?string
    {
        return $this->optionvalidefl;
    }

    public function getPanachefl(): ?string
    {
        return $this->panachefl;
    }

    public function getPcod0006(): ?string
    {
        return $this->pcod0006;
    }

    public function getPunetori(): ?string
    {
        return $this->punetori;
    }

    public function getReglecod(): ?string
    {
        return $this->reglecod;
    }

    public function getRemcodcad(): ?string
    {
        return $this->remcodcad;
    }

    public function getUntyp(): ?string
    {
        return $this->untyp;
    }

    
}
