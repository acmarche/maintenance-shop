<?php

namespace AcMarche\MaintenanceShop\Controller;

use AcMarche\MaintenanceShop\Entity\Commande;
use AcMarche\MaintenanceShop\Entity\CommandeProduit;
use AcMarche\MaintenanceShop\Entity\Produit;
use AcMarche\MaintenanceShop\Form\CommandeType;
use AcMarche\MaintenanceShop\Form\Search\SearchProduitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommandeController
 * @Route("/commande")
 */
class CommandeController extends AbstractController
{
    /**
     * @Route("/", name="acmaintenance_commande", methods={"GET"})
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $search = false;
        $data = array();
        $key = "maintenance_shop_commande";

        if ($session->has($key)) {
            $data = unserialize($session->get($key));
        }

        $search_form = $this->createForm(
            SearchProduitType::class,
            $data,
            array(
                'action' => $this->generateUrl('acmaintenance_commande'),
                'method' => 'GET',
            )
        );

        //to delete ?
        $form = $this->createForm(CommandeType::class);

        $entities = array();
        $search_form->handleRequest($request);

        if ($search_form->isSubmitted() && $search_form->isValid()) {

            if ($search_form->get('raz')->isClicked()) {
                $session->remove($key);
                $this->addFlash('info', 'La recherche a bien été réinitialisée.');

                return $this->redirectToRoute('acmaintenance_commande');
            }

            $data = $search_form->getData();
            $session->set($key, serialize($data));
            $search = true;
            $entities = $em->getRepository(Produit::class)->search($data);
        }

        return $this->render(
            '@AcMarcheMaintenanceShop/commande/index.html.twig',array(
            'search' => $search,
            'search_form' => $search_form->createView(),
            'form' => $form->createView(),
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a Produit entity.
     *
     * @Route("/{id}", name="acmaintenance_commande_show", methods={"GET"})
     */
    public function show(Commande $commande)
    {
        return $this->render(
            '@AcMarcheMaintenanceShop/commande/show.html.twig',array(
            'commande' => $commande,
        ));
    }

    /**
     * @Route("/add", name="acmaintenance_commande_add", methods={"POST"})
     */
    public function addProduit(Request $request)
    {
        $produitId = $request->get('produit');
        $quantite = $request->get('quantite');

        if (!$produitId or !$quantite) {
            return $this->render(
                '@AcMarcheMaintenanceShop/commande/result.html.twig',
                [
                    'class' => 'danger',
                    'result' => 'Référence produit ou quantité non trouvé',
                ]
            );
        }

        if ($quantite < 1) {
            return $this->render(
                '@AcMarcheMaintenanceShop/commande/result.html.twig',
                [
                    'class' => 'danger',
                    'result' => 'La quantité doit être au moins de 1',
                ]
            );
        }

        $em = $this->getDoctrine()->getManager();
        $produit = $em->getRepository(Produit::class)->find($produitId);

        if (!$produit) {
            return $this->render(
                '@AcMarcheMaintenanceShop/commande/result.html.twig',
                [
                    'class' => 'danger',
                    'result' => 'Produit non trouvé',
                ]
            );
        }

        $commande = $em->getRepository(Commande::class)->getCommandeActive();

        if (!$commande) {
            $commande = new Commande();
            $em->persist($commande);
            $em->flush();
        }

        $args = ['commande' => $commande, 'produit' => $produit];
        $commandeProduit = $em->getRepository(CommandeProduit::class)->findOneBy($args);
        if ($commandeProduit) {
            $quantiteDansPanier = $commandeProduit->getQuantite();
            $commandeProduit->setQuantite($quantite + $quantiteDansPanier);
        } else {
            $commandeProduit = new CommandeProduit();
            $commandeProduit->setCommande($commande);
            $commandeProduit->setProduit($produit);
            $commandeProduit->setQuantite($quantite);
        }

        $em->persist($commandeProduit);
        $em->flush();

        // return JsonResponse::create("cou");
        return $this->render(
            '@AcMarcheMaintenanceShop/commande/result.html.twig',
            [
                'class' => 'success',
                'result' => $commandeProduit->getQuantite().' dans le panier',
            ]
        );
    }


}
