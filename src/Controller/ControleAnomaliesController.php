<?php

namespace App\Controller;

use DateTime;
use Symfony\Component\Mime\Email;
use App\Entity\Main\ControlesAnomalies;
use App\Repository\Divalto\CliRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Main\ControlesAnomaliesRepository;
use App\Repository\Divalto\ControleComptabiliteRepository;
use App\Repository\Divalto\ControleArtStockMouvEfRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ControleAnomaliesController extends AbstractController
{
    private $mailer;
    private $anomalies;
    private $compta;
    private $articleSrefFermes;
    private $client;

    public function __construct(CliRepository $client, ControleArtStockMouvEfRepository $articleSrefFermes,MailerInterface $mailer, ControlesAnomaliesRepository $anomalies,ControleComptabiliteRepository $compta)
    {
        $this->mailer = $mailer;
        $this->anomalies = $anomalies;
        $this->compta = $compta;
        $this->articleSrefFermes = $articleSrefFermes;
        $this->client = $client;
        //parent::__construct();
    }
    /**
     * @Route("/controle/anomalies", name="app_controle_anomalies")
     */
    public function Show_Anomalies()
    {
        return $this->render('controle_anomalies/anomalies.html.twig',[
            'title' => 'Liste des anomalies',
            'anomalies' => $this->anomalies->findAll(),
        ]);
    }

    Public function Run_Cron(){
        $dateDuJour = new DateTime();
        $dateDuJour  = $dateDuJour->format('d-m-Y');

        if ($this->isWeekend($dateDuJour) == false) {
            $this->ControlRegimeClient();
            $this->ControlRegimeFournisseur();
            $this->ControlSaisieArtSrefFerme();
            $this->ControlDonneeClient();
            $this->run_auto_wash();
        }

        return $this->render('controle_anomalies/index.html.twig');
    }
    
    function isWeekend($date) {
        $weekDay = date('w', strtotime($date));
        return ($weekDay == 0 || $weekDay == 6);
    }
    
    public function ControlRegimeFournisseur()
    {
                
        $donnees = $this->compta->getSendMailErreurRegimeFournisseur();
        $libelle = 'RegimeFournisseur';
        $template = 'mails/sendMailAnomalieRegimeTiers.html.twig';
        $subject = 'Probléme Régime TVA sur l\'entête d\'une piéce que vous avez saisie';
        $this->Execute($donnees, $libelle, $template, $subject);

    }
    
    public function ControlRegimeClient()
    {
        
        $donnees = $this->compta->getSendMailErreurRegimeClient();
        $libelle = 'RegimeClient';
        $template = 'mails/sendMailAnomalieRegimeTiers.html.twig';
        $subject = 'Probléme Régime TVA sur l\'entête d\'une piéce que vous avez saisie';
        $this->Execute($donnees, $libelle, $template, $subject);
                
    }
    
    // Contrôle des saisies sur article ou Sref fermé
    public function ControlSaisieArtSrefFerme(){
        
        $dossier = 1;
        $donnees = $this->articleSrefFermes->getControleSaisieArticlesSrefFermes($dossier);
        $libelle = 'ArticleSrefFerme';
        $template = 'mails/sendMailSaisieArticlesSrefFermes.html.twig';
        $subject = 'Saisie sur un article ou une sous référence article fermé';
        $this->Execute($donnees, $libelle, $template, $subject);

    }

    // Contrôle des clients
    /**
     * @Route("/controle/anomalies", name="app_controle_anomalies")
     */
    public function ControlDonneeClient(){
        
        $donnees = $this->client->SurveillanceClientLhermitteReglStatVrpTransVisaTvaPay();
        $libelle = 'DonneeClient';
        $template = 'mails/sendMailAnomalieDonneesClients.html.twig';
        $subject = 'Erreur ou manquement sur une fiche client';
        $this->Execute($donnees, $libelle, $template, $subject);
        
    }

    public function Execute($donnees, $libelle, $template, $subject ){
        
        $donnees = $this->client->SurveillanceClientLhermitteReglStatVrpTransVisaTvaPay();
        $dateDuJour = new DateTime();
        //dd($donnees);
        for ($lig=0; $lig <count($donnees) ; $lig++) { 
            $id = $donnees[$lig]['Identification'];
            $ano = $this->anomalies->findOneBy(['idAnomalie' => $id, 'type' => $libelle]);
            
            // si elle n'existe pas, on la créér
            if ( empty($ano)) {
                
                // créer une nouvelle anomalie
                $createAnomalie = new ControlesAnomalies();

                $createAnomalie->setIdAnomalie($id)
                               ->setUpdatedAt($dateDuJour)
                               ->setCreatedAt($dateDuJour)
                               ->setModifiedAt($dateDuJour)
                               ->setType($libelle);
                $em = $this->getDoctrine()->getManager();
                $em->persist($createAnomalie);
                $em->flush();

                // envoyer un mail
                $html = $this->renderView($template, ['anomalie' => $donnees[$lig]]);
                $email = (new Email())
                ->from('intranet@groupe-axis.fr')
                ->to($donnees[$lig]['Email'])
                ->cc('jpochet@lhermitte.fr')
                ->subject($subject . '- id: ' . $donnees[$lig]['Identification'] . ' - Type: ' . $libelle)
                ->html($html);
                $this->mailer->send($email);

                // si elle existe on envoit un mail et on mets à jours la date
            }elseif(!is_null($ano)){
                // mettre la date de modification à jour si ça fait plus de 0 jours que le mail à été envoyé
                
                $dateModif = $ano->getModifiedAt();
                $datediff = $dateModif->diff($dateDuJour)->format("%a");
                if ($datediff > 0) {
                    // envoyer un mail
                    $html = $this->renderView($template, ['anomalie' => $donnees[$lig]]);
                    $email = (new Email())
                    ->from('intranet@groupe-axis.fr')
                    ->to($donnees[$lig]['Email'])
                    ->cc('jpochet@lhermitte.fr')
                    ->subject($subject . '- id: ' . $donnees[$lig]['Identification'] . ' - Type: ' . $libelle)
                    ->html($html);
                    $this->mailer->send($email);

                    $ano->setUpdatedAt($dateDuJour);
                    $ano->setModifiedAt($dateDuJour);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($ano);
                    $em->flush();
                }else{
                    $ano->setUpdatedAt($dateDuJour);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($ano);
                    $em->flush();
                }               
            }
            
        }
        //return $this->render('controle_anomalies/index.html.twig');
    }

    // suppression des anomalies trop vieilles 
    public function run_auto_wash(){
        $dateDuJour = new DateTime();
        $controleAno = $this->anomalies->findAll();
        foreach ($controleAno as $key ) {
            
            $dateModif = $key->getModifiedAt();
            $datediff = $dateModif->diff($dateDuJour)->format("%a");
            // si l'écart de date entre date début et modif est supérieur à 2 jours on supprime, c'est que le probléme est résolu
            if ($datediff > 1) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($key);
                $em->flush(); 
            }
        }
        
    }
}
