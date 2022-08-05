<?php

namespace App\Controller;

use DateTime;
use App\Form\AddEmailType;
use App\Entity\Main\MailList;
use App\Form\AddEmailFeuType;
use Symfony\Component\Mime\Address;
use App\Form\AddEmailTreatementType;
use App\Repository\Main\MailListRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

 class AdminEmailController extends AbstractController
{

    private $repoMail;
    
    public function __construct(MailListRepository $repoMail)
    {
        $this->repoMail = $repoMail;
        //parent::__construct();
    }
    
    
    /**
     * @Route("/admin/email", name="app_admin_email")
     */
    public function index(Request $request): Response
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);
        
        unset($formSendGeneralWithMails);
        $formSendGeneralWithMails = $this->createForm(AddEmailType::class);
        $formSendGeneralWithMails->handleRequest($request);
        if($formSendGeneralWithMails->isSubmitted() && $formSendGeneralWithMails->isValid()){
            $find = $this->repoMail->findBy(['email' => $formSendGeneralWithMails->getData()['email'], 'page' => $tracking, 'SecondOption' => 'envoi']);
            if (empty($find) | is_null($find)) {
                $mailEnvoi = $this->repoMail->getEmailEnvoi();
                if ($mailEnvoi == NULL) {
                    $mail = new MailList();
                    $mail->setCreatedAt(new DateTime())
                    ->setEmail($formSendGeneralWithMails->getData()['email'])
                    ->setPage($tracking)
                    ->setSecondOption('envoi');
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($mail);
                    $em->flush();
                }else {
                    $this->addFlash('danger', 'Un seul email autorisé pour l\'envoi des Emails à partir du site intranet');
                    return $this->redirectToRoute('app_admin_email');
                }
            }else {
                $this->addFlash('danger', 'le mail est déjà inscrit pour ce paramétre !');
                return $this->redirectToRoute($tracking);
            }
        }
        
        unset($formSendTreatementWithMails);
        $formSendTreatementWithMails = $this->createForm(AddEmailTreatementType::class);
        $formSendTreatementWithMails->handleRequest($request);
        if($formSendTreatementWithMails->isSubmitted() && $formSendTreatementWithMails->isValid()){
            $find = $this->repoMail->findBy(['email' => $formSendTreatementWithMails->getData()['email'], 'page' => $tracking, 'SecondOption' => 'traitement']);
            if (empty($find) | is_null($find)) {
                $mailEnvoi = $this->repoMail->findOneBy(['page' => 'app_admin_email', 'SecondOption' => 'traitement']);
                if ($mailEnvoi == NULL) {
                    $mail = new MailList();
                    $mail->setCreatedAt(new DateTime())
                    ->setEmail($formSendTreatementWithMails->getData()['email'])
                    ->setPage($tracking)
                    ->setSecondOption('traitement');
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($mail);
                    $em->flush();
                }else {
                    $this->addFlash('danger', 'Un seul email autorisé pour le traitement des Emails en provenance du site intranet');
                    return $this->redirectToRoute('app_admin_email');
                }
            }else {
                $this->addFlash('danger', 'le mail est déjà inscrit pour ce paramétre !');
                return $this->redirectToRoute($tracking);
            }
        }

        unset($formFeu);
        $formFeu = $this->createForm(AddEmailFeuType::class);
        $formFeu->handleRequest($request);
        if($formFeu->isSubmitted() && $formFeu->isValid()){
            $find = $this->repoMail->findBy(['email' => $formFeu->getData()['email'], 'page' => $tracking, 'SecondOption' => 'feu']);
            if (empty($find) | is_null($find)) {
                $mail = new MailList();
                $mail->setCreatedAt(new DateTime())
                     ->setEmail($formFeu->getData()['email'])
                     ->setPage($tracking)
                     ->setSecondOption('feu');
                $em = $this->getDoctrine()->getManager();
                $em->persist($mail);
                $em->flush();
            }else {
                $this->addFlash('danger', 'le mail est déjà inscrit pour cette page !');
                return $this->redirectToRoute($tracking);
            }
        }

        return $this->render('admin_email/index.html.twig', [
            'controller_name' => 'AdminEmailController',
            'formSendGeneralWithMails' => $formSendGeneralWithMails->createView(),
            'formSendTreatementWithMails' => $formSendTreatementWithMails->createView(),
            'formFeu' => $formFeu->createView(),
            'listeMailsGeneral' => $this->repoMail->findBy(['page' => $tracking, 'SecondOption' => 'envoi']),
            'listeMailsTreatement' => $this->repoMail->findBy(['page' => $tracking, 'SecondOption' => 'traitement']),
            'listeMailsFeu' => $this->repoMail->findBy(['page' => $tracking, 'SecondOption' => 'feu']),
            'title' => "Paramétrage des Emails"
        ]);
    }

    /**
     * @Route("/admin/email/delete/{id}", name="app_admin_email_delete")
     */
    public function deleteMail($id): Response
    {

       $search = $this->repoMail->findOneBy(['id' => $id]);
        
            $em = $this->getDoctrine()->getManager();
            $em->remove($search);
            $em->flush();

        $this->addFlash('message', 'le mail a bien été supprimé !');
        return $this->redirectToRoute('app_admin_email');
    }

    /**
     * @Route("/email/delete/{id}/{route}", name="app_email_delete_redirect")
     */
    public function deleteMailAndRedirect($id,$route): Response
    {

       $search = $this->repoMail->findOneBy(['id' => $id]);
        
            $em = $this->getDoctrine()->getManager();
            $em->remove($search);
            $em->flush();

        $this->addFlash('message', 'le mail a bien été supprimé !');
        return $this->redirectToRoute($route);
    }

    public function formateEmailList($listMails)
    {
        $MailsList = [];
        foreach ($listMails as $value) {
            array_push( $MailsList, new Address($value->getEmail()) );
        }   
        return $MailsList;
    }
}
