<?php

namespace AcMarche\MaintenanceShop\Controller;

use AcMarche\MaintenanceShop\Entity\Commande;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ArchiveController.
 */
#[Route(path: '/archive')]
class ArchiveController extends AbstractController
{
    public function __construct(private ManagerRegistry $managerRegistry)
    {
    }

    #[Route(path: '/', name: 'acmaintenance_commande_archive', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $em = $this->managerRegistry->getManager();
        $data = ['envoye' => true];
        $commandes = $em->getRepository(Commande::class)->findBy($data);

        return $this->render(
            '@AcMarcheMaintenanceShop/archive/index.html.twig',
            [
                'commandes' => $commandes,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'acmaintenance_commande_archive_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render(
            '@AcMarcheMaintenanceShop/archive/show.html.twig',
            [
                'commande' => $commande,
            ]
        );
    }
}
