<?php

namespace App\Controller;

use App\Form\YearMonthType;
use App\Repository\Divalto\RpdRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_INFORMATIQUE")
 */

class DeclarationAgenceEauController extends AbstractController
{
    /**
     * @Route("/declaration/agence/eau", name="app_declaration_agence_eau")
     */
    public function index(Request $request, RpdRepository $repo): Response
    {
        
            $form = $this->createForm(YearMonthType::class);
            $form->handleRequest($request);
            // initialisation de mes variables
            $annee = '';
            $declaration = '';

            // tracking user page for stats
            $tracking = $request->attributes->get('_route');
            $this->setTracking($tracking);
            
            if($form->isSubmitted() && $form->isValid()){
                $annee = $form->getData()['year'];
                // Déclaration non filtrée
                $declarationBrut = $repo->getRpd($annee);
                
                // Filtrer le tableau pour supprimer les doublons en CP/AMM

                for ($ligDeclarationBrut=0; $ligDeclarationBrut <count($declarationBrut) ; $ligDeclarationBrut++) {
                        $declarationFiltre[$ligDeclarationBrut]['cp'] = $declarationBrut[$ligDeclarationBrut]['Cp'];
                        $declarationFiltre[$ligDeclarationBrut]['amm'] = $declarationBrut[$ligDeclarationBrut]['Amm'];
                        $declarationFiltre[$ligDeclarationBrut]['typeArt'] = $declarationBrut[$ligDeclarationBrut]['TypeArt'];
                        $declarationFiltre[$ligDeclarationBrut]['qte'] = 0;
                }
                // déclaration filtrée
                $declarationFiltre = array_values(array_unique($declarationFiltre, SORT_REGULAR));

                // Faire une somme des quantités par département et amm 
                // Pour chaque ligne de la déclaration filtrée
                for ($ligDecla=0; $ligDecla <count($declarationFiltre) ; $ligDecla++) { 
                    // balayer la déclaration brut
                    for ($ligDeclarationBrute=0; $ligDeclarationBrute <count($declarationBrut) ; $ligDeclarationBrute++) { 
                        // si le Cp et l'AMM correspondent
                        if ($declarationFiltre[$ligDecla]['cp'] == $declarationBrut[$ligDeclarationBrute]['Cp'] && $declarationFiltre[$ligDecla]['amm'] == $declarationBrut[$ligDeclarationBrute]['Amm'] ) {
                            $declarationFiltre[$ligDecla]['qte'] += $declarationBrut[$ligDeclarationBrute]['QteSign'];
                            $declarationFiltre[$ligDecla]['ref'] = $declarationBrut[$ligDeclarationBrute]['Ref'];
                            $declarationFiltre[$ligDecla]['designation'] = $declarationBrut[$ligDeclarationBrute]['Designation'];
                            $declarationFiltre[$ligDecla]['uv'] = $declarationBrut[$ligDeclarationBrute]['Uv'];
                        }
                    }
                }
                $declaration = array_values($declarationFiltre);

            }
        
        return $this->render('declaration_agence_eau/index.html.twig', [
            'controller_name' => 'DeclarationAgenceEauController',
            'title' => 'Déclaration RPD',
            'declarations' => $declaration,
            'monthYear' => $form->createView()
        ]);
    }
}
