<?php

namespace App\Controller;

use App\Form\StatesDateFilterType;
use App\Repository\Divalto\MouvRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 */

class RseController extends AbstractController
{
    /**
     * @Route("/Lhermitte/rse/{dos}", name="app_rse")
     * * @Route("/Roby/rse/{dos}", name="app_rse")
     */
    public function index($dos, MouvRepository $repoMouv, Request $request): Response
    {
        $depClis = '';
        $gloClis = '';
        $depFous = '';
        $gloFous = '';
        $dd = date("Y-m-d", strtotime("-1 year"));
        $df = date("Y-m-d");

        $form = $this->createForm(StatesDateFilterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dd = $form->getData()['startDate']->format('Y-m-d');
            $df = $form->getData()['endDate']->format('Y-m-d');
        }
        $depClis = $repoMouv->getRseCartographieAchatVente($dd, $df, 'DEPARTEMENT', 'CLI', $dos);
        $gloClis = $repoMouv->getRseCartographieAchatVente($dd, $df, 'GLOBAL', 'CLI', $dos);
        $depFous = $repoMouv->getRseCartographieAchatVente($dd, $df, 'DEPARTEMENT', 'FOU', $dos);
        $gloFous = $repoMouv->getRseCartographieAchatVente($dd, $df, 'GLOBAL', 'FOU', $dos);
        $paillageEngraisGloFous = $repoMouv->getRseFamilleArticleTvaAchatVente($dd, $df, 'GLOBAL', 'FOU', "'PAILLAGE','ENGRAIS'", $dos);
        $paillageEngraisGloClis = $repoMouv->getRseFamilleArticleTvaAchatVente($dd, $df, 'GLOBAL', 'CLI', "'PAILLAGE','ENGRAIS'", $dos);
        $paillageEngraisFamFous = $repoMouv->getRseFamilleArticleTvaAchatVente($dd, $df, 'FAMILLE', 'FOU', "'PAILLAGE','ENGRAIS'", $dos);
        $paillageEngraisFamClis = $repoMouv->getRseFamilleArticleTvaAchatVente($dd, $df, 'FAMILLE', 'CLI', "'PAILLAGE','ENGRAIS'", $dos);
        $phytoBiocontroleGloFous = $repoMouv->getRseFamilleArticleTvaAchatVente($dd, $df, 'GLOBAL', 'FOU', "'PHYTO','BIOCONTR'", $dos);
        $phytoBiocontroleGloClis = $repoMouv->getRseFamilleArticleTvaAchatVente($dd, $df, 'GLOBAL', 'CLI', "'PHYTO','BIOCONTR'", $dos);
        $phytoBiocontroleFamFous = $repoMouv->getRseFamilleArticleTvaAchatVente($dd, $df, 'FAMILLE', 'FOU', "'PHYTO','BIOCONTR'", $dos);
        $phytoBiocontroleFamClis = $repoMouv->getRseFamilleArticleTvaAchatVente($dd, $df, 'FAMILLE', 'CLI', "'PHYTO','BIOCONTR'", $dos);

        return $this->render('rse/index.html.twig', [
            'title' => 'States RSE',
            'depClis' => $depClis,
            'gloClis' => $gloClis,
            'depFous' => $depFous,
            'gloFous' => $gloFous,
            'paillageEngraisGloClis' => $paillageEngraisGloClis,
            'paillageEngraisGloFous' => $paillageEngraisGloFous,
            'paillageEngraisFamClis' => $paillageEngraisFamClis,
            'paillageEngraisFamFous' => $paillageEngraisFamFous,
            'phytoBiocontroleGloClis' => $phytoBiocontroleGloClis,
            'phytoBiocontroleGloFous' => $phytoBiocontroleGloFous,
            'phytoBiocontroleFamClis' => $phytoBiocontroleFamClis,
            'phytoBiocontroleFamFous' => $phytoBiocontroleFamFous,
            'form' => $form->createView(),
            'dd' => $dd,
            'df' => $df,
        ]);
    }
}
