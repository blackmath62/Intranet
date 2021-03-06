<?php

namespace App\Entity\Divalto;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\SartRepository;

/**
 * Sart
 * @ORM\Entity
 * @ORM\Table(name="SART", indexes={@ORM\Index(name="INDEX_H", columns={"DOS", "REF", "SREF1", "SREF2", "SART_ID"}), @ORM\Index(name="INDEX_P", columns={"DOS", "EAN", "SART_ID"})})
 */
class Sart
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
     * @ORM\Column(name="EAN", type="string", length=13, nullable=false, options={"fixed"=true,"comment"="Code EAN"})
     */
    private $ean;

    /**
     * @var string
     *
     * @ORM\Column(name="NOMCOECOD", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="% sur CR matières"})
     */
    private $nomcoecod;

    /**
     * @var string
     *
     * @ORM\Column(name="NOMCSTCOD", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Constante à ajouter au CR matière"})
     */
    private $nomcstcod;

    /**
     * @var string
     *
     * @ORM\Column(name="RSCECOECOD", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="% sur CR ressource"})
     */
    private $rscecoecod;

    /**
     * @var string
     *
     * @ORM\Column(name="RSCECSTCOD", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Constante à ajouter au CR ressource"})
     */
    private $rscecstcod;

    /**
     * @var string
     *
     * @ORM\Column(name="POSTCOECOD", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="% sur CR poste de charge"})
     */
    private $postcoecod;

    /**
     * @var string
     *
     * @ORM\Column(name="POSTCSTCOD", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Constante à ajouter CR poste"})
     */
    private $postcstcod;

    /**
     * @var string
     *
     * @ORM\Column(name="STRTCOECOD", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="% sur CR sous-traitance"})
     */
    private $strtcoecod;

    /**
     * @var string
     *
     * @ORM\Column(name="STRTCSTCOD", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Constante à ajouter au CR sous-traitance"})
     */
    private $strtcstcod;

    /**
     * @var string
     *
     * @ORM\Column(name="OUTICOECOD", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="% sur CR outillage"})
     */
    private $outicoecod;

    /**
     * @var string
     *
     * @ORM\Column(name="OUTICSTCOD", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Constante à ajouter au  CR outillage"})
     */
    private $outicstcod;

    /**
     * @var string
     *
     * @ORM\Column(name="COECOD", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Code coefficient"})
     */
    private $coecod;

    /**
     * @var string
     *
     * @ORM\Column(name="PRODIND", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Indice production"})
     */
    private $prodind;

    /**
     * @var string
     *
     * @ORM\Column(name="CBNGESCOD", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Code règle de gestion du CBN"})
     */
    private $cbngescod;

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
     * @ORM\Column(name="CR", type="decimal", precision=17, scale=6, nullable=false, options={"comment"="Coût de revient"})
     */
    private $cr;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="CORDT", type="date", nullable=true, options={"comment"="Date de mise à jour du dernier coût de revient"})
     */
    private $cordt;

    /**
     * @var string
     *
     * @ORM\Column(name="CMP", type="decimal", precision=17, scale=6, nullable=false, options={"comment"="Coût moyen pondéré"})
     */
    private $cmp;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="CMPDT", type="date", nullable=true, options={"comment"="Date de mise à jour du CMP"})
     */
    private $cmpdt;

    /**
     * @var string
     *
     * @ORM\Column(name="CRSTD", type="decimal", precision=17, scale=6, nullable=false, options={"comment"="Coût de revient standard"})
     */
    private $crstd;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="CRSTDDT", type="date", nullable=true, options={"comment"="Date de calcul cr standard"})
     */
    private $crstddt;

    /**
     * @var string
     *
     * @ORM\Column(name="PA", type="decimal", precision=13, scale=4, nullable=false, options={"comment"="Prix d achat"})
     */
    private $pa;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="PADT", type="date", nullable=true, options={"comment"="Date de mise à jour du dernier PA"})
     */
    private $padt;

    /**
     * @var string
     *
     * @ORM\Column(name="CRNOM", type="decimal", precision=17, scale=6, nullable=false, options={"comment"="Coût standard nomenclature"})
     */
    private $crnom;

    /**
     * @var string
     *
     * @ORM\Column(name="CRRSCE", type="decimal", precision=17, scale=6, nullable=false, options={"comment"="Coût standard ressource"})
     */
    private $crrsce;

    /**
     * @var string
     *
     * @ORM\Column(name="CRPOSTE", type="decimal", precision=17, scale=6, nullable=false, options={"comment"="Coût standard poste"})
     */
    private $crposte;

    /**
     * @var string
     *
     * @ORM\Column(name="CRSTRT", type="decimal", precision=17, scale=6, nullable=false, options={"comment"="Coût standard sous-traitance"})
     */
    private $crstrt;

    /**
     * @var string
     *
     * @ORM\Column(name="CROUTIL", type="decimal", precision=17, scale=6, nullable=false, options={"comment"="Coût standard outillage"})
     */
    private $croutil;

    /**
     * @var string
     *
     * @ORM\Column(name="FABQTE", type="decimal", precision=12, scale=3, nullable=false, options={"comment"="Quantité optimale de fabrication"})
     */
    private $fabqte;

    /**
     * @var string
     *
     * @ORM\Column(name="POIB", type="decimal", precision=8, scale=3, nullable=false, options={"comment"="Poids brut"})
     */
    private $poib;

    /**
     * @var string
     *
     * @ORM\Column(name="POIN", type="decimal", precision=8, scale=3, nullable=false, options={"comment"="Masse nette"})
     */
    private $poin;

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
     * @ORM\Column(name="CRH", type="decimal", precision=7, scale=2, nullable=false, options={"comment"="Coût de revient horaire"})
     */
    private $crh;

    /**
     * @var string
     *
     * @ORM\Column(name="DELSECUJR", type="decimal", precision=3, scale=0, nullable=false, options={"comment"="Délai de sécurité jours"})
     */
    private $delsecujr;

    /**
     * @var string
     *
     * @ORM\Column(name="PRODQTEARR", type="decimal", precision=8, scale=3, nullable=false, options={"comment"="Arrondi des quantités à produire"})
     */
    private $prodqtearr;

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
     * @ORM\Column(name="SURF", type="decimal", precision=8, scale=3, nullable=false, options={"comment"="Surface"})
     */
    private $surf;

    /**
     * @var string
     *
     * @ORM\Column(name="LOTQTE", type="decimal", precision=12, scale=3, nullable=false, options={"comment"="Quantité de lot de fabrication"})
     */
    private $lotqte;

    /**
     * @var string
     *
     * @ORM\Column(name="LOTCOD", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Méthode de découpage en lot de fabrication"})
     */
    private $lotcod;

    /**
     * @var string
     *
     * @ORM\Column(name="WMQTEIMP", type="decimal", precision=9, scale=0, nullable=false, options={"comment"="Quantité seuil méthode de réservation en volume important"})
     */
    private $wmqteimp;

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
     * @var string
     *
     * @ORM\Column(name="DTIND", type="string", length=4, nullable=false, options={"fixed"=true,"comment"="Indice gamme devis technique"})
     */
    private $dtind;

    /**
     * @var string
     *
     * @ORM\Column(name="COUTVARCOD", type="string", length=8, nullable=false, options={"fixed"=true,"comment"="Code variante de coût"})
     */
    private $coutvarcod;

    /**
     * @var string
     *
     * @ORM\Column(name="CRSTDMN", type="decimal", precision=17, scale=6, nullable=false, options={"comment"="Coût de revient standard multi-niveau"})
     */
    private $crstdmn;

    /**
     * @var string
     *
     * @ORM\Column(name="CRNOMMN", type="decimal", precision=17, scale=6, nullable=false, options={"comment"="Coût standard nomenclature multi-niveau"})
     */
    private $crnommn;

    /**
     * @var string
     *
     * @ORM\Column(name="CRRSCEMN", type="decimal", precision=17, scale=6, nullable=false, options={"comment"="Coût standard ressource multi-niveau"})
     */
    private $crrscemn;

    /**
     * @var string
     *
     * @ORM\Column(name="CRPOSTEMN", type="decimal", precision=17, scale=6, nullable=false, options={"comment"="Coût standard poste multi-niveau"})
     */
    private $crpostemn;

    /**
     * @var string
     *
     * @ORM\Column(name="CRSTRTMN", type="decimal", precision=17, scale=6, nullable=false, options={"comment"="Coût standard sous-traitance multi-niveau"})
     */
    private $crstrtmn;

    /**
     * @var string
     *
     * @ORM\Column(name="CROUTILMN", type="decimal", precision=17, scale=6, nullable=false, options={"comment"="Coût standard outillage multi-niveau"})
     */
    private $croutilmn;

    /**
     * @var string
     *
     * @ORM\Column(name="ICPFL", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="Element synchronisé en inter-compagnies"})
     */
    private $icpfl;

    /**
     * @var string
     *
     * @ORM\Column(name="PRIXMOY", type="decimal", precision=13, scale=4, nullable=false, options={"comment"="Prix moyen"})
     */
    private $prixmoy;

    /**
     * @var string
     *
     * @ORM\Column(name="EANAUTOFL", type="decimal", precision=1, scale=0, nullable=false, options={"comment"="EAN généré automatiquement"})
     */
    private $eanautofl;

    /**
     * @var int
     *
     * @ORM\Column(name="SART_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $sartId;

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

    public function getSref1(): ?string
    {
        return $this->sref1;
    }

    public function getSref2(): ?string
    {
        return $this->sref2;
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

    public function getEan(): ?string
    {
        return $this->ean;
    }

    public function getNomcoecod(): ?string
    {
        return $this->nomcoecod;
    }

    public function getNomcstcod(): ?string
    {
        return $this->nomcstcod;
    }

    public function getRscecoecod(): ?string
    {
        return $this->rscecoecod;
    }

    public function getRscecstcod(): ?string
    {
        return $this->rscecstcod;
    }

    public function getPostcoecod(): ?string
    {
        return $this->postcoecod;
    }

    public function getPostcstcod(): ?string
    {
        return $this->postcstcod;
    }

    public function getStrtcoecod(): ?string
    {
        return $this->strtcoecod;
    }

    public function getStrtcstcod(): ?string
    {
        return $this->strtcstcod;
    }

    public function getOuticoecod(): ?string
    {
        return $this->outicoecod;
    }

    public function getOuticstcod(): ?string
    {
        return $this->outicstcod;
    }

    public function getCoecod(): ?string
    {
        return $this->coecod;
    }

    public function getProdind(): ?string
    {
        return $this->prodind;
    }

    public function getCbngescod(): ?string
    {
        return $this->cbngescod;
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

    public function getCr(): ?string
    {
        return $this->cr;
    }

    public function getCordt(): ?\DateTimeInterface
    {
        return $this->cordt;
    }

    public function getCmp(): ?string
    {
        return $this->cmp;
    }

    public function getCmpdt(): ?\DateTimeInterface
    {
        return $this->cmpdt;
    }

    public function getCrstd(): ?string
    {
        return $this->crstd;
    }

    public function getCrstddt(): ?\DateTimeInterface
    {
        return $this->crstddt;
    }

    public function getPa(): ?string
    {
        return $this->pa;
    }

    public function getPadt(): ?\DateTimeInterface
    {
        return $this->padt;
    }

    public function getCrnom(): ?string
    {
        return $this->crnom;
    }

    public function getCrrsce(): ?string
    {
        return $this->crrsce;
    }

    public function getCrposte(): ?string
    {
        return $this->crposte;
    }

    public function getCrstrt(): ?string
    {
        return $this->crstrt;
    }

    public function getCroutil(): ?string
    {
        return $this->croutil;
    }

    public function getFabqte(): ?string
    {
        return $this->fabqte;
    }

    public function getPoib(): ?string
    {
        return $this->poib;
    }

    public function getPoin(): ?string
    {
        return $this->poin;
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

    public function getQtemin(): ?string
    {
        return $this->qtemin;
    }

    public function getQtepar(): ?string
    {
        return $this->qtepar;
    }

    public function getCrh(): ?string
    {
        return $this->crh;
    }

    public function getDelsecujr(): ?string
    {
        return $this->delsecujr;
    }

    public function getProdqtearr(): ?string
    {
        return $this->prodqtearr;
    }

    public function getCejoint(): ?string
    {
        return $this->cejoint;
    }

    public function getJoint(): ?string
    {
        return $this->joint;
    }

    public function getSurf(): ?string
    {
        return $this->surf;
    }

    public function getLotqte(): ?string
    {
        return $this->lotqte;
    }

    public function getLotcod(): ?string
    {
        return $this->lotcod;
    }

    public function getWmqteimp(): ?string
    {
        return $this->wmqteimp;
    }

    public function getTiers(): ?string
    {
        return $this->tiers;
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

    public function getCoutvarcod(): ?string
    {
        return $this->coutvarcod;
    }

    public function getCrstdmn(): ?string
    {
        return $this->crstdmn;
    }

    public function getCrnommn(): ?string
    {
        return $this->crnommn;
    }

    public function getCrrscemn(): ?string
    {
        return $this->crrscemn;
    }

    public function getCrpostemn(): ?string
    {
        return $this->crpostemn;
    }

    public function getCrstrtmn(): ?string
    {
        return $this->crstrtmn;
    }

    public function getCroutilmn(): ?string
    {
        return $this->croutilmn;
    }

    public function getIcpfl(): ?string
    {
        return $this->icpfl;
    }

    public function getPrixmoy(): ?string
    {
        return $this->prixmoy;
    }

    public function getEanautofl(): ?string
    {
        return $this->eanautofl;
    }

    public function getSartId(): ?int
    {
        return $this->sartId;
    }


}
