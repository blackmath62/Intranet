<?php

namespace App\Controller;

use DateTime;
use Symfony\Component\Mime\Email;
use App\Entity\Main\ControlesAnomalies;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Main\ControlesAnomaliesRepository;
use App\Repository\Divalto\ControleComptabiliteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ControleAnomaliesController extends AbstractController
{
    private $mailer;
    private $anomalies;
    private $compta;

    public function __construct(MailerInterface $mailer, ControlesAnomaliesRepository $anomalies,ControleComptabiliteRepository $compta)
    {
        $this->mailer = $mailer;
        $this->anomalies = $anomalies;
        $this->compta = $compta;
        //parent::__construct();
    }
    
    /**
     * @Route("/controle/anomalies", name="app_controle_anomalies")
     */
    public function ControlRegimeFournisseur(): Response
    {
        
        $regimeTiers = $this->compta->getSendMailErreurRegimeFournisseur();
        $dateDuJour = new DateTime();
        //dd($regimeTiers);
        for ($ligRegimeTiers=0; $ligRegimeTiers <count($regimeTiers) ; $ligRegimeTiers++) { 
            $id = $regimeTiers[$ligRegimeTiers]['identification'];
            $ano = $this->anomalies->findOneBy(['idAnomalie' => $id]);
            
            // si elle n'existe pas, on la créér
            if ( empty($ano)) {
                
                // créer une nouvelle anomalie
                $createAnomalie = new ControlesAnomalies();

                $createAnomalie->setIdAnomalie($id)
                               ->setCreatedAt(new \DateTime())
                               ->setModifiedAt(new \DateTime())
                               ->setType('RegimeFournisseur');
                $em = $this->getDoctrine()->getManager();
                $em->persist($createAnomalie);
                $em->flush();

                // envoyer un mail
                $html = $this->renderView('mails/sendMailAnomalieRegimeFournisseur.html.twig', ['anomalie' => $regimeTiers[$ligRegimeTiers]]);
                $email = (new Email())
                ->from('intranet@groupe-axis.fr')
                ->to($regimeTiers[$ligRegimeTiers]['EMAIL'])
                ->cc('jpochet@lhermitte.fr')
                ->subject("Probléme Régime TVA sur l'entête d'une piéce que vous avez saisie")
                ->html($html);
                $this->mailer->send($email);

                // si elle existe on envoit un mail et on mets à jours la date
            }elseif(!is_null($ano)){
                // mettre la date de modification à jour si ça fait plus de 0 jours que le mail à été envoyé
                
                $dateModif = $ano->getModifiedAt();
                $datediff = $dateModif->diff($dateDuJour)->format("%a");
                if ($datediff > 0) {
                    // envoyer un mail
                    $html = $this->renderView('mails/sendMailAnomalieRegimeFournisseur.html.twig', ['anomalie' => $regimeTiers[$ligRegimeTiers]]);
                    $email = (new Email())
                    ->from('intranet@groupe-axis.fr')
                    ->to($regimeTiers[$ligRegimeTiers]['EMAIL'])
                    ->cc('jpochet@lhermitte.fr')
                    ->subject("Probléme Régime TVA sur l'entête d'une piéce que vous avez saisie")
                    ->html($html);
                    $this->mailer->send($email);

                    $ano->setModifiedAt(new \DateTime());
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($ano);
                    $em->flush();
                }               
            }
            
        }
        // suppression des anomalies trop vieilles 
        $controleAno = $this->anomalies->findAll();
        foreach ($controleAno as $key ) {
            
            $dateModif = $key->getModifiedAt();
            $datediff = $dateModif->diff($dateDuJour)->format("%a");
            // si l'écart de date entre date début et modif est supérieur à 15 jours on supprime
            if ($datediff > 2) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($key);
                $em->flush(); 
            }
        }
        
        return $this->render('controle_anomalies/index.html.twig', [
            'controller_name' => 'ControleAnomaliesController',
        ]);
    }
}
