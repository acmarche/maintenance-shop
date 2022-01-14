<?php

namespace AcMarche\MaintenanceShop\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route(path: '/', name: 'acmaintenance', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('@AcMarcheMaintenanceShop/default/index.html.twig');
    }

    #[Route(path: '/bug', name: 'acmaintenance_bug', methods: ['GET'])]
    public function bug(): Response
    {
        $propoer = null;
        $propoer->findAll();

        return $this->render('@AcMarcheMaintenanceShop/default/index.html.twig');
    }
}
