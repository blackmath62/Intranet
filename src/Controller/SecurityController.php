<?php

namespace App\Controller;

use App\Form\ChangepasswordType;
use App\Form\PasswordForgotType;
use Symfony\Component\Mime\Email;
use App\Repository\UsersRepository;
use App\Form\UserRegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{

    public const LAST_EMAIL = 'app_login_form_last_email';

    /**
     * @Route("/login", name="app_login" , methods={"GET", "POST"})
     */
    public function login(): Response
    {
        return $this->render('security/login.html.twig', [
            'controller_name' => 'SecurityController',
            'title' => "connexion"
        ]);
    }
    /**
     * @Route("/register", name="app_register", methods={"GET","POST"})
     */
    public function register(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, MailerInterface $mailerInterface): Response
    {
        $form = $this->createForm(UserRegistrationFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $plainPassword = $form['plainPassword']->getData();
            $user->setCreatedAt(new \DateTime())
                ->setRoles(["ROLE_USER"])
                ->setPassword($passwordEncoder->encodePassword($user, $plainPassword))
                ->setToken(md5(uniqid()));
            $em->persist($user);
            $em->flush();

            $email = (new Email())
                ->from('intranet@groupe-axis.fr')
                ->to($user->getEmail())
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                ->priority(Email::PRIORITY_HIGH)
                ->subject('Activation de compte intranet !')
                //->text('Sending emails is fun again!')
                ->html($this->renderView('mails/activation.html.twig', ['token' => $user->getToken()]));

            $mailerInterface->send($email);
            $this->addFlash('warning', 'Veuillez consulter vos mails et activer votre compte, le mail de validation peut arriver dans vos courriers indésirables');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'controller_name' => 'SecurityController',
            'title' => "inscription",
            'registrationForm' => $form->createView()
        ]);
    }
    /**
     * @Route("/passwordForgot", name="app_password_forgot")
     */
    // envoyer un mail pour réinitialiser le mot de passe
    public function passwordforgot(Request $request, MailerInterface $mailerInterface, UsersRepository $usersRepo, EntityManagerInterface $em)
    {
        $form = $this->createForm(PasswordForgotType::class);
        // lecture des données
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            // On verifie si l'utilisateur existe
            $user = $usersRepo->findOneBy(['email' => $user->getEmail()]);
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
                ->from('intranet@groupe-axis.fr')
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
            'passwordForgotForm' => $form->createView()
        ]);
    }
    /**
     * @Route("/changePassword/{token}", name="app_change_password")
     */
    public function changePassword($token, Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, UsersRepository $usersRepo)
    {
        $form = $this->createForm(ChangepasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()&& $form->isValid()) {
            // On verifie si un utilisateur a ce token
            // Vérification du token utilisateur
            $user = $usersRepo->findOneBy(['token' => $token]);
            // si aucun utilisateur n'existe avec ce token
            if (!$user) {
                // erreur 404
                throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
            }
            $user = $form->getData();
            // Modification du mot de passe de l'utilisateur
            $plainPassword = $form['plainPassword']->getData();
            $user->setPassword($passwordEncoder->encodePassword($user, $plainPassword))
            // on supprime le token
                 ->setToken(null);
            $em->persist($user);
            $em->flush();

            // on envoie un message flash
            $this->addFlash('success', 'Vous avez activé votre compte');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('security/changePassword.html.twig', [
            'controller_name' => 'SecurityController',
            'title' => "Changement de mot de passe",
            'changePasswordForm' => $form->createView()
        ]);
    }


    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout()
    {
        $this->addFlash('warning', 'Vous avez bien été déconnecté !');
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    /**
     * @Route("/activation/{token}", name="app_activation", methods={"GET"})
     */
    public function activation($token, UsersRepository $usersRepo)
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
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        // on envoie un message flash
        $this->addFlash('message', 'Vous avez activé votre compte');
        return $this->redirectToRoute('app_home');
    }
}
