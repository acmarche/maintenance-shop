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
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommandeController.
 */
#[Route(path: '/commande')]
class CommandeController extends AbstractController
{
    public function __construct(private ProduitRepository $produitRepository, private CommandeRepository $commandeRepository, private CommandeProduitRepository $commandeProduitRepository, private ManagerRegistry $managerRegistry)
    {
    }

    #[Route(path: '/', name: 'acmaintenance_commande', methods: ['GET'])]
    public function index(Request $request): RedirectResponse|Response
    {
        $session = $request->getSession();
        $search = false;
        $data = [];
        $key = 'maintenance_shop_commande';
        if ($session->has($key)) {
            $data = unserialize($session->get($key));
        }
        $search_form = $this->createForm(
            SearchProduitType::class,
            $data,
            [
                'action' => $this->generateUrl('acmaintenance_commande'),
                'method' => 'GET',
            ]
        );
        //to delete ?
        $form = $this->createForm(CommandeType::class);
        $produits = [];
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
            [
                'search' => $search,
                'search_form' => $search_form->createView(),
                'form' => $form->createView(),
                'produits' => $produits,
            ]
        );
    }

    /**
     * Finds and displays a Produit entity.
     */
    #[Route(path: '/{id}', name: 'acmaintenance_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render(
            '@AcMarcheMaintenanceShop/commande/show.html.twig',
            [
                'commande' => $commande,
            ]
        );
    }

    /**
     * Finds and displays a Produit entity.
     */
    #[Route(path: '/product/{id}', name: 'acmaintenance_commande_show_produit', methods: ['GET', 'POST'])]
    public function showProduit(Request $request, Produit $produit): RedirectResponse|Response
    {
        $data = ['quantite' => 1];
        $form = $this->createForm(CommandeSingleType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $quantite = $form->get('quantite')->getData();
            $commandeProduit = $this->traitement($produit, $quantite);
            $this->addFlash('success', $commandeProduit->getQuantite().' dans le panier');

            return $this->redirectToRoute('acmaintenance_commande_show_produit', ['id' => $produit->getId()]);
        }

        return $this->render(
            '@AcMarcheMaintenanceShop/commande/show_produit.html.twig',
            [
                'produit' => $produit,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/add', name: 'acmaintenance_commande_add', methods: ['POST'])]
    public function addProduit(Request $request): Response
    {
        $produitId = $request->get('produit');
        $quantite = $request->get('quantite');
        if (!$produitId || !$quantite) {
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
        $em = $this->managerRegistry->getManager();
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
        if (null !== $commandeProduit) {
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
