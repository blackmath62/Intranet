<?php

namespace App\Controller;

use App\Entity\Main\documentsFsc;
use App\Entity\Main\fscListMovement;
use DateTime;
use App\Entity\Main\MovBillFsc;
use App\Form\FactureFournisseursFscType;
use App\Repository\Main\UsersRepository;
use App\Repository\Divalto\EntRepository;
use App\Repository\Divalto\MouvRepository;
use App\Repository\Main\documentsFscRepository;
use App\Repository\Main\MovBillFscRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_ROBY")
 */

class MovementBillFscController extends AbstractController
{
    private $repoFact;
    private $repoEnt;
    private $repoMouv;
    private $repoDocs;

    public function __construct(documentsFscRepository $repoDocs, MouvRepository $repoMouv, MovBillFscRepository $repoFact, EntRepository $repoEnt)
    {
        $this->repoFact = $repoFact;
        $this->repoEnt = $repoEnt;
        $this->repoMouv = $repoMouv;
        $this->repoDocs = $repoDocs;
        //parent::__construct();
    }    
    
    /**
     * @Route("/Roby/movement/bill/fsc", name="app_movement_bill_fsc")
     */
    public function index(): Response
    {
        return $this->render('movement_bill_fsc/index.html.twig', [
            'clients' => $this->repoFact->findAll(),
            'title' => 'Factures clients Fsc'
        ]);
    }

    /**
     * @Route("/Roby/movement/bill/fsc/show/{id}", name="app_movement_bill_fsc_show")
     */
    public function show($id=null, Request $request, MovBillFsc $bill): Response
    {
        $form = $this->createForm(FactureFournisseursFscType::class, $bill);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($bill);
            $entityManager->flush();
            
            $this->addFlash('message', 'Mise à jour effectuée avec succés');
            return $this->redirectToRoute('app_movement_bill_fsc_show', ['id' => $id]);
        }
        $facture = $this->repoFact->findOneBy(['id' =>$id]);
        $documents = [];
        foreach ($facture->getVentilations()->getValues() as $value) {
            $docs = $this->repoDocs->findBy(['fscListMovement' => $value->getId() ]);
            foreach ($docs as $doc) {
                array_push($documents, $doc);
            }
        } 
        return $this->render('movement_bill_fsc/show.html.twig', [
            'facture' => $facture,
            'documents' => $documents,
            'title' => 'Détail facture client Fsc',
            'details' => $this->repoMouv->getDetailFactureFscClient($facture->getFacture()),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/Roby/movement/bill/fsc/update", name="app_movement_bill_fsc_update")
     */
    // on ajoute les factures clients qui n'y sont pas déjà
    public function update(UsersRepository $users): Response
    {
        $user = $users->findOneBy(['pseudo' => 'intranet']);
            $factures = $this->repoEnt->getMouvfactCliFsc();
            foreach ($factures as $value) {
                $bill = $this->repoFact->findOneBy(['facture' => $value['facture']]);
                if ($bill == NULL) {
                    $bill = new MovBillFsc();
                    $bill->setCreatedAt(new DateTime())
                    ->setCreatedBy($user)
                    ->setFacture($value['facture'])
                    ->setdateFact(new DateTime($value['dateFacture']))
                    ->setTiers($value['tiers'])
                    ->setNom($value['nom'])
                    ->setNotreRef($value['notreRef'])
                    ->setTypeTiers($value['typeTiers']);
                    
                }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($bill);
            $entityManager->flush();
    }


        $this->addFlash('message', 'Mise à jour effectuée avec succés');
        return $this->redirectToRoute('app_movement_bill_fsc');
    }

}
