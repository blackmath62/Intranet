<?php

namespace App\Controller;

use DateTime;
use Exception;
use App\Form\AddEmailType;
use App\Form\YearMonthType;
use App\Entity\Main\MailList;
use App\Controller\AdminEmailController;
use App\Repository\Main\MailListRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Divalto\ComptaAnalytiqueRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_COMPTA")
*/

class ComptaAnalytiqueController extends AbstractController
{

    private $mailer;
    private $repoMail;
    private $mailEnvoi;
    private $mailTreatement;
    private $adminEmailController;

    public function __construct(AdminEmailController $adminEmailController,MailerInterface $mailer, MailListRepository $repoMail)
    {
        $this->mailer = $mailer;
        $this->repoMail =$repoMail;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi()['email'];
        $this->mailTreatement = $this->repoMail->getEmailTreatement()['email'];
        $this->adminEmailController = $adminEmailController;

        //parent::__construct();
    }


    /**
     * @Route("compta/compta_analytique", name="app_compta_analytique")
     */
    public function index(Request $request, ComptaAnalytiqueRepository $repo): Response
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        $achat = [];
        $ventes = [];
        $estimation = 0;
        $estimationTotal = 0;
        $form = $this->createForm(YearMonthType::class);
        $form->handleRequest($request);
        
                    if($form->isSubmitted() && $form->isValid()){
                        $annee = $form->getData()['year'];
                        $mois = $form->getData()['month'];
                        $regime = "";
                        $exportVentes = $repo->getRapportClient($annee, $mois);
                        // exportation des ventes
                        for ($lig=0; $lig <count($exportVentes) ; $lig++) { 
                            $port = 0;
                            
                            $ventes[$lig]['Facture'] = $exportVentes[$lig]['Facture'];
                            $ventes[$lig]['Ref'] = $exportVentes[$lig]['Ref'];
                            $ventes[$lig]['Sref1'] = $exportVentes[$lig]['Sref1'];
                            $ventes[$lig]['Sref2'] = $exportVentes[$lig]['Sref2'];
                            $ventes[$lig]['Designation'] = $exportVentes[$lig]['Designation'];
                            $ventes[$lig]['Uv'] = $exportVentes[$lig]['Uv'];
                            $ventes[$lig]['Op'] = $exportVentes[$lig]['Op'];
                            $ventes[$lig]['Article'] = $exportVentes[$lig]['Article'];
                            $ventes[$lig]['Client'] = $exportVentes[$lig]['Client'];
                            $ventes[$lig]['QteSign'] = $exportVentes[$lig]['qteVtl'];
                            $ventes[$lig]['CoutRevient'] = $exportVentes[$lig]['CoutRevient'];
                            $ventes[$lig]['CoutMoyenPondere'] = $exportVentes[$lig]['CoutMoyenPondere'];
                            // rapprocher les achats
                            $achat = $repo->getRapportFournisseurAvecSref(
                                        $exportVentes[$lig]['VentAss'], 
                                        $exportVentes[$lig]['Ref'], 
                                        $exportVentes[$lig]['Sref1'], 
                                        $exportVentes[$lig]['Sref2']);
                            $pa = 0;
                            if ($achat)
                                {$pa = $achat['pa'];}
                            $ventes[$lig]['Cma'] = $pa;
                            $crt = 0;
                            $cmpt = 0;
                            $cmat = 0;
                            if ($exportVentes[$lig]['CoutRevient'] <> 0 && $exportVentes[$lig]['qteVtl'] <> 0)
                                { $crt  = $exportVentes[$lig]['qteVtl'] * $exportVentes[$lig]['CoutRevient']; }
                            $ventes[$lig]['TotalCoutRevient'] = $crt;
                            if ($exportVentes[$lig]['CoutMoyenPondere'] <> 0 && $exportVentes[$lig]['qteVtl'] <> 0)
                                { $cmpt  = $exportVentes[$lig]['qteVtl'] * $exportVentes[$lig]['CoutMoyenPondere']; }
                            $ventes[$lig]['TotalCoutMoyenPondere'] = $cmpt;
                            if ($pa <> 0 && $exportVentes[$lig]['qteVtl'] <> 0)
                                { $cmat  = $exportVentes[$lig]['qteVtl'] * $pa; }
                            $ventes[$lig]['TotalCoutCma'] = $cmat;
                            if ($achat['regimePiece']) {
                                $regime = $achat['regimePiece'];
                            }else {
                                $regime = $exportVentes[$lig]['regimeFou'];
                            }
                            if ($regime == 0) {
                                $compteAchat = $exportVentes[$lig]['CompteAchat'];
                            }elseif ($regime == 1) {
                                $compteAchat = $exportVentes[$lig]['CompteAchat'] + 10000;
                            }elseif ($regime == 2) {
                                $compteAchat = $exportVentes[$lig]['CompteAchat'] + 20000;
                            }
                            $ventes[$lig]['CompteAchat'] = $compteAchat;
                            $ventes[$lig]['estimation'] = '';
                            $ventes[$lig]['estimationTotal'] = '';
                            // TODO REVOIR CETTE PARTIE QUI NE FONCTIONNE PAS DU TOUT DEPUIS L'AJOUT DES ESTIMATIONS, J'AI DU PETER QUELQUE CHOSE
                            if ($achat['pinoFou']) {
                                // ramener la somme des montants du transport sur cette piéce
                                $port = $repo->getTransportFournisseur($achat['pinoFou']);
                                if ($port['montant'] > 0 && $port['montant'] <> 'null') {
                                    // ramener le détail de la piéce fournisseur
                                    $transport = $repo->getDetailPieceFournisseur($achat['pinoFou']);
                                    // La quantité pour les produits qui ne sont pas des articles de transport
                                    $estim = $repo->getQteHorsPortFournisseur($achat['pinoFou']);
                                    if ($estim['qte'] > 0 && $port['montant'] > 0) {
                                        try {
                                            $ventes[$lig]['estimation'] = ($port['montant'] / $estim['qte']);
                                        } catch (Exception $e) {
                                            echo 'Exception reçue : ',  $e->getMessage() . $port['montant'] . ' - ' . $estim['qte'], "\n";
                                        }
                                        if ($exportVentes[$lig]['qteVtl'] <> 0 ) {
                                            $ventes[$lig]['estimationTotal'] = $exportVentes[$lig]['qteVtl'] * ($port['montant']/$estim['qte']);
                                        }
                                    }
                                }else {
                                    $transport = 0;
                                }

                            }
                            if ($cmat) {
                                $ventes[$lig]['prixRetenu'] = $cmat;
                            }elseif ($cmpt) {
                                $ventes[$lig]['prixRetenu'] = $cmpt;
                            }elseif ($crt) {
                                $ventes[$lig]['prixRetenu'] = $crt;
                            }else {
                                $ventes[$lig]['prixRetenu'] = 0;
                            }
                            $ventes[$lig]['DetailFacture'] = [];
                            $ventes[$lig]['DetailFacture'] = $transport;
                            
                        }
                    }

                    // form pour gérer la liste des mails d'envois
                    unset($formMails);
                    $formMails = $this->createForm(AddEmailType::class);
                    $formMails->handleRequest($request);
                    if($formMails->isSubmitted() && $formMails->isValid()){
                        $find = $this->repoMail->findBy(['email' => $formMails->getData()['email'], 'page' => $tracking]);
                        if (empty($find) | is_null($find)) {
                            $mail = new MailList();
                            $mail->setCreatedAt(new DateTime())
                                ->setEmail($formMails->getData()['email'])
                                ->setPage($tracking);
                            $em = $this->getDoctrine()->getManager();
                            $em->persist($mail);
                            $em->flush();
                            $this->addFlash('message', 'le mail a été ajouté avec succés !');
                        }else {
                            $this->addFlash('danger', 'le mail est déjà inscrit pour cette page !');
                            return $this->redirectToRoute('app_compta_analytique');
                        }
                    }  
        return $this->render('compta_analytique/index.html.twig', [
            'ventes' => $ventes,
            'title' => 'Compta Analytique par mois',
            'monthYear' => $form->createView(),
            'formMails' => $formMails->createView(),
            'listeMails' => $this->repoMail->findBy(['page' => $tracking]),
            ]);
    }

    // créer une fonction avec l'export qui sera aussi utilisé pour la génération du fichier Excel

    // générer un fichier Excel qui sera envoyé par mail aux adresses renseignées

    // ajouter une section par total par compte
}
