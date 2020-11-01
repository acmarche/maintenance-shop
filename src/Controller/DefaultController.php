<?php

namespace AcMarche\MaintenanceShop\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="acmaintenance", methods={"GET"})
     */
    public function index()
    {
        return $this->render('@AcMarcheMaintenanceShop/default/index.html.twig');
    }

    /**
     * @Route("/bug", name="acmaintenance_bug", methods={"GET"})
     */
    public function bug()
    {
        $propoer = null;
        $propoer->findAll();
        return $this->render('@AcMarcheMaintenanceShop/default/index.html.twig');
    }
}
