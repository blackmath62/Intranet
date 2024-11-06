<?php

namespace App\Controller;

use App\Form\DosTiersType;
use App\Repository\Divalto\CliRepository;
use RtfHtmlPhp\Document;
use RtfHtmlPhp\Html\HtmlFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

class TiersFicheContactAdresseController extends AbstractController
{
    #[Route('/tiers/fiche/contact/adresse', name: 'app_tiers_fiche_contact_adresse')]
    public function index(CliRepository $repo, Request $request): Response
    {
        $tiers = [];
        $datasContactsAdresses = "";
        $datasbanque = "";

        $form = $this->createForm(DosTiersType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dos = $form->getData()['dos'];
            $typeTiers = $form->getData()['typeTiers'];

            $datas = $repo->getTiersFiche($dos, $typeTiers);

            foreach ($datas as $value) {
                $formatter = new HtmlFormatter();

                try {
                    $blob = new Document($value['Note']);
                    $blob = $formatter->Format($blob);
                } catch (Throwable $th) {
                    $blob = "";
                }
                try {
                    $blob2 = new Document($value['T2']);
                    $blob2 = $formatter->Format($blob2);
                } catch (Throwable $th) {
                    $blob2 = "";
                }
                try {
                    $blob3 = new Document($value['T3']);
                    $blob3 = $formatter->Format($blob3);
                } catch (Throwable $th) {
                    $blob3 = "";
                }
                try {
                    $blob4 = new Document($value['T4']);
                    $blob4 = $formatter->Format($blob4);
                } catch (Throwable $th) {
                    $blob4 = "";
                }
                $tiers[] = [
                    'tiers' => $value['tiers'],
                    'titre' => $value['titre'],
                    'nom' => $value['nom'],
                    'rue' => $value['rue'],
                    'Compl1' => $value['Compl1'],
                    'Compl2' => $value['Compl2'],
                    'CodePostal' => $value['CodePostal'],
                    'Ville' => $value['Ville'],
                    'Pays' => $value['Pays'],
                    'tel' => $value['tel'],
                    'fax' => $value['fax'],
                    'Email' => $value['Email'],
                    'mail2' => $value['mail2'],
                    'SiteWeb' => $value['SiteWeb'],
                    'Siret' => $value['Siret'],
                    'ConditionPaiement' => $value['ConditionPaiement'],
                    'Etiquettes' => $value['Etiquettes'],
                    'Stat2' => $value['Stat2'],
                    'Stat3' => $value['Stat3'],
                    'Representant' => $value['Representant'],
                    'LimiteDeCredit' => $value['LimiteDeCredit'],
                    'Naf' => $value['Naf'],
                    'LimiteDeCredit' => $value['LimiteDeCredit'],
                    'Note' => $blob,
                    'FichiersJoints' => $value['FichiersJoints'],
                    'Intra' => $value['Intra'],
                    'avertissement' => $value['avertissement'],
                    'Feu' => $value['Feu'],
                    'ModPort' => $value['ModPort'],
                    'blob2' => $blob2,
                    'blob3' => $blob3,
                    'blob4' => $blob4,
                ];
            }
            $datasContactsAdresses = $repo->getTiersContactsAdresses($dos, $typeTiers);
            $datasbanque = $repo->getBanqueTiers($dos, $typeTiers);
        }

        return $this->render('tiers_fiche_contact_adresse/index.html.twig', [
            'title' => 'Tiers Fiches Adresses, Contacts et Blobs',
            'datas' => $tiers,
            'contacts' => $datasContactsAdresses,
            'banques' => $datasbanque,
            'form' => $form->createView(),
        ]);
    }
}
