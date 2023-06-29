<?php

namespace App\Controller;

use App\Entity\Main\SignatureElectronique;
use App\Form\Main\SignatureElectroniqueType;
use App\Repository\Main\SignatureElectroniqueRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/signature/electronique")
 */
class SignatureElectroniqueController extends AbstractController
{
    /**
     * @Route("/", name="app_signature_electronique_index", methods={"GET"})
     */
    public function index(SignatureElectroniqueRepository $signatureElectroniqueRepository): Response
    {
        return $this->render('signature_electronique/index.html.twig', [
            'signature_electroniques' => $signatureElectroniqueRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_signature_electronique_new", methods={"GET", "POST"})
     */
    function new (Request $request, SignatureElectroniqueRepository $signatureElectroniqueRepository): Response{
        $signatureElectronique = new SignatureElectronique();
        $form = $this->createForm(SignatureElectroniqueType::class, $signatureElectronique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $signatureElectronique->setCreatedAt(new DateTime());
            $signatureElectronique->setCreatedBy($this->getUser());
            $signatureElectroniqueRepository->add($signatureElectronique);
            return $this->redirectToRoute('app_signature_electronique_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('signature_electronique/new.html.twig', [
            'signature_electronique' => $signatureElectronique,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_signature_electronique_show", methods={"GET"})
     */
    public function show(SignatureElectronique $signatureElectronique): Response
    {
        return $this->render('signature_electronique/show.html.twig', [
            'signature_electronique' => $signatureElectronique,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_signature_electronique_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, SignatureElectronique $signatureElectronique, SignatureElectroniqueRepository $signatureElectroniqueRepository): Response
    {
        $form = $this->createForm(SignatureElectroniqueType::class, $signatureElectronique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $signatureElectroniqueRepository->add($signatureElectronique);
            return $this->redirectToRoute('app_signature_electronique_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('signature_electronique/edit.html.twig', [
            'signature_electronique' => $signatureElectronique,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_signature_electronique_delete", methods={"POST"})
     */
    public function delete(Request $request, SignatureElectronique $signatureElectronique, SignatureElectroniqueRepository $signatureElectroniqueRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $signatureElectronique->getId(), $request->request->get('_token'))) {
            $signatureElectroniqueRepository->remove($signatureElectronique);
        }

        return $this->redirectToRoute('app_signature_electronique_index', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * @Route("/{id}"/signature, name="app_signature_electronique", methods={"GET"})
     */
    /*public function signature(SignatureElectronique $signatureElectronique, SignatureElectroniqueRepository $signatureElectroniqueRepository, YousignService $yousignService): Response
{
$yousignSignatureRequest = $yousignService->signatureRequest();
$signatureElectronique->setSignatureId($yousignSignatureRequest['id']);
$signatureElectroniqueRepository->save($signatureElectronique, true);

$uploadDocument = $yousignService->uploadDocument($signatureElectronique->getSignatureId(), $signatureElectronique->getPdfSansSignature());
$signatureElectronique->setDocumentId($uploadDocument['id']);
$signatureElectroniqueRepository->save($signatureElectronique, true);

$signerId = $yousignService->addSigner(
$signatureElectronique->getSignatureId(),
$signatureElectronique->getDocumentId(),
$signatureElectronique->getMail(),
$signatureElectronique->getPrenom(),
$signatureElectronique->getNom()
);
$signatureElectronique->setSignerId($signerId['id']);
$signatureElectroniqueRepository->save($signatureElectronique, true);

return $this->redirectToRoute('app_signature_electronique_index', [], Response::HTTP_SEE_OTHER);
}*/
}
