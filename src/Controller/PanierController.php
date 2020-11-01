<?php

namespace AcMarche\MaintenanceShop\Controller;

use AcMarche\MaintenanceShop\Entity\Commande;
use AcMarche\MaintenanceShop\Entity\CommandeProduit;
use AcMarche\MaintenanceShop\Form\PanierType;
use AcMarche\MaintenanceShop\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/panier")
 */
class PanierController extends AbstractController
{
    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @Route("/", name="acmaintenance_panier", methods={"GET","POST"})
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $commande = $em->getRepository(Commande::class)->getCommandeActive();
        if (!$commande) {
            $commande = new Commande();
            $em->persist($commande);
            $em->flush();
        }

        $form = $this->createForm(PanierType::class, $commande);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $produits = $commande->getProduits();

            if (count($produits) == 0) {
                return $this->redirectToRoute('acmaintenance_panier');
            }

            try {
                $this->mailer->sendPanier($commande);
                $this->addFlash("success", "La commande a bien été envoyé");
            } catch (TransportExceptionInterface $e) {
                $this->addFlash("error", $e->getMessage());
            }

            $commande->setEnvoye(true);

            $txt = '';
            foreach ($commande->getProduits() as $commandeProduit) {
                $txt .= $commandeProduit->getQuantite().' '.$commandeProduit->getProduit()->getNom().'<br />';
            }
            $commande->setArchivesProduits($txt);

            $em->persist($commande);
            $em->flush();

            $this->addFlash('success', '');

            return $this->redirectToRoute('acmaintenance_panier');
        }

        return $this->render(
            '@AcMarcheMaintenanceShop/panier/index.html.twig',
            [
                'commande' => $commande,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/update", name="acmaintenance_commande_update_quantite", methods={"POST"})
     */
    public function updateQuantite(Request $request)
    {
        $id = $request->get('id');
        $quantite = $request->get('quantite');

        if (!$id or !$quantite) {
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
        $commandeProduit = $em->getRepository(CommandeProduit::class)->find($id);

        if (!$commandeProduit) {
            return $this->render(
                '@AcMarcheMaintenanceShop/commande/result.html.twig',
                [
                    'class' => 'danger',
                    'result' => 'Produit non trouvé',
                ]
            );
        }

        if ($quantite == $commandeProduit->getQuantite()) {
            return Response::create('');
        }

        $commandeProduit->setQuantite($quantite);
        $em->persist($commandeProduit);
        $em->flush();

        return $this->render(
            '@AcMarcheMaintenanceShop/commande/result.html.twig',
            [
                'class' => 'success',
                'result' => 'La quantitée a bien été changée ('.$commandeProduit->getQuantite().')',
            ]
        );
    }

    /**
     * @Route("/delete", name="acmaintenance_commande_delete", methods={"POST"})
     */
    public function deleteProduit(Request $request)
    {
        $id = $request->get('id');
        $result = [];

        if (!$id) {
            $result['status'] = 'error';
            $txt = $this->renderView(
                '@AcMarcheMaintenanceShop/commande/result.html.twig',
                [
                    'class' => 'danger',
                    'result' => 'Référence produit non trouvée',
                ]
            );
            $result['message'] = $txt;

            return JsonResponse::create($result);
        }

        $em = $this->getDoctrine()->getManager();
        $commandeProduit = $em->getRepository(CommandeProduit::class)->find($id);

        if (!$commandeProduit) {
            $result['status'] = 'error';

            $result['message'] = $this->renderView(
                '@AcMarcheMaintenanceShop/commande/result.html.twig',
                [
                    'class' => 'danger',
                    'result' => 'Produit non trouvé',
                ]
            );

            return JsonResponse::create($result);
        }

        $em->remove($commandeProduit);
        $em->flush();
        $result['status'] = 'success';

        $result['message'] = $this->renderView(
            '@AcMarcheMaintenanceShop/panier/_list_produits.html.twig',
            [
                'commande' => $commandeProduit->getCommande(),
            ]
        );

        return JsonResponse::create($result);
    }
}
