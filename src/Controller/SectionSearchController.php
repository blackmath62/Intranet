<?php

namespace App\Controller;

use App\Entity\Main\SectionSearch;
use App\Form\EditSectionSearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Main\SectionSearchRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
* @IsGranted("ROLE_ADMIN")
*/

class SectionSearchController extends AbstractController
{
    /**
     * @Route("/admin/section_searchs", name="app_admin_section_search")
     */
   
    public function index(SectionSearch $section_search = null, Request $request, SectionSearchRepository $repo, EntityManagerInterface $manager)
    {
        if(!$section_search){
            $section_search = new SectionSearch();
            }
            $form = $this->createFormBuilder($section_search)
                ->add("nom")
                ->add("backgroundColor")
                ->add("textColor")
                ->add('fa')
                ->getForm();
            $form->handleRequest($request);

            // tracking user page for stats
            $tracking = $request->attributes->get('_route');
            $this->setTracking($tracking);

            if ($form->isSubmitted() && $form->isValid()) {
                $section_search->setCreatedAt(new \DateTime());
                $manager->persist($section_search);
                $manager->flush();
            }
            $section_searchs = $repo->findAll();
            return $this->render('section_search/index.html.twig', [
                'section_searchs' => $section_searchs,
                'formSectionSearch' => $form->createView(),
                'title' => "Administration des mots de recherche"
            ]);
    }

    /**
     * @Route("/admin/section_search/delete/{id}",name="app_delete_section_search")
     */
    public function deletesection_search($id, Request $request)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(SectionSearch::class);
        $section_searchId = $repository->find($id);
         
        $em = $this->getDoctrine()->getManager();
        $em->remove($section_searchId);
        $em->flush();        

        // tracking user page for stats
            $tracking = $request->attributes->get('_route');
            $this->setTracking($tracking);

        return $this->redirect($this->generateUrl('app_admin_section_search'));
    }
    /**
     * @Route("/admin/section_search/edit/{id}",name="app_edit_section_search")
     */
    public function editsection_search(SectionSearch $section_search, Request $request)
    {
        $form = $this->createForm(EditSectionSearchType::class, $section_search);
            $form->handleRequest($request);

            // tracking user page for stats
            $tracking = $request->attributes->get('_route');
            $this->setTracking($tracking);
            
            if($form->isSubmitted() && $form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($section_search);
                $em->flush();

                $this->addFlash('message', 'Mot clé modifié avec succès');
                return $this->redirectToRoute('app_admin_section_search');

            }
            return $this->render('section_search/edit_section_searchs.html.twig',[
                'section_searchEditForm' => $form->createView(),
                'section_searchs' => $section_search,
                'title' => 'Edition des Mots de recherche'
            ]);
    }
}
