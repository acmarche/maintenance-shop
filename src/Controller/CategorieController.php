<?php

namespace AcMarche\MaintenanceShop\Controller;

use AcMarche\MaintenanceShop\Entity\Categorie;
use AcMarche\MaintenanceShop\Entity\Produit;
use AcMarche\MaintenanceShop\Form\CategorieType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Categorie controller.
 *
 * @Route("/categorie")
 * @Security("has_role('ROLE_MAINTENANCE_ADMIN')")
 */
class CategorieController extends AbstractController
{

    /**
     * Lists all Categorie categories.
     *
     * @Route("/", name="acmaintenance_categorie", methods={"GET"})
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository(Categorie::class)->findAll();

        return $this->render(
            '@AcMarcheMaintenanceShop/categorie/index.html.twig',
            array(
                'categories' => $entities,
            )
        );
    }

    /**
     * Displays a form to create a new Categorie categorie.
     *
     * @Route("/new", name="acmaintenance_categorie_new", methods={"GET","POST"})
     */
    public function new(Request $request)
    {
        $entity = new Categorie();
        $form = $this->createForm(CategorieType::class, $entity)
            ->add('Create', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($entity);
            $em->flush();

            $this->addFlash('success', 'La catégorie a bien été crée.');

            return $this->redirectToRoute('acmaintenance_categorie_show', array('id' => $entity->getId()));
        }

        return $this->render(
            '@AcMarcheMaintenanceShop/categorie/new.html.twig',
            array(
                'categorie' => $entity,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Categorie categorie.
     *
     * @Route("/{id}", name="acmaintenance_categorie_show", methods={"GET"})
     */
    public function show(Categorie $categorie)
    {
        $deleteForm = $this->createDeleteForm($categorie->getId());

        return $this->render(
            '@AcMarcheMaintenanceShop/categorie/show.html.twig',
            array(
                'categorie' => $categorie,
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Categorie categorie.
     *
     * @Route("/{id}/edit", name="acmaintenance_categorie_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Categorie $categorie)
    {
        $editForm = $this->createForm(CategorieType::class, $categorie, ['method' => 'PUT'])
            ->add('Update', SubmitType::class);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'La catégorie a bien été mise à jour.');

            return $this->redirectToRoute('acmaintenance_categorie_show', array('id' => $categorie->getId()));
        }

        return $this->render(
            '@AcMarcheMaintenanceShop/categorie/edit.html.twig',
            array(
                'categorie' => $categorie,
                'edit_form' => $editForm->createView(),
            )
        );
    }

    /**
     * Deletes a Categorie categorie.
     *
     * @Route("/{id}", name="acmaintenance_categorie_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Categorie $categorie)
    {
        $form = $this->createDeleteForm($categorie->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($categorie);
            $em->flush();

            $this->addFlash('success', 'La catégorie a bien été supprimée.');
        }

        return $this->redirectToRoute('acmaintenance_categorie');
    }

    /**
     * Creates a form to delete a Categorie categorie by id.
     *
     * @param mixed $id The categorie id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('acmaintenance_categorie_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add(
                'submit',
                SubmitType::class,
                array(
                    'label' => 'Delete',
                    'attr' => array('class' => 'btn-danger'),
                )
            )
            ->getForm();
    }

    /**
     * Trier les news
     *
     * @Route("/trier/{id}", name="acmaintenance_categorie_trier", methods={"GET","POST"})
     */
    public function trier(Request $request, Categorie $categorie)
    {
        $em = $this->getDoctrine()->getManager();
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax) {
            $produits = $request->request->get("produits");
            if (is_array($produits)) {
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
            array('categorie' => $categorie)
        );
    }

}