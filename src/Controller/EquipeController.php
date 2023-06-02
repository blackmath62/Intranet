<?php

namespace App\Controller;

use App\Entity\Main\Equipe;
use App\Form\EquipeType;
use App\Repository\Main\EquipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EquipeController extends AbstractController
{
    /**
     * @Route("/equipe", name="app_equipe")
     */
    public function index(Request $request, EquipeRepository $repo): Response
    {
        $equipe = new Equipe;
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($equipe);
            $em->flush();
            $this->addFlash('message', 'Création effectuée avec succés');
        }

        $equipes = $repo->findAll();

        return $this->render('equipe/index.html.twig', [
            'title' => 'Equipes',
            'equipes' => $equipes,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/equipe/{id}", name="app_edit_equipe")
     */
    public function edit($id, Request $request, EquipeRepository $repo): Response
    {
        $equipe = $repo->findOneBy(['id' => $id]);
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($equipe);
            $em->flush();

            $this->addFlash('message', 'Equipe modifiée avec succés');
            return $this->redirectToRoute('app_equipe');
        }
        return $this->render('equipe/edit.html.twig', [
            'title' => 'Equipes',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/equipe/{id}", name="app_delete_equipe")
     */
    public function delete($id, EquipeRepository $repo): Response
    {
        $equipe = $repo->findOneBy(['id' => $id]);

        $em = $this->getDoctrine()->getManager();
        $em->remove($equipe);
        $em->flush();
        $this->addFlash('message', 'Equipe supprimée avec succés');
        return $this->redirectToRoute('app_equipe');
    }
}
