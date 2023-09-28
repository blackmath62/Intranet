<?php

namespace App\Controller;

use App\Entity\Main\FournisseursDivalto;
use App\Repository\Divalto\FouRepository;
use App\Repository\Main\FournisseursDivaltoRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]

class FournisseursDivaltoController extends AbstractController
{

    private $repoFou;
    private $repoFouDivalto;
    private $entityManager;

    public function __construct(ManagerRegistry $registry, FournisseursDivaltoRepository $repoFouDivalto, FouRepository $repoFou)
    {
        $this->repoFou = $repoFou;
        $this->repoFouDivalto = $repoFouDivalto;
        $this->entityManager = $registry->getManager();
    }

    #[Route("/fournisseurs/divalto", name: "app_fournisseurs_divalto")]
    // Mise à jour de la liste des fournisseurs Divalto dans la base SQL du Main
    public function maj(): Response
    {
        $fournisseurs = $this->repoFou->getFournisseurDivalto();

        foreach ($fournisseurs as $value) {
            $fournisseur = $this->repoFouDivalto->findOneBy(['tiers' => $value['tiers']]);
            if (!$fournisseur) {
                $fournisseur = new FournisseursDivalto();
                $fournisseur->setTiers($value['tiers'])
                    ->setNom($value['nom'])
                    ->setCreatedAt(new DateTime);
            } else {
                $fournisseur->setNom($value['nom']);
            }
            $em = $this->entityManager;
            $em->persist($fournisseur);
            $em->flush();
        }

        return $this->render('fournisseurs_divalto/index.html.twig', [
            'controller_name' => 'FournisseursDivaltoController',
        ]);
    }
}
