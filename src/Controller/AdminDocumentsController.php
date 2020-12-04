<?php

namespace App\Controller;


use DateTime;
use App\Entity\Users;
use App\Entity\Documents;
use App\Form\AdminDocumentsType;
use App\Repository\DocumentsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class AdminDocumentsController extends AbstractController
{
    /**
     * @Route("/admin/documents", name="app_admin_documents")
     */
    public function index(Request $request, DocumentsRepository $repo, Users $user)
    {
        $document = new Documents();
        $form = $this->createForm(AdminDocumentsType::class, $document);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $someNewFilename = "Fichier de test";
                $file = $form['url']->getData();
                $file->move("public/doc/", $someNewFilename);
                $document->setCreatedAt(new DateTime());
                $user = $this->getUser;
                dd($user);
                $document->setUser($request->user->getId());
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
                'title' => "Administration des documents"
            ]);
        
    }
}
