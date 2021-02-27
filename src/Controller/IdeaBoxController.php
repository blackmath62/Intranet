<?php

namespace App\Controller;

use App\Form\IdeaBoxType;
use App\Entity\Main\IdeaBox;
use App\Repository\Main\IdeaBoxRepository;
use App\Repository\Main\TrackingsRepository;
use App\Repository\Main\UsersRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 */

class IdeaBoxController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(Request $request, IdeaBoxRepository $repo, UsersRepository $repoUser, TrackingsRepository $repoTracking): Response
    {
        $idea = new IdeaBox();
        $form = $this->createForm(IdeaBoxType::class, $idea);
        $form->handleRequest($request);

        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $idea->setCreatedAt(new \DateTime());
            $idea->setUser($this->getUser());
            $idea->setStatus(false);
            $entityManager->persist($idea);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }
        $ideas = $repo->findBy(['status' => false]);
        $users = $repoUser->findAll();
        $track = $repoTracking->getLastConnect();
        return $this->render('idea_box/index.html.twig', [
            'ideaBoxForm' => $form->createView(),
            'title' => 'Boite à Idée !',
            'ideas' => $ideas,
            'users' => $users,
            'tracks' => $track
        ]);
    }

    /**
     * @Route("/idea/show/{id}", name="app_idea_show")
     */

    public function IdeaShow(int $id, IdeaBoxRepository $repo, Request $request)
    {
        $idea = $repo->findOneBy(['id' => $id]);

        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        return $this->render('idea_box/idea_show.html.twig', [
            'idea' => $idea,
            'title' => 'Idea View'
        ]);
    }

    /**
     * @Route("/idea/edit/{id}", name="app_idea_edit")
     */

    public function IdeaEdit(int $id, IdeaBoxRepository $repo, Request $request)
    {
        $idea = $repo->findOneBy(['id' => $id]);

        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);
        
        $form = $this->createForm(IdeaBoxType::class, $idea);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $idea = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($idea);
            $em->flush();
            
            $this->addFlash('message', 'Idée Modifiée avec succés');
            return $this->redirectToRoute('app_idea_show', ['id' => $id ]);
        }
        return $this->render('idea_box/idea_edit.html.twig', [
            'ideaForm' => $form->createView(),
            'title' => 'Idea Edit'
        ]);
    }
}
