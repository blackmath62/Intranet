<?php

namespace App\Entity\Divalto;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\EntRepository;

/**
 * Ent
 * @ORM\Entity
 * @ORM\Table(name="ENT", indexes={@ORM\Index(name="INDEX_A", columns={"DOS", "TICOD", "PICOD", "PIDT", "PREFPINO", "PINO", "ENT_ID"}), @ORM\Index(name="INDEX_B", columns={"DOS", "TICOD", "PICOD", "TIERS", "PIDT", "PREFPINO", "PINO", "ENT_ID"}), @ORM\Index(name="INDEX_C", columns={"DOS", "TICOD", "PICOD", "PIDT", "PREFPINO", "PINO", "ENT_ID"}), @ORM\Index(name="INDEX_D", columns={"DOS", "TICOD", "PICOD", "TIERS", "PIDT", "PREFPINO", "PINO", "ENT_ID"}), @ORM\Index(name="INDEX_E", columns={"DOS", "TICOD", "PICOD", "STATUS", "PREFPINO", "PINO", "ENT_ID"}), @ORM\Index(name="INDEX_F", columns={"DOS", "TICOD", "PICOD", "STATUS", "TIERS", "PREFPINO", "PINO", "ENT_ID"}), @ORM\Index(name="INDEX_G", columns={"DOS", "TICOD", "PICOD", "PREFPINO", "PINO", "ENT_ID"}), @ORM\Index(name="INDEX_H", columns={"DOS", "TICOD", "PICOD", "TIERS", "PREFPINO", "PINO", "ENT_ID"}), @ORM\Index(name="INDEX_I", columns={"DOS", "TICOD", "PICOD", "PREFPINO", "PINO", "ENT_ID"}), @ORM\Index(name="INDEX_J", columns={"DOS", "TICOD", "PICOD", "TIERS", "PREFPINO", "PINO", "ENT_ID"}), @ORM\Index(name="INDEX_K", columns={"DOS", "TICOD", "PICOD", "CE4", "PREFPINO", "PINO", "ENT_ID"}), @ORM\Index(name="INDEX_L", columns={"DOS", "TICOD", "PICOD", "CE4", "TIERS", "PREFPINO", "PINO", "ENT_ID"}), @ORM\Index(name="INDEX_M", columns={"DOS", "PREFRLVNO", "RLVNO", "TIERSRLV", "PREFPINO", "PINO", "ENT_ID"}), @ORM\Index(name="INDEX_N", columns={"DOS", "TICOD", "PICOD", "CE4", "TIERS", "PINOTIERS", "ENT_ID"}), @ORM\Index(name="INDEX_O", columns={"DOS", "TICOD", "PICOD", "TIERS", "PINOTIERS", "ENT_ID"}), @ORM\Index(name="INDEX_P", columns={"DOS", "TICOD", "CE2", "TIERS", "PREFPINO", "PINO", "ENT_ID"}), @ORM\Index(name="INDEX_S", columns={"CE7", "DOS", "TICOD", "PICOD", "PREFCDNOPERE", "CDNOPERE", "ENT_ID"}), @ORM\Index(name="INDEX_T", columns={"DOS", "VERSIONDEVISORIPREFPINO", "VERSIONDEVISORIPINO", "VERSIONDEVISNO", "ENT_ID"})})
 */
class Ent
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
     * @ORM\Column(name="PREFPINO", type="string", length=10, nullable=false, options={"fixed"=true,"comment"="Préfixe du numéro de pièce"})
     */
    private $prefpino;

    /**
     * @var string
     *
     * @ORM\Column(name="PINO", type="decimal", precision=10, scale=0, nullable=false, options={"comment"="Pièce no"})
     */
    private $pino;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="PIDT", type="date", nullable=true, options={"comment"="Date de la pièce"})
     */
    private $pidt;

    /**
     * @var string
     *
     * @ORM\Column(name="ETB", type="string", length=3, nullable=false, options={"fixed"=true,"comment"="Etablissement"})
     */
    private $etb;

    /**
     * @var string
     *
     * @ORM\Column(name="STATUS", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Status de la pièce"})
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="DEV", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Devise"})
     */
    private $dev;

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
     * @ORM\Column(name="RIBCOD", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Code rib"})
     */
    private $ribcod;

    /**
     * @var string
     *
     * @ORM\Column(name="MARCHE", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Code marché"})
     */
    private $marche;

    /**
     * @var string
     *
     * @ORM\Column(name="PROJET", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Projet"})
     */
    private $projet;

    /**
     * @var string
     *
     * @ORM\Column(name="DEPO", type="string", length=3, nullable=false, options={"fixed"=true,"comment"="Dépôt"})
     */
    private $depo;

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
     * @ORM\Column(name="BLMOD", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Mode de transport"})
     */
    private $blmod;

    /**
     * @var string
     *
     * @ORM\Column(name="REGL", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Mode de règlement"})
     */
    private $regl;

    /**
     * @var string
     *
     * @ORM\Column(name="TOUR", type="string", length=6, nullable=false, options={"fixed"=true,"comment"="Tournée"})
     */
    private $tour;

    /**
     * @var string
     *
     * @ORM\Column(name="PIREF", type="string", length=40, nullable=false, options={"fixed"=true,"comment"="Référence de la pièce"})
     */
    private $piref;

    /**
     * @var string
     *
     * @ORM\Column(name="PINOTIERS", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="No de pièce fournisseur"})
     */
    private $pinotiers;

    /**
     * @var string
     *
     * @ORM\Column(name="TIERSPAYER", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Tiers payeur"})
     */
    private $tierspayer;

    /**
     * @var string
     *
     * @ORM\Column(name="TIERSGRP", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Code groupement tiers"})
     */
    private $tiersgrp;

    /**
     * @var string
     *
     * @ORM\Column(name="TIERSRLV", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Tiers relevé"})
     */
    private $tiersrlv;

    /**
     * @var string
     *
     * @ORM\Column(name="BAPSALCOD", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Signataire du bon à payer"})
     */
    private $bapsalcod;

    /**
     * @var string
     *
     * @ORM\Column(name="SALCOD", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Code salarié"})
     */
    private $salcod;

    /**
     * @var string
     *
     * @ORM\Column(name="PREFRLVNO", type="string", length=10, nullable=false, options={"fixed"=true,"comment"="Préfixe du no de relevé"})
     */
    private $prefrlvno;

    /**
     * @var string
     *
     * @ORM\Column(name="RLVNO", type="decimal", precision=10, scale=0, nullable=false, options={"comment"="No de relevé"})
     */
    private $rlvno;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="RLVDT", type="date", nullable=true, options={"comment"="Date de relevé"})
     */
    private $rlvdt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="DELDEMDT", type="date", nullable=true, options={"comment"="Délai demandé"})
     */
    private $deldemdt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="DELACCDT", type="date", nullable=true, options={"comment"="Délai accepté"})
     */
    private $delaccdt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="DELREPDT", type="date", nullable=true, options={"comment"="Délai reporté"})
     */
    private $delrepdt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="ECHDT", type="date", nullable=true, options={"comment"="Date d échéance"})
     */
    private $echdt;

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
     * @ORM\Column(name="COFAM", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Famille commission"})
     */
    private $cofam;

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
     * @ORM\Column(name="ETANO", type="string", length=1, nullable=false, options={"fixed"=true,"comment"="Numéro  d état"})
     */
    private $etano;

    /**
     * @var string
     *
     * @ORM\Column(name="TXTEDCODD", type="string", length=5, nullable=false, options={"fixed"=true,"comment"="Code impression texte en-tête"})
     */
    private $txtedcodd;

    /**
     * @var string
     *
     * @ORM\Column(name="TXTEDCODF", type="string", length=5, nullable=false, options={"fixed"=true,"comment"="Code impression texte fin"})
     */
    private $txtedcodf;

    /**
     * @var string
     *
     * @ORM\Column(name="CONTACT", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Contact"})
     */
    private $contact;

    /**
     * @var string
     *
     * @ORM\Column(name="PREFBLASNO", type="string", length=10, nullable=false, options={"fixed"=true,"comment"="Préfixe no de BL associé"})
     */
    private $prefblasno;

    /**
     * @var string
     *
     * @ORM\Column(name="BLASNO", type="decimal", precision=10, scale=0, nullable=false, options={"comment"="No de BL associé"})
     */
    private $blasno;

    /**
     * @var string
     *
     * @ORM\Column(name="BLASDEPO", type="string", length=3, nullable=false, options={"fixed"=true,"comment"="Dépôt du BL associé"})
     */
    private $blasdepo;

    /**
     * @var string
     *
     * @ORM\Column(name="TPFT", type="string", length=1, nullable=false, options={"fixed"=true,"comment"="Régime TPF tiers"})
     */
    private $tpft;

    /**
     * @var string
     *
     * @ORM\Column(name="AVENANT", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Avenant"})
     */
    private $avenant;

    /**
     * @var string
     *
     * @ORM\Column(name="CESINTCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Cession inter-établissement"})
     */
    private $cesintcod;

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
     * @ORM\Column(name="TPVBL", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Génération Bl par tpv"})
     */
    private $tpvbl;

    /**
     * @var string
     *
     * @ORM\Column(name="DEEEINCCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Inclure l éco-contribution au prix"})
     */
    private $deeeinccod;

    /**
     * @var string
     *
     * @ORM\Column(name="CONFIGURATEURFORMULAIRE", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Formulaire du configurateur"})
     */
    private $configurateurformulaire;

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
     * @ORM\Column(name="CONFIGURATEURPINO", type="decimal", precision=8, scale=0, nullable=false, options={"comment"="Numero de pièce pour le configurateur"})
     */
    private $configurateurpino;

    /**
     * @var string
     *
     * @ORM\Column(name="CONFIGURATEURSTATUS", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Statut de la configuration"})
     */
    private $configurateurstatus;

    /**
     * @var string
     *
     * @ORM\Column(name="PREFPINA", type="string", length=10, nullable=false, options={"fixed"=true,"comment"="Préfixe numéro de la pièce de régularisation associée"})
     */
    private $prefpina;

    /**
     * @var string
     *
     * @ORM\Column(name="PINA", type="decimal", precision=10, scale=0, nullable=false, options={"comment"="Numéro de la pièce de régularisation associée"})
     */
    private $pina;

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
     * @ORM\Column(name="TXTCODD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Indicateur texte en-tête"})
     */
    private $txtcodd;

    /**
     * @var string
     *
     * @ORM\Column(name="TXTCODF", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Indicateur texte pied"})
     */
    private $txtcodf;

    /**
     * @var string
     *
     * @ORM\Column(name="TXTNOTED", type="decimal", precision=8, scale=0, nullable=false, options={"comment"="Numéro de note  en-tête"})
     */
    private $txtnoted;

    /**
     * @var string
     *
     * @ORM\Column(name="TXTNOTEF", type="decimal", precision=8, scale=0, nullable=false, options={"comment"="Numéro de note pied"})
     */
    private $txtnotef;

    /**
     * @var string
     *
     * @ORM\Column(name="ORIGINE", type="decimal", precision=2, scale=0, nullable=false, options={"comment"="Origine de l enregistrement"})
     */
    private $origine;

    /**
     * @var string
     *
     * @ORM\Column(name="HTMT", type="decimal", precision=13, scale=2, nullable=false, options={"comment"="Montant HT"})
     */
    private $htmt;

    /**
     * @var string
     *
     * @ORM\Column(name="TTCMT", type="decimal", precision=13, scale=2, nullable=false, options={"comment"="TTC de la pièce"})
     */
    private $ttcmt;

    /**
     * @var string
     *
     * @ORM\Column(name="HTPDTMT", type="decimal", precision=13, scale=2, nullable=false, options={"comment"="HT produit"})
     */
    private $htpdtmt;

    /**
     * @var string
     *
     * @ORM\Column(name="ESCP", type="decimal", precision=5, scale=2, nullable=false, options={"comment"="Taux escompte"})
     */
    private $escp;

    /**
     * @var string
     *
     * @ORM\Column(name="ACMT", type="decimal", precision=12, scale=2, nullable=false, options={"comment"="Montant acompte"})
     */
    private $acmt;

    /**
     * @var string
     *
     * @ORM\Column(name="SOACMT", type="decimal", precision=12, scale=2, nullable=false, options={"comment"="Solde de l acompte"})
     */
    private $soacmt;

    /**
     * @var string
     *
     * @ORM\Column(name="REMMT", type="decimal", precision=9, scale=2, nullable=false, options={"comment"="Remise en montant"})
     */
    private $remmt;

    /**
     * @var string
     *
     * @ORM\Column(name="REM1", type="decimal", precision=6, scale=2, nullable=false, options={"comment"="% remise"})
     */
    private $rem1;

    /**
     * @var string
     *
     * @ORM\Column(name="REMTYP1", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Type de la remise pied"})
     */
    private $remtyp1;

    /**
     * @var string
     *
     * @ORM\Column(name="FOUHTMT", type="decimal", precision=13, scale=2, nullable=false, options={"comment"="HT du fournisseur"})
     */
    private $fouhtmt;

    /**
     * @var string
     *
     * @ORM\Column(name="FOUESCMT", type="decimal", precision=13, scale=2, nullable=false, options={"comment"="Montant escompte fournisseur"})
     */
    private $fouescmt;

    /**
     * @var string
     *
     * @ORM\Column(name="FOUTVAMT", type="decimal", precision=13, scale=2, nullable=false, options={"comment"="Montant tva fournisseur"})
     */
    private $foutvamt;

    /**
     * @var string
     *
     * @ORM\Column(name="DEVP", type="decimal", precision=17, scale=8, nullable=false, options={"comment"="Taux de la devise"})
     */
    private $devp;

    /**
     * @var string
     *
     * @ORM\Column(name="PIEDNO_0001", type="decimal", precision=2, scale=0, nullable=false, options={"comment"="Pied de pièce client"})
     */
    private $piedno0001;

    /**
     * @var string
     *
     * @ORM\Column(name="PIEDNO_0002", type="decimal", precision=2, scale=0, nullable=false, options={"comment"="Pied de pièce client"})
     */
    private $piedno0002;

    /**
     * @var string
     *
     * @ORM\Column(name="PIEDNO_0003", type="decimal", precision=2, scale=0, nullable=false, options={"comment"="Pied de pièce client"})
     */
    private $piedno0003;

    /**
     * @var string
     *
     * @ORM\Column(name="PIEDMT_0001", type="decimal", precision=12, scale=2, nullable=false, options={"comment"="Montant pied de pièce"})
     */
    private $piedmt0001;

    /**
     * @var string
     *
     * @ORM\Column(name="PIEDMT_0002", type="decimal", precision=12, scale=2, nullable=false, options={"comment"="Montant pied de pièce"})
     */
    private $piedmt0002;

    /**
     * @var string
     *
     * @ORM\Column(name="PIEDMT_0003", type="decimal", precision=12, scale=2, nullable=false, options={"comment"="Montant pied de pièce"})
     */
    private $piedmt0003;

    /**
     * @var string
     *
     * @ORM\Column(name="NBEX", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Nombre exemplaire"})
     */
    private $nbex;

    /**
     * @var string
     *
     * @ORM\Column(name="PIRELCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Pièce en reliquat"})
     */
    private $pirelcod;

    /**
     * @var string
     *
     * @ORM\Column(name="RELCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Reliquat ?"})
     */
    private $relcod;

    /**
     * @var string
     *
     * @ORM\Column(name="EDITCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Code édition"})
     */
    private $editcod;

    /**
     * @var string
     *
     * @ORM\Column(name="TRCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Code traite imprimée"})
     */
    private $trcod;

    /**
     * @var string
     *
     * @ORM\Column(name="BOREDICOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Code édition bordereau"})
     */
    private $boredicod;

    /**
     * @var string
     *
     * @ORM\Column(name="ASCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Indicateur BL associé"})
     */
    private $ascod;

    /**
     * @var string
     *
     * @ORM\Column(name="ECHVCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Ventilation échéance non/oui"})
     */
    private $echvcod;

    /**
     * @var string
     *
     * @ORM\Column(name="ENCASSCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Encours assurance"})
     */
    private $encasscod;

    /**
     * @var string
     *
     * @ORM\Column(name="ADRTYP_0001", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Type d adresse"})
     */
    private $adrtyp0001;

    /**
     * @var string
     *
     * @ORM\Column(name="ADRTYP_0002", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Type d adresse"})
     */
    private $adrtyp0002;

    /**
     * @var string
     *
     * @ORM\Column(name="ADRTYP_0003", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Type d adresse"})
     */
    private $adrtyp0003;

    /**
     * @var string
     *
     * @ORM\Column(name="ADRTYP_0004", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Type d adresse"})
     */
    private $adrtyp0004;

    /**
     * @var string
     *
     * @ORM\Column(name="ADRTYP_0005", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Type d adresse"})
     */
    private $adrtyp0005;

    /**
     * @var string
     *
     * @ORM\Column(name="PRIOCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Code priorité"})
     */
    private $priocod;

    /**
     * @var string
     *
     * @ORM\Column(name="HTCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Type tarif (HT ou TTC)"})
     */
    private $htcod;

    /**
     * @var string
     *
     * @ORM\Column(name="STRES", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Réservation du stock ?"})
     */
    private $stres;

    /**
     * @var string
     *
     * @ORM\Column(name="FAMOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Mode de facturation"})
     */
    private $famod;

    /**
     * @var string
     *
     * @ORM\Column(name="PERIOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Périodicité de facturation"})
     */
    private $period;

    /**
     * @var string
     *
     * @ORM\Column(name="PORCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Port mode de calcul"})
     */
    private $porcod;

    /**
     * @var string
     *
     * @ORM\Column(name="POICOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Code poids saisi"})
     */
    private $poicod;

    /**
     * @var string
     *
     * @ORM\Column(name="VOLCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Code volume saisi"})
     */
    private $volcod;

    /**
     * @var string
     *
     * @ORM\Column(name="PORFRFL", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Indicateur si franco non/oui"})
     */
    private $porfrfl;

    /**
     * @var string
     *
     * @ORM\Column(name="POITOT", type="decimal", precision=12, scale=3, nullable=false, options={"comment"="Masse totale"})
     */
    private $poitot;

    /**
     * @var string
     *
     * @ORM\Column(name="VOLTOT", type="decimal", precision=15, scale=3, nullable=false, options={"comment"="Volume total"})
     */
    private $voltot;

    /**
     * @var string
     *
     * @ORM\Column(name="COLINB", type="decimal", precision=4, scale=0, nullable=false, options={"comment"="Nombre de colis"})
     */
    private $colinb;

    /**
     * @var string
     *
     * @ORM\Column(name="REFNB", type="decimal", precision=8, scale=0, nullable=false, options={"comment"="Nombre de références"})
     */
    private $refnb;

    /**
     * @var string
     *
     * @ORM\Column(name="TOURRG", type="decimal", precision=4, scale=0, nullable=false, options={"comment"="Rang dans la tournée"})
     */
    private $tourrg;

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
     * @ORM\Column(name="PORTHEOMT", type="decimal", precision=12, scale=2, nullable=false, options={"comment"="Port théorique"})
     */
    private $portheomt;

    /**
     * @var string
     *
     * @ORM\Column(name="REMPIETOT", type="decimal", precision=12, scale=2, nullable=false, options={"comment"="Remise pied totale"})
     */
    private $rempietot;

    /**
     * @var string
     *
     * @ORM\Column(name="TRANSJRNB", type="decimal", precision=3, scale=0, nullable=false, options={"comment"="Nombre de jours de transport"})
     */
    private $transjrnb;

    /**
     * @var string
     *
     * @ORM\Column(name="OFASCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Of associé non/oui"})
     */
    private $ofascod;

    /**
     * @var string
     *
     * @ORM\Column(name="FINAL", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Pièce au stade final n/o"})
     */
    private $final;

    /**
     * @var string
     *
     * @ORM\Column(name="QUACOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Controle qualité"})
     */
    private $quacod;

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
     * @ORM\Column(name="DEEEMT", type="decimal", precision=12, scale=2, nullable=false, options={"comment"="Montant de l éco-contribution"})
     */
    private $deeemt;

    /**
     * @var string
     *
     * @ORM\Column(name="FOUDEEEMT", type="decimal", precision=12, scale=2, nullable=false, options={"comment"="Montant de l éco-contribution fournisseur"})
     */
    private $foudeeemt;

    /**
     * @var string
     *
     * @ORM\Column(name="PRGCDEFLG", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Indicateur commande programme"})
     */
    private $prgcdeflg;

    /**
     * @var string
     *
     * @ORM\Column(name="BQCPCE", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Compte bancaire prév."})
     */
    private $bqcpce;

    /**
     * @var string
     *
     * @ORM\Column(name="POINCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Code poids net saisi"})
     */
    private $poincod;

    /**
     * @var string
     *
     * @ORM\Column(name="POINTOT", type="decimal", precision=12, scale=3, nullable=false, options={"comment"="Masse totale nette"})
     */
    private $pointot;

    /**
     * @var string
     *
     * @ORM\Column(name="PRIOREG", type="decimal", precision=2, scale=0, nullable=false, options={"comment"="Priorité du décaissement du fournisseur"})
     */
    private $prioreg;

    /**
     * @var string
     *
     * @ORM\Column(name="TVATIE", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Régime TVA tiers"})
     */
    private $tvatie;

    /**
     * @var string
     *
     * @ORM\Column(name="STLGTGAMCOD", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Code de la gamme"})
     */
    private $stlgtgamcod;

    /**
     * @var string
     *
     * @ORM\Column(name="DTFLG", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Piece devis technique"})
     */
    private $dtflg;

    /**
     * @var string
     *
     * @ORM\Column(name="SYNCHROFL", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Activer synchro intercompagnie OUI/NON"})
     */
    private $synchrofl;

    /**
     * @var string
     *
     * @ORM\Column(name="ICPFL", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Element synchronisé en inter-compagnies"})
     */
    private $icpfl;

    /**
     * @var string
     *
     * @ORM\Column(name="TRANSICOD", type="string", length=20, nullable=false, options={"fixed"=true,"comment"="Code transitaire"})
     */
    private $transicod;

    /**
     * @var string
     *
     * @ORM\Column(name="TVABLCD3", type="string", length=3, nullable=false, options={"fixed"=true,"comment"="Tva condition livraison code incoterm 3"})
     */
    private $tvablcd3;

    /**
     * @var string
     *
     * @ORM\Column(name="LIEUINCT", type="string", length=40, nullable=false, options={"fixed"=true,"comment"="Lieu Incoterm"})
     */
    private $lieuinct;

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
     * @ORM\Column(name="SITECOD", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Code de site"})
     */
    private $sitecod;

    /**
     * @var string
     *
     * @ORM\Column(name="CEATRAITEFL", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Pièce traitée pour CEA"})
     */
    private $ceatraitefl;

    /**
     * @var string
     *
     * @ORM\Column(name="BEXNO", type="decimal", precision=10, scale=0, nullable=false)
     */
    private $bexno;

    /**
     * @var string
     *
     * @ORM\Column(name="BLQFL", type="decimal", precision=1, scale=0, nullable=false)
     */
    private $blqfl;

    /**
     * @var string
     *
     * @ORM\Column(name="CONFIRMATIONFL", type="decimal", precision=1, scale=0, nullable=false)
     */
    private $confirmationfl;

    /**
     * @var string
     *
     * @ORM\Column(name="TAXCPLFFL", type="decimal", precision=1, scale=0, nullable=false)
     */
    private $taxcplffl;

    /**
     * @var string
     *
     * @ORM\Column(name="TAXSFVFL", type="decimal", precision=1, scale=0, nullable=false)
     */
    private $taxsfvfl;

    /**
     * @var string
     *
     * @ORM\Column(name="TVAAUTOLIQFL", type="decimal", precision=1, scale=0, nullable=false)
     */
    private $tvaautoliqfl;

    /**
     * @var string
     *
     * @ORM\Column(name="UNLOGCOD", type="decimal", precision=1, scale=0, nullable=false)
     */
    private $unlogcod;

    /**
     * @var string
     *
     * @ORM\Column(name="UNLOGTOT", type="decimal", precision=12, scale=3, nullable=false)
     */
    private $unlogtot;

    /**
     * @var string
     *
     * @ORM\Column(name="UNTYP", type="string", length=8, nullable=false, options={"fixed"=true})
     */
    private $untyp;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="VALFINDT", type="date", nullable=true)
     */
    private $valfindt;

    /**
     * @var string
     *
     * @ORM\Column(name="VERSIONDEVISNO", type="decimal", precision=2, scale=0, nullable=false)
     */
    private $versiondevisno;

    /**
     * @var string
     *
     * @ORM\Column(name="VERSIONDEVISORIPINO", type="decimal", precision=10, scale=0, nullable=false)
     */
    private $versiondevisoripino;

    /**
     * @var string
     *
     * @ORM\Column(name="VERSIONDEVISORIPREFPINO", type="string", length=10, nullable=false, options={"fixed"=true})
     */
    private $versiondevisoriprefpino;

    /**
     * @var int
     *
     * @ORM\Column(name="ENT_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $entId;

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

    public function getTicod(): ?string
    {
        return $this->ticod;
    }

    public function getPicod(): ?string
    {
        return $this->picod;
    }

    public function getTiers(): ?string
    {
        return $this->tiers;
    }

    public function getPrefpino(): ?string
    {
        return $this->prefpino;
    }

    public function getPino(): ?string
    {
        return $this->pino;
    }

    public function getPidt(): ?\DateTimeInterface
    {
        return $this->pidt;
    }

    public function getEtb(): ?string
    {
        return $this->etb;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getDev(): ?string
    {
        return $this->dev;
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

    public function getRibcod(): ?string
    {
        return $this->ribcod;
    }

    public function getMarche(): ?string
    {
        return $this->marche;
    }

    public function getProjet(): ?string
    {
        return $this->projet;
    }

    public function getDepo(): ?string
    {
        return $this->depo;
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

    public function getBlmod(): ?string
    {
        return $this->blmod;
    }

    public function getRegl(): ?string
    {
        return $this->regl;
    }

    public function getTour(): ?string
    {
        return $this->tour;
    }

    public function getPiref(): ?string
    {
        return $this->piref;
    }

    public function getPinotiers(): ?string
    {
        return $this->pinotiers;
    }

    public function getTierspayer(): ?string
    {
        return $this->tierspayer;
    }

    public function getTiersgrp(): ?string
    {
        return $this->tiersgrp;
    }

    public function getTiersrlv(): ?string
    {
        return $this->tiersrlv;
    }

    public function getBapsalcod(): ?string
    {
        return $this->bapsalcod;
    }

    public function getSalcod(): ?string
    {
        return $this->salcod;
    }

    public function getPrefrlvno(): ?string
    {
        return $this->prefrlvno;
    }

    public function getRlvno(): ?string
    {
        return $this->rlvno;
    }

    public function getRlvdt(): ?\DateTimeInterface
    {
        return $this->rlvdt;
    }

    public function getDeldemdt(): ?\DateTimeInterface
    {
        return $this->deldemdt;
    }

    public function getDelaccdt(): ?\DateTimeInterface
    {
        return $this->delaccdt;
    }

    public function getDelrepdt(): ?\DateTimeInterface
    {
        return $this->delrepdt;
    }

    public function getEchdt(): ?\DateTimeInterface
    {
        return $this->echdt;
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

    public function getTacod(): ?string
    {
        return $this->tacod;
    }

    public function getRemcod(): ?string
    {
        return $this->remcod;
    }

    public function getCofam(): ?string
    {
        return $this->cofam;
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

    public function getEtano(): ?string
    {
        return $this->etano;
    }

    public function getTxtedcodd(): ?string
    {
        return $this->txtedcodd;
    }

    public function getTxtedcodf(): ?string
    {
        return $this->txtedcodf;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function getPrefblasno(): ?string
    {
        return $this->prefblasno;
    }

    public function getBlasno(): ?string
    {
        return $this->blasno;
    }

    public function getBlasdepo(): ?string
    {
        return $this->blasdepo;
    }

    public function getTpft(): ?string
    {
        return $this->tpft;
    }

    public function getAvenant(): ?string
    {
        return $this->avenant;
    }

    public function getCesintcod(): ?string
    {
        return $this->cesintcod;
    }

    public function getPromotacod(): ?string
    {
        return $this->promotacod;
    }

    public function getPromoremcod(): ?string
    {
        return $this->promoremcod;
    }

    public function getPrefcdnopere(): ?string
    {
        return $this->prefcdnopere;
    }

    public function getCdnopere(): ?string
    {
        return $this->cdnopere;
    }

    public function getTpvbl(): ?string
    {
        return $this->tpvbl;
    }

    public function getDeeeinccod(): ?string
    {
        return $this->deeeinccod;
    }

    public function getConfigurateurformulaire(): ?string
    {
        return $this->configurateurformulaire;
    }

    public function getConfigurateurmonostatus(): ?string
    {
        return $this->configurateurmonostatus;
    }

    public function getConfigurateurmultistatus(): ?string
    {
        return $this->configurateurmultistatus;
    }

    public function getConfigurateurpino(): ?string
    {
        return $this->configurateurpino;
    }

    public function getConfigurateurstatus(): ?string
    {
        return $this->configurateurstatus;
    }

    public function getPrefpina(): ?string
    {
        return $this->prefpina;
    }

    public function getPina(): ?string
    {
        return $this->pina;
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

    public function getTxtcodd(): ?string
    {
        return $this->txtcodd;
    }

    public function getTxtcodf(): ?string
    {
        return $this->txtcodf;
    }

    public function getTxtnoted(): ?string
    {
        return $this->txtnoted;
    }

    public function getTxtnotef(): ?string
    {
        return $this->txtnotef;
    }

    public function getOrigine(): ?string
    {
        return $this->origine;
    }

    public function getHtmt(): ?string
    {
        return $this->htmt;
    }

    public function getTtcmt(): ?string
    {
        return $this->ttcmt;
    }

    public function getHtpdtmt(): ?string
    {
        return $this->htpdtmt;
    }

    public function getEscp(): ?string
    {
        return $this->escp;
    }

    public function getAcmt(): ?string
    {
        return $this->acmt;
    }

    public function getSoacmt(): ?string
    {
        return $this->soacmt;
    }

    public function getRemmt(): ?string
    {
        return $this->remmt;
    }

    public function getRem1(): ?string
    {
        return $this->rem1;
    }

    public function getRemtyp1(): ?string
    {
        return $this->remtyp1;
    }

    public function getFouhtmt(): ?string
    {
        return $this->fouhtmt;
    }

    public function getFouescmt(): ?string
    {
        return $this->fouescmt;
    }

    public function getFoutvamt(): ?string
    {
        return $this->foutvamt;
    }

    public function getDevp(): ?string
    {
        return $this->devp;
    }

    public function getPiedno0001(): ?string
    {
        return $this->piedno0001;
    }

    public function getPiedno0002(): ?string
    {
        return $this->piedno0002;
    }

    public function getPiedno0003(): ?string
    {
        return $this->piedno0003;
    }

    public function getPiedmt0001(): ?string
    {
        return $this->piedmt0001;
    }

    public function getPiedmt0002(): ?string
    {
        return $this->piedmt0002;
    }

    public function getPiedmt0003(): ?string
    {
        return $this->piedmt0003;
    }

    public function getNbex(): ?string
    {
        return $this->nbex;
    }

    public function getPirelcod(): ?string
    {
        return $this->pirelcod;
    }

    public function getRelcod(): ?string
    {
        return $this->relcod;
    }

    public function getEditcod(): ?string
    {
        return $this->editcod;
    }

    public function getTrcod(): ?string
    {
        return $this->trcod;
    }

    public function getBoredicod(): ?string
    {
        return $this->boredicod;
    }

    public function getAscod(): ?string
    {
        return $this->ascod;
    }

    public function getEchvcod(): ?string
    {
        return $this->echvcod;
    }

    public function getEncasscod(): ?string
    {
        return $this->encasscod;
    }

    public function getAdrtyp0001(): ?string
    {
        return $this->adrtyp0001;
    }

    public function getAdrtyp0002(): ?string
    {
        return $this->adrtyp0002;
    }

    public function getAdrtyp0003(): ?string
    {
        return $this->adrtyp0003;
    }

    public function getAdrtyp0004(): ?string
    {
        return $this->adrtyp0004;
    }

    public function getAdrtyp0005(): ?string
    {
        return $this->adrtyp0005;
    }

    public function getPriocod(): ?string
    {
        return $this->priocod;
    }

    public function getHtcod(): ?string
    {
        return $this->htcod;
    }

    public function getStres(): ?string
    {
        return $this->stres;
    }

    public function getFamod(): ?string
    {
        return $this->famod;
    }

    public function getPeriod(): ?string
    {
        return $this->period;
    }

    public function getPorcod(): ?string
    {
        return $this->porcod;
    }

    public function getPoicod(): ?string
    {
        return $this->poicod;
    }

    public function getVolcod(): ?string
    {
        return $this->volcod;
    }

    public function getPorfrfl(): ?string
    {
        return $this->porfrfl;
    }

    public function getPoitot(): ?string
    {
        return $this->poitot;
    }

    public function getVoltot(): ?string
    {
        return $this->voltot;
    }

    public function getColinb(): ?string
    {
        return $this->colinb;
    }

    public function getRefnb(): ?string
    {
        return $this->refnb;
    }

    public function getTourrg(): ?string
    {
        return $this->tourrg;
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

    public function getPortheomt(): ?string
    {
        return $this->portheomt;
    }

    public function getRempietot(): ?string
    {
        return $this->rempietot;
    }

    public function getTransjrnb(): ?string
    {
        return $this->transjrnb;
    }

    public function getOfascod(): ?string
    {
        return $this->ofascod;
    }

    public function getFinal(): ?string
    {
        return $this->final;
    }

    public function getQuacod(): ?string
    {
        return $this->quacod;
    }

    public function getCejoint(): ?string
    {
        return $this->cejoint;
    }

    public function getJoint(): ?string
    {
        return $this->joint;
    }

    public function getDeeemt(): ?string
    {
        return $this->deeemt;
    }

    public function getFoudeeemt(): ?string
    {
        return $this->foudeeemt;
    }

    public function getPrgcdeflg(): ?string
    {
        return $this->prgcdeflg;
    }

    public function getBqcpce(): ?string
    {
        return $this->bqcpce;
    }

    public function getPoincod(): ?string
    {
        return $this->poincod;
    }

    public function getPointot(): ?string
    {
        return $this->pointot;
    }

    public function getPrioreg(): ?string
    {
        return $this->prioreg;
    }

    public function getTvatie(): ?string
    {
        return $this->tvatie;
    }

    public function getStlgtgamcod(): ?string
    {
        return $this->stlgtgamcod;
    }

    public function getDtflg(): ?string
    {
        return $this->dtflg;
    }

    public function getSynchrofl(): ?string
    {
        return $this->synchrofl;
    }

    public function getIcpfl(): ?string
    {
        return $this->icpfl;
    }

    public function getTransicod(): ?string
    {
        return $this->transicod;
    }

    public function getTvablcd3(): ?string
    {
        return $this->tvablcd3;
    }

    public function getLieuinct(): ?string
    {
        return $this->lieuinct;
    }

    public function getPorfrval(): ?string
    {
        return $this->porfrval;
    }

    public function getPorfrcod(): ?string
    {
        return $this->porfrcod;
    }

    public function getSitecod(): ?string
    {
        return $this->sitecod;
    }

    public function getCeatraitefl(): ?string
    {
        return $this->ceatraitefl;
    }

    public function getBexno(): ?string
    {
        return $this->bexno;
    }

    public function getBlqfl(): ?string
    {
        return $this->blqfl;
    }

    public function getConfirmationfl(): ?string
    {
        return $this->confirmationfl;
    }

    public function getTaxcplffl(): ?string
    {
        return $this->taxcplffl;
    }

    public function getTaxsfvfl(): ?string
    {
        return $this->taxsfvfl;
    }

    public function getTvaautoliqfl(): ?string
    {
        return $this->tvaautoliqfl;
    }

    public function getUnlogcod(): ?string
    {
        return $this->unlogcod;
    }

    public function getUnlogtot(): ?string
    {
        return $this->unlogtot;
    }

    public function getUntyp(): ?string
    {
        return $this->untyp;
    }

    public function getValfindt(): ?\DateTimeInterface
    {
        return $this->valfindt;
    }

    public function getVersiondevisno(): ?string
    {
        return $this->versiondevisno;
    }

    public function getVersiondevisoripino(): ?string
    {
        return $this->versiondevisoripino;
    }

    public function getVersiondevisoriprefpino(): ?string
    {
        return $this->versiondevisoriprefpino;
    }

    public function getEntId(): ?int
    {
        return $this->entId;
    }


}
