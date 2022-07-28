<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Component\Mime\Email;
use App\Repository\Main\MailListRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 */
class ContactController extends AbstractController
{
    
    private $repoMail;
    private $mailEnvoi;
    private $mailTreatement;

    public function __construct(MailListRepository $repoMail)
    {
        $this->repoMail =$repoMail;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi()['email'];
        $this->mailTreatement = $this->repoMail->getEmailTreatement()['email'];
        //parent::__construct();
    }
    
    /**
     * @Route("/contact", name="app_contact")
     */
    public function index(Request $request, MailerInterface $mailer)
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);
        
        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
        $email = (new Email())
            ->from($this->mailEnvoi)
            ->to($this->mailTreatement)
            ->subject($data['objet'])
            ->html($this->renderView('mails/contact.html.twig', ['contact' => $form->getData()]));

        $mailer->send($email);
        $this->addFlash('message', 'Message envoyé !');
            return $this->redirectToRoute('app_contact');

        }
        
        return $this->render('contact/index.html.twig', [
            'title' => 'Contact',
            'contactForm' => $form->createView()
        ]);
    }
}
