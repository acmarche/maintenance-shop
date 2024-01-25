<?php

namespace AcMarche\MaintenanceShop\Controller;

use AcMarche\MaintenanceShop\Entity\Produit;
use AcMarche\MaintenanceShop\Form\ProduitType;
use AcMarche\MaintenanceShop\Form\Search\SearchProduitType;
use AcMarche\MaintenanceShop\Repository\CommandeProduitRepository;
use AcMarche\MaintenanceShop\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/produit')]
#[IsGranted('ROLE_MAINTENANCE_ADMIN')]
class ProduitController extends AbstractController
{
    public function __construct(
        private ProduitRepository $produitRepository,
        private CommandeProduitRepository $commandeProduitRepository
    ) {
    }

    #[Route(path: '/', name: 'acmaintenance_produit', methods: ['GET', 'POST'])]
    public function index(Request $request): RedirectResponse|Response
    {
        $form = $this->createForm(SearchProduitType::class);
        $produits = [];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $produits = $this->produitRepository->search($data);
        }

        $response = new Response(null, $form->isSubmitted() ? Response::HTTP_ACCEPTED : Response::HTTP_OK);

        return $this->render(
            '@AcMarcheMaintenanceShop/produit/index.html.twig',
            [
                'search_form' => $form->createView(),
                'search' => $form->isSubmitted(),
                'produits' => $produits,
            ]
            , $response
        );
    }

    #[Route(path: '/new', name: 'acmaintenance_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request): RedirectResponse|Response
    {
        $entity = new Produit();
        $form = $this->createForm(ProduitType::class, $entity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->produitRepository->persist($entity);
            $this->produitRepository->flush();

            $this->addFlash('success', 'Le produit a bien été ajouté.');

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

    #[Route(path: '/{id}', name: 'acmaintenance_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $commandesProduit = $this->commandeProduitRepository->findBy(['produit' => $produit]);
            foreach ($commandesProduit as $commandeProduit) {
                $this->commandeProduitRepository->remove($commandeProduit);
            }

            $this->produitRepository->remove($produit);
            $this->produitRepository->flush();
            $this->addFlash('success', 'Le produit a bien été supprimé.');
        }

        return $this->redirectToRoute('acmaintenance_produit');
    }
}
