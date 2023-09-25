<?php

namespace App\Controller;

use App\Form\ModifiedPasswordType;
use App\Form\PasswordForgotType;
use App\Form\UserRegistrationFormType;
use App\Repository\Main\MailListRepository;
use App\Repository\Main\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class SecurityController extends AbstractController
{

    public const LAST_EMAIL = 'app_login_form_last_email';
    private $repoMail;
    private $mailEnvoi;
    private $mailTreatement;
    private $entityManager;

    public function __construct(ManagerRegistry $registry, MailListRepository $repoMail)
    {
        $this->repoMail = $repoMail;
        $this->mailEnvoi = $this->repoMail->getEmailEnvoi();
        $this->mailTreatement = $this->repoMail->getEmailTreatement();
        $this->entityManager = $registry->getManager();
        //parent::__construct();
    }

    #[Route("/login", name: "app_login", methods: ["GET", "POST"])]

    public function login(): Response
    {
        return $this->render('security/login.html.twig', [
            'controller_name' => 'SecurityController',
            'title' => "connexion",
        ]);
    }
    #[Route("/register", name: "app_register", methods: ["GET", "POST"])]

    function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordEncoder, MailerInterface $mailerInterface, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(UserRegistrationFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $plainPassword = $form['plainPassword']->getData();
            $user->setCreatedAt(new \DateTime())
                ->setRoles(["ROLE_USER"])
                ->setPassword($passwordEncoder->hashPassword($user, $plainPassword))
                ->setToken(md5(uniqid()));

            $file = $form->get('img')->getData();

            if ($file) {

                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('doc_profiles'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'Filename' property to store the PDF file name
                // instead of its contents
                $user->setImg($newFilename);
            } else {
                $user->setImg('AdminLTELogo.png');
            }
            $em->persist($user);
            $em->flush();

            $email = (new Email())
                ->from($this->mailEnvoi)
                ->to($user->getEmail())
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                ->priority(Email::PRIORITY_HIGH)
                ->subject('Activation de compte intranet !')
                //->text('Sending emails is fun again!')
                ->html($this->renderView('mails/activation.html.twig', ['token' => $user->getToken()]));

            $mailerInterface->send($email);

            $emailAdmin = (new Email())
                ->from($this->mailEnvoi)
                ->to($this->mailTreatement)
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                ->priority(Email::PRIORITY_HIGH)
                ->subject('Un compte doit être paramétré sur le site intranet !')
                //->text('Sending emails is fun again!')
                ->html('Merci de paramétrer le compte de ' . $user->getEmail());

            $mailerInterface->send($emailAdmin);
            $this->addFlash('warning', 'Veuillez consulter vos mails et activer votre compte, le mail de validation peut arriver dans vos courriers indésirables');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'controller_name' => 'SecurityController',
            'title' => "inscription",
            'registrationForm' => $form->createView(),
        ]);
    }
    #[Route("/passwordForgot", name: "app_password_forgot")]

    // envoyer un mail pour réinitialiser le mot de passe
    function passwordforgot(Request $request, MailerInterface $mailerInterface, UsersRepository $usersRepo, EntityManagerInterface $em)
    {
        $form = $this->createForm(PasswordForgotType::class);
        // lecture des données
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            // On verifie si l'utilisateur existe et qu'il n'est pas fermé
            $user = $usersRepo->findOneBy(['email' => $user->getEmail(), 'closedAt' => null]);
            // si l'utilisateur n'existe pas
            if (!$user) {
                // erreur 404
                throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
            }
            // création d'un token
            $user->setToken(md5(uniqid()));
            // envoyer un mail
            $em->persist($user);
            $em->flush();

            $email = (new Email())
                ->from($this->mailEnvoi)
                ->to($user->getEmail())
                ->priority(Email::PRIORITY_HIGH)
                ->subject('Modification de votre mot de passe !')
                ->html($this->renderView('mails/passwordForgot.html.twig', ['token' => $user->getToken()]));

            $mailerInterface->send($email);

            return $this->redirectToRoute('app_login');
            // on envoie un message flash
            $this->addFlash('message', 'Un email vous a été envoyé');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('security/passwordForgot.html.twig', [
            'controller_name' => 'SecurityController',
            'title' => "mot de passe oublié",
            'passwordForgotForm' => $form->createView(),
        ]);
    }
    #[Route("/changePassword/{token}", name: "app_change_password")]

    function changePassword($token, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordEncoder, UsersRepository $usersRepo)
    {
        $form = $this->createForm(ModifiedPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // On verifie si un utilisateur a ce token
            // Vérification du token utilisateur  et qu'il n'est pas fermé
            $user = $usersRepo->findOneBy(['token' => $token, 'closedAt' => null]);

            // si aucun utilisateur n'existe avec ce token
            if (!$user) {
                // erreur 404
                throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
            }
            //$user = $form->getData();
            //dd($user);
            // Modification du mot de passe de l'utilisateur
            $plainPassword = $form['plainPassword']->getData();
            $user->setPassword($passwordEncoder->hashPassword($user, $plainPassword))
            // on supprime le token
                ->setToken(null);
            $em->persist($user);
            $em->flush();

            // on envoie un message flash
            $this->addFlash('message', 'Vous avez activé votre compte');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('security/changePassword.html.twig', [
            'controller_name' => 'SecurityController',
            'title' => "Changement de mot de passe",
            'changePasswordForm' => $form->createView(),
        ]);
    }

    #[Route("/logout", name: "app_logout", methods: ["GET"])]

    function logout()
    {
        $this->addFlash('warning', 'Vous avez bien été déconnecté !');
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    #[Route("/activation/{token}", name: "app_activation", methods: ["GET"])]

    function activation($token, UsersRepository $usersRepo)
    {
        // On verifie si un utilisateur a ce token
        $user = $usersRepo->findOneBy(['token' => $token]);

        // si aucun utilisateur n'existe avec ce token
        if (!$user) {
            // erreur 404
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }
        // on supprime le token
        $user->setToken(null);
        $em = $this->entityManager;
        $em->persist($user);
        $em->flush();

        // on envoie un message flash
        $this->addFlash('message', 'Vous avez activé votre compte');
        return $this->redirectToRoute('app_home');
    }
}
