<?php

namespace App\EventListener;

use App\Entity\Main\Trackings;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class TrackingsListener
{
    private $entityManager;
    private $security;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        // Vérifiez si l'utilisateur est identifié
        if ($this->security->getUser()) {
            // Récupérez le chemin de la page
            $page = $request->getPathInfo();

            // Vérifiez si $page n'est pas nul et n'est pas vide
            if ($page !== null && $page !== '') {
                // Créez une instance de Tracking et enregistrez-la dans la base de données
                $tracking = new Trackings();
                $tracking->setUser($this->security->getUser());
                $tracking->setPage($page);
                $tracking->setCreatedAt(new \DateTime());

                $this->entityManager->persist($tracking);
                $this->entityManager->flush();
            }
        }
    }
}
