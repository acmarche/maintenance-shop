<?php

namespace AcMarche\MaintenanceShop\Controller;

use AcMarche\MaintenanceShop\Entity\CommandeProduit;
use AcMarche\MaintenanceShop\Entity\Produit;
use AcMarche\MaintenanceShop\Form\ProduitType;
use AcMarche\MaintenanceShop\Form\Search\SearchProduitType;
use AcMarche\MaintenanceShop\Repository\ProduitRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Produit controller.
 *
 * @Route("/produit")
 * @IsGranted("ROLE_MAINTENANCE_ADMIN")
 */
class ProduitController extends AbstractController
{
    /**
     * @var ProduitRepository
     */
    private $produitRepository;

    public function __construct(ProduitRepository $produitRepository)
    {
        $this->produitRepository = $produitRepository;
    }

    /**
     * Lists all Produit entities.
     *
     * @Route("/", name="acmaintenance_produit", methods={"GET"})
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $search = false;
        $data = array();
        $key = "maintenance_shop_search";

        if ($session->has($key)) {
            $data = unserialize($session->get($key));
        }

        $search_form = $this->createForm(
            SearchProduitType::class,
            $data,
            array(
                'action' => $this->generateUrl('acmaintenance_produit'),
                'method' => 'GET',
            )
        );

        $produits = array();
        $search_form->handleRequest($request);

        if ($search_form->isSubmitted() && $search_form->isValid()) {
            if ($search_form->get('raz')->isClicked()) {
                $session->remove($key);
                $this->addFlash('info', 'La recherche a bien été réinitialisée.');

                return $this->redirectToRoute('acmaintenance_produit');
            }

            $data = $search_form->getData();
            $session->set($key, serialize($data));
            $search = true;
            $produits = $em->getRepository(Produit::class)->search($data);
        }

        return $this->render(
            '@AcMarcheMaintenanceShop/produit/index.html.twig',
            array(
                'search' => $search,
                'search_form' => $search_form->createView(),
                'produits' => $produits,
            )
        );
    }

    /**
     * Displays a form to create a new Produit produit.
     *
     * @Route("/new", name="acmaintenance_produit_new", methods={"GET","POST"})
     */
    public function new(Request $request)
    {
        $entity = new Produit();
        $form = $this->createForm(ProduitType::class, $entity)
            ->add('saveAndCreateNew', SubmitType::class)
            ->add('Create', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($entity);
            $em->flush();

            $this->addFlash('success', 'Le produit a bien été ajouté.');

            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('acmaintenance_produit_new');
            }

            return $this->redirectToRoute('acmaintenance_produit_show', array('id' => $entity->getId()));
        }

        return $this->render(
            '@AcMarcheMaintenanceShop/produit/new.html.twig',
            array(
                'produit' => $entity,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Produit produit.
     *
     * @Route("/{id}", name="acmaintenance_produit_show", methods={"GET"})
     */
    public function show(Produit $produit)
    {
        return $this->render(
            '@AcMarcheMaintenanceShop/produit/show.html.twig',
            array(
                'produit' => $produit,
            )
        );
    }

    /**
     * Displays a form to edit an existing Produit produit.
     *
     * @Route("/{id}/edit", name="acmaintenance_produit_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Produit $produit)
    {
        $editForm = $this->createForm(ProduitType::class, $produit);
        $editForm->add('Update', SubmitType::class);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'Le produit a bien été mise à jour.');

            return $this->redirectToRoute('acmaintenance_produit_show', array('id' => $produit->getId()));
        }

        return $this->render(
            '@AcMarcheMaintenanceShop/produit/edit.html.twig',
            array(
                'produit' => $produit,
                'edit_form' => $editForm->createView(),
            )
        );
    }

    /**
     * Deletes a Produit produit.
     *
     * @Route("/{id}", name="acmaintenance_produit_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Produit $produit)
    {
        $em = $this->getDoctrine()->getManager();

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
