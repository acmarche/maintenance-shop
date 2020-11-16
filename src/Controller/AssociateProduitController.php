<?php

namespace AcMarche\MaintenanceShop\Controller;

use AcMarche\MaintenanceShop\Entity\Produit;
use AcMarche\MaintenanceShop\Form\ProduitAssociateType;
use AcMarche\MaintenanceShop\Repository\ProduitRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Produit controller.
 *
 * @Route("/associate/produit")
 * @IsGranted("ROLE_MAINTENANCE_ADMIN")
 */
class AssociateProduitController extends AbstractController
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
     * Displays a form to edit an existing Produit produit.
     *
     * @Route("/{id}/edit", name="acmaintenance_produit_associate", methods={"GET","POST"})
     */
    public function edit(Request $request, Produit $produit)
    {
        $editForm = $this->createForm(ProduitAssociateType::class, $produit);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $productSelected = $request->request->get('produits-choice');
            $product = $this->produitRepository->findOneBy(['nom' => $productSelected]);

            if (!$product) {
                $this->addFlash('danger', 'Le produit n\'a pas été trouvé dans la liste');
            } else {
                $produit->addAssociatedProduct($product);
                $this->produitRepository->flush();
                $this->addFlash('success', 'Le produit a bien été associé.');
            }

            return $this->redirectToRoute('acmaintenance_produit_show', array('id' => $produit->getId()));
        }

        $produits = $this->produitRepository->findAll();

        return $this->render(
            '@AcMarcheMaintenanceShop/produit_associate/edit.html.twig',
            array(
                'produit' => $produit,
                'produits' => $produits,
                'form' => $editForm->createView(),
            )
        );
    }

    /**
     * @Route("/", name="acmaintenance_produit_dissociate", methods={"DELETE"})
     */
    public function delete(Request $request): Response
    {
        $produitAssociatedId = $request->request->get('associateid');
        $produitId = $request->request->get('produitid');

        $produit = $this->produitRepository->find($produitId);

        if (!$produitAssociatedId) {
            $this->addFlash('danger', 'Produit non trouvé');

            return $this->redirectToRoute('acmaintenance_produit_associate', ['id' => $produit->getId()]);
        }

        $produitAssociated = $this->produitRepository->find($produitAssociatedId);
        if (null === $produitAssociated) {
            $this->addFlash('danger', 'Relation non trouvée');

            return $this->redirectToRoute('acmaintenance_produit_associate', ['id' => $produit->getId()]);
        }

        if ($this->isCsrfTokenValid('delete'.$produitAssociated->getId(), $request->request->get('_token'))) {
            $produit->removeAssociatedProduct($produitAssociated);
            $this->produitRepository->flush();
            $this->addFlash('success', 'Le produit a été dissocié');
        }

        return $this->redirectToRoute('acmaintenance_produit_associate', ['id' => $produit->getId()]);
    }


}
