<?php

namespace AcMarche\MaintenanceShop\Controller;

use AcMarche\MaintenanceShop\Entity\Categorie;
use AcMarche\MaintenanceShop\Entity\Produit;
use AcMarche\MaintenanceShop\Form\CategorieType;
use AcMarche\MaintenanceShop\Repository\CategorieRepository;
use AcMarche\MaintenanceShop\Repository\ProduitRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Categorie controller.
 */
#[Route(path: '/categorie')]
#[IsGranted(data: 'ROLE_MAINTENANCE_ADMIN')]
class CategorieController extends AbstractController
{
    public function __construct(
        private CategorieRepository $categorieRepository,
        private ProduitRepository $produitRepository,
        private ManagerRegistry $managerRegistry
    ) {
    }

    /**
     * Lists all Categorie categories.
     */
    #[Route(path: '/', name: 'acmaintenance_categorie', methods: ['GET'])]
    public function index(): Response
    {
        $categories = $this->categorieRepository->findAll();

        return $this->render(
            '@AcMarcheMaintenanceShop/categorie/index.html.twig',
            [
                'categories' => $categories,
            ]
        );
    }

    /**
     * Displays a form to create a new Categorie categorie.
     */
    #[Route(path: '/new', name: 'acmaintenance_categorie_new', methods: ['GET', 'POST'])]
    public function new(Request $request): RedirectResponse|Response
    {
        $entity = new Categorie();
        $form = $this->createForm(CategorieType::class, $entity)
            ->add('Create', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();

            $em->persist($entity);
            $em->flush();

            $this->addFlash('success', 'La catégorie a bien été crée.');

            return $this->redirectToRoute('acmaintenance_categorie_show', ['id' => $entity->getId()]);
        }

        return $this->render(
            '@AcMarcheMaintenanceShop/categorie/new.html.twig',
            [
                'categorie' => $entity,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Categorie categorie.
     */
    #[Route(path: '/{id}', name: 'acmaintenance_categorie_show', methods: ['GET'])]
    public function show(Categorie $categorie): Response
    {
        $produits = $this->produitRepository->findByCategorie($categorie);

        return $this->render(
            '@AcMarcheMaintenanceShop/categorie/show.html.twig',
            [
                'categorie' => $categorie,
                'produits' => $produits,
            ]
        );
    }

    /**
     * Displays a form to edit an existing Categorie categorie.
     */
    #[Route(path: '/{id}/edit', name: 'acmaintenance_categorie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categorie $categorie): RedirectResponse|Response
    {
        $editForm = $this->createForm(CategorieType::class, $categorie)
            ->add('Update', SubmitType::class);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->managerRegistry->getManager();
            $em->flush();
            $this->addFlash('success', 'La catégorie a bien été mise à jour.');

            return $this->redirectToRoute('acmaintenance_categorie_show', ['id' => $categorie->getId()]);
        }

        return $this->render(
            '@AcMarcheMaintenanceShop/categorie/edit.html.twig',
            [
                'categorie' => $categorie,
                'edit_form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Deletes a Categorie categorie.
     */
    #[Route(path: '/{id}', name: 'acmaintenance_categorie_delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->request->get('_token'))) {
            $this->categorieRepository->remove($categorie);
            $this->categorieRepository->flush();
            $this->addFlash('success', 'La catégorie a bien été supprimée');
        }

        return $this->redirectToRoute('acmaintenance_categorie');
    }

    /**
     * Trier les news.
     */
    #[Route(path: '/trier/{id}', name: 'acmaintenance_categorie_trier', methods: ['GET', 'POST'])]
    public function trier(Request $request, Categorie $categorie): Response
    {
        $em = $this->managerRegistry->getManager();
        $isAjax = $request->isXmlHttpRequest();
        if ($isAjax) {
            $produits = $request->request->get('produits');
            if (\is_array($produits)) {
                foreach ($produits as $position => $produitId) {
                    $produit = $em->getRepository(Produit::class)->find($produitId);
                    if ($produit) {
                        $produit->setPosition($position);
                        $em->persist($produit);
                    }
                }
                $em->flush();

                return new Response('<div class="alert alert-success">Tri enregistré</div>');
            }

            return new Response('<div class="alert alert-error">Erreur</div>');
        }

        return $this->render(
            '@AcMarcheMaintenanceShop/categorie/trier.html.twig',
            ['categorie' => $categorie]
        );
    }

    #[Route(path: '/q/sort/', name: 'acmaintenance_categorie_ajax_sort', methods: ['POST'])]
    public function trierProduit(Request $request): JsonResponse
    {
        //    $isAjax = $request->isXmlHttpRequest();
        //    if ($isAjax) {
        //
        $data = json_decode($request->getContent(), null, 512, JSON_THROW_ON_ERROR);
        $produits = $data->produits;
        foreach ($produits as $position => $produitId) {
            $produit = $this->produitRepository->find($produitId);
            if (null !== $produit) {
                $produit->setPosition($position);
            }
        }
        $this->produitRepository->flush();

        return $this->json('<div class="alert alert-success">Tri enregistré</div>');
    }
}
