<?php

namespace AcMarche\MaintenanceShop\Controller;

use AcMarche\MaintenanceShop\Entity\Commande;
use AcMarche\MaintenanceShop\Entity\CommandeProduit;
use AcMarche\MaintenanceShop\Entity\Produit;
use AcMarche\MaintenanceShop\Form\CommandeSingleType;
use AcMarche\MaintenanceShop\Form\CommandeType;
use AcMarche\MaintenanceShop\Form\Search\SearchProduitType;
use AcMarche\MaintenanceShop\Repository\CommandeProduitRepository;
use AcMarche\MaintenanceShop\Repository\CommandeRepository;
use AcMarche\MaintenanceShop\Repository\ProduitRepository;
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
     * @var CommandeRepository
     */
    private $commandeRepository;
    /**
     * @var CommandeProduitRepository
     */
    private $commandeProduitRepository;
    /**
     * @var ProduitRepository
     */
    private $produitRepository;

    public function __construct(
        ProduitRepository $produitRepository,
        CommandeRepository $commandeRepository,
        CommandeProduitRepository $commandeProduitRepository
    ) {
        $this->commandeRepository = $commandeRepository;
        $this->commandeProduitRepository = $commandeProduitRepository;
        $this->produitRepository = $produitRepository;
    }

    /**
     * @Route("/", name="acmaintenance_commande", methods={"GET"})
     */
    public function index(Request $request)
    {
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

        $produits = array();
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
            $produits = $this->produitRepository->search($data);
        }

        return $this->render(
            '@AcMarcheMaintenanceShop/commande/index.html.twig',
            array(
                'search' => $search,
                'search_form' => $search_form->createView(),
                'form' => $form->createView(),
                'produits' => $produits,
            )
        );
    }

    /**
     * Finds and displays a Produit entity.
     *
     * @Route("/{id}", name="acmaintenance_commande_show", methods={"GET"})
     */
    public function show(Commande $commande)
    {
        return $this->render(
            '@AcMarcheMaintenanceShop/commande/show.html.twig',
            array(
                'commande' => $commande,
            )
        );
    }

    /**
     * Finds and displays a Produit entity.
     *
     * @Route("/product/{id}", name="acmaintenance_commande_show_produit", methods={"GET","POST"})
     */
    public function showProduit(Request $request, Produit $produit)
    {
        $data = ['quantite' => 1];
        $form = $this->createForm(CommandeSingleType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quantite = $form->get('quantite')->getData();
            $commandeProduit = $this->traitement($produit, $quantite);
            $this->addFlash('success', $commandeProduit->getQuantite().' dans le panier');

            return $this->redirectToRoute('acmaintenance_commande_show_produit', array('id' => $produit->getId()));
        }

        return $this->render(
            '@AcMarcheMaintenanceShop/commande/show_produit.html.twig',
            array(
                'produit' => $produit,
                'form' => $form->createView(),
            )
        );
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

        $commandeProduit = $this->traitement($produit, $quantite);

        // return JsonResponse::create("cou");
        return $this->render(
            '@AcMarcheMaintenanceShop/commande/result.html.twig',
            [
                'class' => 'success',
                'result' => $commandeProduit->getQuantite().' dans le panier',
            ]
        );
    }

    private function traitement(Produit $produit, int $quantite): CommandeProduit
    {
        $commande = $this->commandeRepository->getCommandeActive();

        if (!$commande) {
            $commande = new Commande();
            $this->commandeRepository->persist($commande);
            $this->commandeRepository->flush();
        }

        $args = ['commande' => $commande, 'produit' => $produit];
        $commandeProduit = $this->commandeProduitRepository->findOneBy($args);
        if ($commandeProduit) {
            $quantiteDansPanier = $commandeProduit->getQuantite();
            $commandeProduit->setQuantite($quantite + $quantiteDansPanier);
        } else {
            $commandeProduit = new CommandeProduit();
            $commandeProduit->setCommande($commande);
            $commandeProduit->setProduit($produit);
            $commandeProduit->setQuantite($quantite);
        }

        $this->commandeProduitRepository->persist($commandeProduit);
        $this->commandeProduitRepository->flush();

        return $commandeProduit;
    }


}
