<?php

namespace App\Controller;

use App\Form\YearMonthType;
use App\Repository\Divalto\RpdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment;

#[IsGranted("ROLE_INFORMATIQUE")]

class DeclarationAgenceEauController extends AbstractController
{
    #[Route("/declaration/agence/eau", name: "app_declaration_agence_eau")]

    public function index(SerializerInterface $serializer, Environment $twig, Request $request, RpdRepository $repo): Response
    {

        $form = $this->createForm(YearMonthType::class);
        $form->handleRequest($request);
        // initialisation de mes variables
        $annee = '';
        $declaration = '';

        if ($form->isSubmitted() && $form->isValid()) {
            $annee = $form->getData()['year'];
            // Déclaration non filtrée
            $declarationBrut = $repo->getRpd($annee);

            // Filtrer le tableau pour supprimer les doublons en CP/AMM

            for ($ligDeclarationBrut = 0; $ligDeclarationBrut < count($declarationBrut); $ligDeclarationBrut++) {
                $declarationFiltre[$ligDeclarationBrut]['cp'] = $declarationBrut[$ligDeclarationBrut]['Cp'];
                $declarationFiltre[$ligDeclarationBrut]['amm'] = $declarationBrut[$ligDeclarationBrut]['Amm'];
                $declarationFiltre[$ligDeclarationBrut]['typeArt'] = $declarationBrut[$ligDeclarationBrut]['TypeArt'];
                $declarationFiltre[$ligDeclarationBrut]['qte'] = 0;
            }
            // déclaration filtrée
            $declarationFiltre = array_values(array_unique($declarationFiltre, SORT_REGULAR));

            // Faire une somme des quantités par département et amm
            // Pour chaque ligne de la déclaration filtrée
            for ($ligDecla = 0; $ligDecla < count($declarationFiltre); $ligDecla++) {
                // balayer la déclaration brut
                for ($ligDeclarationBrute = 0; $ligDeclarationBrute < count($declarationBrut); $ligDeclarationBrute++) {
                    // si le Cp et l'AMM correspondent
                    if ($declarationFiltre[$ligDecla]['cp'] == $declarationBrut[$ligDeclarationBrute]['Cp'] && $declarationFiltre[$ligDecla]['amm'] == $declarationBrut[$ligDeclarationBrute]['Amm']) {
                        $declarationFiltre[$ligDecla]['qte'] += $declarationBrut[$ligDeclarationBrute]['QteSign'];
                        $declarationFiltre[$ligDecla]['ref'] = $declarationBrut[$ligDeclarationBrute]['Ref'];
                        $declarationFiltre[$ligDecla]['designation'] = $declarationBrut[$ligDeclarationBrute]['Designation'];
                        $declarationFiltre[$ligDecla]['uv'] = $declarationBrut[$ligDeclarationBrute]['Uv'];
                    }
                }
            }
            $declaration = array_values($declarationFiltre);
            return $this->generateXML($serializer, $twig, $repo, $annee);
        }

        return $this->render('declaration_agence_eau/index.html.twig', [
            'controller_name' => 'DeclarationAgenceEauController',
            'title' => 'Déclaration RPD',
            'declarations' => $declaration,
            'monthYear' => $form->createView(),
        ]);
    }

    /**
     * @Route("/generate-xml/{annee}", name="generate_xml")
     */
    public function generateXML($serializer, $twig, $repo, $annee): Response
    {
        // Initialisez un tableau vide pour stocker les ventes
        $donneesDesVentes = $repo->getRpdXML($annee);

        // Récupérez vos données dans un tableau
        $data = [
            'distributeur' => [
                'NOM_ORGANISME' => 'LHERMITTE FRERES',
                'NOM_SIEGE' => 'LHERMITTE FRERES',
                'CONTACT' => [
                    'NOM' => 'POCHET JEROME',
                    'FONCTION' => 'INFORMATICIEN',
                    'TELEPHONE' => '0763044026',
                    'COURRIEL' => 'jpochet@groupe-axis.fr',
                ],
                'ADRESSE' => [
                    'BATIMENT' => "PARC D'ACTIVITES DE LA CROISETTE",
                    'NUMERO_ET_VOIE' => '25 RUE ABBE JERZY POPIELUSKO',
                    'LIEU_DIT' => null,
                    'INSEE' => '62498',
                ],
                'NUMERO_AGREMENT' => 'NC00285',
                'SIRET' => '78401372400041',
                'CODE_NAF' => '4675Z',
            ],
            'ventes' => $donneesDesVentes,
        ];

        // Utilisez le Serializer pour convertir le tableau en XML
        $xmlData = $serializer->serialize($data, 'xml');

        // Convertir la chaîne XML en tableau associatif
        $encoder = new XmlEncoder();
        $xmlArray = $encoder->decode($xmlData, 'xml');

        // Utilisez Twig pour ajouter la structure XML fixe
        $xmlContent = $twig->render('declaration_agence_eau/xml_template.xml.twig', ['xmlData' => $xmlArray, 'annee' => $annee]);

        // Créez une réponse HTTP avec le contenu XML
        $response = new Response($xmlContent);
        $fileName = 'RPD_LHERMITTE_' . $annee . '.xml';
        // Définir les en-têtes pour forcer le téléchargement du fichier
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileName
        );
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'application/xml');

        return $response;
    }
}
