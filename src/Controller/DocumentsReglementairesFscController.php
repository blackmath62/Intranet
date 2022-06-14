<?php

namespace App\Controller;

use DateTime;
use App\Form\DocumentsReglementairesFscType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Repository\Main\DocumentsReglementairesFscRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @IsGranted("ROLE_USER")
 */

class DocumentsReglementairesFscController extends AbstractController
{
    /**
     * @Route("/Roby/documents/reglementaires/fsc", name="app_documents_reglementaires_fsc")
     */
    public function index(Request $request, DocumentsReglementairesFscRepository $repo, SluggerInterface $slugger): Response
    {
        
        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);
        
        $form = $this->createForm(DocumentsReglementairesFscType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $file = $form->get('files')->getData();
            $year = $form->get('years')->getData();
             if ($file) {
                $d = new DateTime();
                
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                 // this is needed to safely include the file name as part of the URL
                 $safeFilename = $slugger->slug($originalFilename);
                 $newFilename = $safeFilename . '-' . $d->format('Y-m-d H-i-s') . '.' . $file->guessExtension();
                 $path = 'doc/Roby/Fsc/' . $year . '/';
                    if (!is_dir($path)) {
                        mkdir($path, 0777, true);
                    }

                 // Move the file to the directory where brochures are stored
                 try {
                     $file->move($path,$newFilename);
                 } catch (FileException $e) {
                     // ... handle exception if something happens during file upload
                 }
                 // updates the 'Filename' property to store the PDF file name
                 // instead of its contents
                $doc = $form->getData();
                $doc->setCreatedAt(new \DateTime())
                    ->setFiles($newFilename)
                    ->setAddBy($this->getUser()->getId());
                $em = $this->getDoctrine()->getManager();
                $em->persist($doc);
                $em->flush();
             }

            $this->addFlash('message', 'Document ajouté avec succés');
            return $this->redirectToRoute('app_documents_reglementaires_fsc');
        }

        $docs = $repo->findAll();

        return $this->render('documents_reglementaires_fsc/index.html.twig', [
            'form' => $form->createView(),
            'docs' => $docs,
            'title' => 'Documents Fsc' 
        ]);
    }

    /**
     * @Route("/Roby/documents/reglementaires/fsc/show/{id}", name="app_documents_reglementaires_fsc_show")
     */

    public function DocsRegleFscShow(int $id, DocumentsReglementairesFscRepository $repo, Request $request)
    {
        $doc = $repo->findOneBy(['id' => $id]);

        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);

        return $this->render('faq/faq_show.html.twig', [
            'doc' => $doc,
            'title' => 'Document'
        ]);
    }

    /**
     * @Route("/Roby/documents/reglementaires/fsc/delete/{id}", name="app_documents_reglementaires_fsc_delete")
     */

    public function DocsRegleFscDelete(int $id, DocumentsReglementairesFscRepository $repo, Request $request)
    {
        $doc = $repo->findOneBy(['id' => $id]);

        // tracking user page for stats
        $tracking = $request->attributes->get('_route');
        $this->setTracking($tracking);
        
     
            $em = $this->getDoctrine()->getManager();
            $em->remove($doc);
            $em->flush();
            
            $this->addFlash('message', 'Document supprimé avec succés');
            return $this->redirectToRoute('app_documents_reglementaires_fsc', ['id' => $id ]);
    }
}
