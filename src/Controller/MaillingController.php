<?php

namespace App\Controller;

use App\Form\MailingType;
use App\Repository\Divalto\FouRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class MaillingController extends AbstractController
{
    #[Route("/admin/mailing", name: "app_mailing")]

    public function index(MailerInterface $mailer, FouRepository $repo, Request $request, SluggerInterface $slugger): Response
    {

        $erreur = [];
        $form = $this->createForm(MailingType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $listMails = $repo->getAllMail($form->getData()['dossier'], $form->getData()['tiers']);

            //dd($listMails);

            $mails = [];
            // on fait une liste des mails présents dans les champs mail fiche tiers, web et mail contact
            $n = 0;
            for ($i = 0; $i < count($listMails); $i++) {
                for ($c = 0; $c <= 2; $c++) {
                    if ($c == 0) {
                        if ($listMails[$i]['mail'] != '' && !empty($listMails[$i]['mail'])) {
                            $mails[$n]['tiers'] = $listMails[$i]['tiers'];
                            $mails[$n]['nom'] = $listMails[$i]['nom'];
                            $mails[$n]['mail'] = str_replace(' ', '', strtolower($listMails[$i]['mail']));
                            $n++;
                        }
                    }
                    if ($c == 1) {
                        if ($listMails[$i]['web'] != '' && !empty($listMails[$i]['web'])) {
                            $mails[$n]['tiers'] = $listMails[$i]['tiers'];
                            $mails[$n]['nom'] = $listMails[$i]['nom'];
                            $mails[$n]['mail'] = str_replace(' ', '', strtolower($listMails[$i]['web']));
                            $n++;
                        }
                    }
                    if ($c == 2) {
                        if ($listMails[$i]['contactMail'] != '' && !empty($listMails[$i]['contactMail'])) {
                            $mails[$n]['tiers'] = $listMails[$i]['tiers'];
                            $mails[$n]['nom'] = $listMails[$i]['nom'];
                            $mails[$n]['mail'] = str_replace(' ', '', strtolower($listMails[$i]['contactMail']));
                            $n++;
                        }
                    }
                }
            }
            // suppression des doublons
            $mails = array_map("unserialize", array_unique(array_map("serialize", $mails)));
            //dd($mails);

            // on repére les champs qui contiennent des ; ou des , pour les scinder
            $mails = array_values($mails); // on réindexe le tableau
            //dd($mails);
            for ($m = 0; $m < count($mails); $m++) {
                if (strstr($mails[$m]['mail'], ';')) {
                    $explode = explode(';', $mails[$m]['mail']);
                    foreach ($explode as $val) {
                        $mails = array_values($mails); // on réindexe le tableau
                        $nbe = count($mails) + 1; // on compte le nombre de ligne
                        $mails[$nbe]['tiers'] = $mails[$m]['tiers'];
                        $mails[$nbe]['nom'] = $mails[$m]['nom'];
                        $mails[$nbe]['mail'] = $val;
                    }
                    unset($mails[$m]['mail']);
                    $mails = array_values($mails); // on réindexe le tableau
                }
            }

            // on supprime les mails vides aprés le traitement de scindement.
            /*foreach ($mails as $value) {
            if (empty($value['mail'])) {
            unset($mails[array_search($value['mail'], $mails)]);
            }
            }*/
            // suppression des doublons
            $mails = array_map("unserialize", array_unique(array_map("serialize", $mails)));
            //dd($mails);
            $objet = $form->getData()['objet'];
            $message = $form->getData()['message'];
            //$de = $form->getData()['de'];
            //$file = $form->get('file')->getData();
            /*$fichier = '';
            $newFilename = "";
            if ($file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '.' . $file->guessExtension();
            $chemin = 'doc_lhermitte';
            try {
            $file->move(
            $this->getParameter($chemin, $file->getClientOriginalName()),
            $newFilename
            );
            } catch (FileException $e) {
            // ... handle exception if something happens during file upload
            }
            //$fichier = str_replace("\\","/",$this->getParameter('doc_lhermitte')) .'/'. str_replace("\\","/",$file->getClientOriginalName());
            }*/

            //dd($mails);
            ini_set('memory_limit', '1024M');
            ini_set('max_execution_time', 0);
            // désactiver le Garbage Collector
            gc_disable();
            //$adresse = 'C:/wamp64/www/Intranet/public/doc/Lhermitte_freres/Autorisation-de-facture-dematerialisee-Roby.pdf';
            $i = 0;
            $ligErreur = 0;
            foreach ($mails as $value) {
                //if ($i >= 386) {
                if (!!!empty($value['mail'])) {
                    try {
                        $email = (new Email())
                            ->from('jpochet@groupe-axis.fr')
                            //->to('jpochet@groupe-axis.fr')
                            ->to($value['mail'])
                            ->priority(Email::PRIORITY_HIGH)
                            ->subject($objet)
                            ->html($message);
                        //->attachFromPath($adresse);
                        //if ($form->getData()['test'] == false) {
                        $mailer->send($email);
                        $i++;
                        //}
                    } catch (Exception $e) {
                        $erreur[$ligErreur]['tiers'] = $value['tiers'];
                        $erreur[$ligErreur]['nom'] = $value['nom'];
                        $erreur[$ligErreur]['mail'] = $value['mail'];
                        $ligErreur++;
                    }
                }
                /*if ($fichier) {
                unlink($adresse);
                }*/
                //}
            }
            if ($i == 0 | $i == 1) {
                $this->addFlash('message', $i . ' Mail envoyé');
            } elseif ($i >= 2) {
                $this->addFlash('message', $i . ' Mails envoyés');
            }
            // lancement manuel du Garbage Collector pour libérer de la mémoire
            gc_collect_cycles();
        }

        return $this->render('mailling/index.html.twig', [
            'title' => 'Mailing',
            'form' => $form->createView(),
            'erreurs' => $erreur,
        ]);
    }

}
