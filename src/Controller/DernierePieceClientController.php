<?php

namespace App\Controller;

use App\Repository\Divalto\MouvRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 */

class DernierePieceClientController extends AbstractController
{
    /**
     * @Route("/dernieres/pieces/client", name="app_dernieres_pieces_ par_client")
     */
    public function index(MouvRepository $repo): Response
    {
        $roby = false;
        $lhermitte = false;
        $dos = '';
        $roles = $this->getUser()->getRoles();
        
        foreach ($roles as $role) {
            if ( strstr($role, 'ROBY') ) {
                $roby = true;
                $dos = '\'3\'';
            }
            if ( strstr($role, 'LHERMITTE') ) {
                $lhermitte = true;
                $dos = '\'1\'';
            }
        }
        if ($lhermitte == true && $roby == true ) {
            $dos = '\'1\',\'3\'';
        }
        
        return $this->render('derniere_piece_client/index.html.twig', [
            'controller_name' => 'DernierePieceClientController',
            'lastOrders' => $repo->getLastMouvCli($dos),
            'title' => 'Derniéres piéces par client'
        ]);
    }
}
