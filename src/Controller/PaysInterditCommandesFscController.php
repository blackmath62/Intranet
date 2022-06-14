<?php

namespace App\Controller;

use DateTime;
use App\Entity\Main\PaysBanFsc;
use App\Form\PaysBanType;
use App\Repository\Main\PaysBanFscRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 */

class PaysInterditCommandesFscController extends AbstractController
{
    /**
     * @Route("/Roby/pays/interdit/commandes/fsc", name="app_pays_interdit_commandes_fsc")
     */
    public function index(PaysBanFscRepository $repo, Request $request): Response
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);      
        
        $form = $this->createForm(PaysBanType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $pays = $form->get('pays')->getData();
            $pay = new PaysBanFsc();
            $pay->setCreatedAt(new DateTime());
            $pay->setCreatedBy($this->getUser())
                ->setPays(strtoupper($pays));
            $em = $this->getDoctrine()->getManager();
            $em->persist($pay);
            $em->flush();
        }
        return $this->render('pays_interdit_commandes_fsc/index.html.twig', [
            'pays' => $repo->findAll(),
            'form' => $form->createView(),
            'title' => 'Pays Ban Fsc'

        ]);
    }

    /**
     * @Route("/Roby/pays/interdit/commandes/fsc/delete{id}", name="app_pays_interdit_commandes_fsc_delete")
     */
    public function delete($id, PaysBanFscRepository $repo, Request $request): Response
    {
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);      
        
        
            $pay = $repo->findOneBy(['id' => $id]);
            $em = $this->getDoctrine()->getManager();
            $em->remove($pay);
            $em->flush();

            $this->addFlash('message', 'Pays Supprimé avec succés');
            return $this->redirectToRoute('app_pays_interdit_commandes_fsc');
    }
}
