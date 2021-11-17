<?php

namespace App\Controller;

use App\Repository\Divalto\EntRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 */

class OldCmdController extends AbstractController
{
    /**
     * @Route("/old/cmd", name="app_old_cmd")
     */
    public function show(EntRepository $repo, Request $request): Response
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
        $oldCmds = $repo->getOldCmds($dos);
        
        return $this->render('old_cmd/index.html.twig', [
            'controller_name' => 'OldCmdController',
            'oldCmds' => $oldCmds,
            'title' => 'Vieilles Commandes actives'
        ]);
    }
}
