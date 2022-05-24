<?php

namespace AcMarche\MaintenanceShop\Controller;

use AcMarche\MaintenanceShop\Entity\Produit;
use AcMarche\MaintenanceShop\Form\ProduitType;
use AcMarche\MaintenanceShop\Form\Search\SearchProduitType;
use AcMarche\MaintenanceShop\Repository\ProduitRepository;
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
    public function __construct(private ProduitRepository $produitRepository)
    {
    }

    /**
     * Lists all Produit entities.
     */
    #[Route(path: '/', name: 'acmaintenance_produit', methods: ['GET', 'POST'])]
    public function index(Request $request): RedirectResponse|Response
    {
        $search_form = $this->createForm(SearchProduitType::class);
        $produits = [];
        $search_form->handleRequest($request);
        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $data = $search_form->getData();
            $produits = $this->produitRepository->search($data);
        }

        return $this->render(
            '@AcMarcheMaintenanceShop/produit/index.html.twig',
            [
                'search_form' => $search_form->createView(),
                'search' => $search_form->isSubmitted(),
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

            $this->produitRepository->persist($entity);
            $this->produitRepository->flush();

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

            $this->produitRepository->flush();
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
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $commandesProduit = $this->produitRepository->findBy(['produit' => $produit]);
            foreach ($commandesProduit as $commandeProduit) {
                $this->produitRepository->remove($commandeProduit);
            }

            $this->produitRepository->remove($produit);
            $this->produitRepository->flush();
            $this->addFlash('success', 'Le produit a bien été supprimé.');
        }

        return $this->redirectToRoute('acmaintenance_produit');
    }
}
