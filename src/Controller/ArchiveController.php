<?php

namespace AcMarche\MaintenanceShop\Controller;

use AcMarche\MaintenanceShop\Entity\Commande;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ArchiveController
 * @Route("/archive")
 */
class ArchiveController extends AbstractController
{
    /**
     * @Route("/", name="acmaintenance_commande_archive", methods={"GET"})
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = ['envoye' => true];
        $commandes = $em->getRepository(Commande::class)->findBy($data);


        return $this->render(
            '@AcMarcheMaintenanceShop/archive/index.html.twig',
            array(
                'commandes' => $commandes,
            )
        );
    }

    /**
     * @Route("/{id}", name="acmaintenance_commande_archive_show", methods={"GET"})
     */
    public function show(Commande $commande)
    {
        return $this->render(
            '@AcMarcheMaintenanceShop/archive/show.html.twig',
            array(
                'commande' => $commande,
            )
        );
    }


}
