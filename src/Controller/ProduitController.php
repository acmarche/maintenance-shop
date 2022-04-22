<?php

namespace AcMarche\MaintenanceShop\Controller;

use AcMarche\MaintenanceShop\Entity\CommandeProduit;
use AcMarche\MaintenanceShop\Entity\Produit;
use AcMarche\MaintenanceShop\Form\ProduitType;
use AcMarche\MaintenanceShop\Form\Search\SearchProduitType;
use AcMarche\MaintenanceShop\Repository\ProduitRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Produit controller.
 */
#[Route(path: '/produit')]
#[IsGranted(data: 'ROLE_MAINTENANCE_ADMIN')]
class ProduitController extends AbstractController
{
    public function __construct(private ProduitRepository $produitRepository, private ManagerRegistry $managerRegistry)
    {
    }

    /**
     * Lists all Produit entities.
     */
    #[Route(path: '/', name: 'acmaintenance_produit', methods: ['GET'])]
    public function index(Request $request): RedirectResponse|Response
    {
        $em = $this->managerRegistry->getManager();
        $session = $request->getSession();

        $data = [];
        $key = 'maintenance_shop_search';
        if ($session->has($key)) {
            $data = unserialize($session->get($key));
        }
        $search_form = $this->createForm(
            SearchProduitType::class,
            $data,
            [
                'action' => $this->generateUrl('acmaintenance_produit'),
                'method' => 'GET',
            ]
        );
        $produits = [];
        $search_form->handleRequest($request);
        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $data = $search_form->getData();
            $session->set($key, serialize($data));
            $produits = $em->getRepository(Produit::class)->search($data);
        }

        return $this->render(
            '@AcMarcheMaintenanceShop/produit/index.html.twig',
            [
                'search' => $search_form->isSubmitted(),
                'search_form' => $search_form->createView(),
                'produits' => $produits,
            ]
        );
    }

    /**
     * Displays a form to create a new Produit produit.
     */
    #[Route(path: '/new', name: 'acmaintenance_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request): RedirectResponse|Response
    {
        $entity = new Produit();
        $form = $this->createForm(ProduitType::class, $entity)
            ->add('saveAndCreateNew', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();

            $em->persist($entity);
            $em->flush();

            $this->addFlash('success', 'Le produit a bien été ajouté.');

            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('acmaintenance_produit_new');
            }

            return $this->redirectToRoute('acmaintenance_produit_show', ['id' => $entity->getId()]);
        }

        return $this->render(
            '@AcMarcheMaintenanceShop/produit/new.html.twig',
            [
                'produit' => $entity,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Produit produit.
     */
    #[Route(path: '/{id}', name: 'acmaintenance_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render(
            '@AcMarcheMaintenanceShop/produit/show.html.twig',
            [
                'produit' => $produit,
            ]
        );
    }

    /**
     * Displays a form to edit an existing Produit produit.
     */
    #[Route(path: '/{id}/edit', name: 'acmaintenance_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit): RedirectResponse|Response
    {
        $editForm = $this->createForm(ProduitType::class, $produit);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->managerRegistry->getManager();
            $em->flush();
            $this->addFlash('success', 'Le produit a bien été mise à jour.');

            return $this->redirectToRoute('acmaintenance_produit_show', ['id' => $produit->getId()]);
        }

        return $this->render(
            '@AcMarcheMaintenanceShop/produit/edit.html.twig',
            [
                'produit' => $produit,
                'form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Deletes a Produit produit.
     */
    #[Route(path: '/{id}', name: 'acmaintenance_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit): RedirectResponse
    {
        $em = $this->managerRegistry->getManager();
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $commandesProduit = $em->getRepository(CommandeProduit::class)->findBy(['produit' => $produit]);
            foreach ($commandesProduit as $commandeProduit) {
                $em->remove($commandeProduit);
            }

            $this->produitRepository->remove($produit);
            $em->flush();
            $this->addFlash('success', 'Le produit a bien été supprimé.');
        }

        return $this->redirectToRoute('acmaintenance_produit');
    }
}
