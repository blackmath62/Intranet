<?php

namespace App\Controller;


use DateTime;
use App\Entity\Documents;
use App\Form\EditDocumentsType;
use App\Form\AdminDocumentsType;
use App\Repository\SocieteRepository;
use App\Repository\DocumentsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class AdminDocumentsController extends AbstractController
{
    /**
     * @Route("/admin/documents", name="app_admin_documents")
     */
    public function index(Request $request, DocumentsRepository $repo, SocieteRepository $repoSociete, SluggerInterface $slugger)
    {
        $document = new Documents();
        $user = $this->getUser();
        $form = $this->createForm(AdminDocumentsType::class, $document);
        $form->handleRequest($request);
        
            if($form->isSubmitted() && $form->isValid()){
                
            // On récupére le fichier dans le formulaire
            
             $file = $form->get('url')->getData();

             if ($file) {
                 // On récupére l'identifiant de la société
                $fileSoc = $form->getData()->getSociete()->getId();
                // On la cherche dans la BDD
                $societe = $repoSociete->findOneBy(['id' => $fileSoc]);
                 $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                 // this is needed to safely include the file name as part of the URL
                 $safeFilename = $slugger->slug($originalFilename);
                 $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
                 // Move the file to the directory where brochures are stored
                 try {
                     $file->move(
                         $this->getParameter($societe->getParameter(), $file->getClientOriginalName()),
                         $newFilename
                     );
                 } catch (FileException $e) {
                     // ... handle exception if something happens during file upload
                 }
                 // updates the 'Filename' property to store the PDF file name
                 // instead of its contents
                 $document->setUrl($newFilename)
                 // on assigne la date de création
                           ->setCreatedAt(new DateTime()) 
                // On assigne l'utilisateur
                           ->setUser($user);
             }

                $em = $this->getDoctrine()->getManager();
                $em->persist($document);
                $em->flush();

                $this->addFlash('success', 'Document ajouté avec succès');
                return $this->redirectToRoute('app_admin_documents');

            }
            $documents = $repo->findAll();

            return $this->render('admin_documents/index.html.twig',[
                'adminDocumentsForm' => $form->createView(),
                'documents' => $documents,
                'title' => "Administration des documents",
                
            ]);
        
    }

    /**
     * @Route("/admin/documents/delete/{id}",name="app_delete_document")
     */
    public function deleteDocuments($id)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(documents::class);
        $documentsId = $repository->find($id);
         
        $em = $this->getDoctrine()->getManager();
        $em->remove($documentsId);
        $em->flush();        

        
        return $this->redirect($this->generateUrl('app_admin_documents'));
    }
    /**
     * @Route("/admin/documents/edit/{id}",name="app_edit_document")
     */
    public function editSociete(documents $documents, Request $request)
    {
        $form = $this->createForm(EditDocumentsType::class, $documents);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($documents);
                $em->flush();

                $this->addFlash('success', 'documents modifié avec succès');
                return $this->redirectToRoute('app_admin_documents');

            }
            return $this->render('admin_documents/edit_documents.html.twig',[
                'title' => 'Edition de Société',
                'documentEditForm' => $form->createView(),
                'documents' => $documents
            ]);
    }
}
