<?php

namespace App\Controller;

use App\Form\UserRegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
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
    public function register(Request $request,EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        
        $form = $this->createForm(UserRegistrationFormType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();
            $plainPassword = $form['plainPassword']->getData();
            $user->setCreatedAt(new \DateTime());
            $user->setPassword($passwordEncoder->encodePassword($user, $plainPassword));
            $em->persist($user);
            $em->flush();

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
    public function passwordforgot()
    {
        return $this->render('security/passwordForgot.html.twig', [
            'controller_name' => 'SecurityController',
            'title' => "mot de passe oublié"
        ]);
    }
    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
