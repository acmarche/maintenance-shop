<?php

namespace AcMarche\MaintenanceShop\Controller;

use AcMarche\MaintenanceShop\Entity\User;
use AcMarche\MaintenanceShop\Form\UtilisateurEditType;
use AcMarche\MaintenanceShop\Form\UtilisateurType;
use AcMarche\MaintenanceShop\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/utilisateur')]
#[IsGranted(data: 'ROLE_MAINTENANCE_ADMIN')]
class UtilisateurController extends AbstractController
{
    public function __construct(private UserRepository $userRepository, private UserPasswordHasherInterface $passwordEncoder, private ManagerRegistry $managerRegistry)
    {
    }

    /**
     * Lists all Utilisateur entities.
     */
    #[Route(path: '/', name: 'commande_utilisateur', methods: ['GET'])]
    public function index(): Response
    {
        $users = $this->userRepository->findBy([], ['nom' => 'ASC']);

        return $this->render(
            '@AcMarcheMaintenanceShop/utilisateur/index.html.twig',
            [
                'users' => $users,
            ]
        );
    }

    /**
     * Displays a form to create a new Utilisateur utilisateur.
     */
    #[Route(path: '/new', name: 'commande_utilisateur_new', methods: ['GET', 'POST'])]
    public function new(Request $request): RedirectResponse|Response
    {
        $utilisateur = new User();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateur->setPassword(
                $this->passwordEncoder->hashPassword($utilisateur, $form->getData()->getPlainPassword())
            );
            $this->userRepository->persist($utilisateur);
            $this->userRepository->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté");

            return $this->redirectToRoute('commande_utilisateur');
        }

        return $this->render(
            '@AcMarcheMaintenanceShop/utilisateur/new.html.twig',
            [
                'utilisateur' => $utilisateur,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Utilisateur utilisateur.
     */
    #[Route(path: '/{id}', name: 'commande_utilisateur_show', methods: ['GET'])]
    public function show(User $utilisateur): Response
    {
        return $this->render(
            '@AcMarcheMaintenanceShop/utilisateur/show.html.twig',
            [
                'utilisateur' => $utilisateur,
            ]
        );
    }

    /**
     * Displays a form to edit an existing Utilisateur utilisateur.
     */
    #[Route(path: '/{id}/edit', name: 'commande_utilisateur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $utilisateur): RedirectResponse|Response
    {
        $editForm = $this->createForm(UtilisateurEditType::class, $utilisateur);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->userRepository->flush();
            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('commande_utilisateur');
        }

        return $this->render(
            '@AcMarcheMaintenanceShop/utilisateur/edit.html.twig',
            [
                'utilisateur' => $utilisateur,
                'form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Deletes a Utilisateur utilisateur.
     */
    #[Route(path: '/{id}', name: 'commande_utilisateur_delete', methods: ['POST'])]
    public function delete(Request $request, User $user): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->managerRegistry->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'L\'utilisateur a été supprimé');
        }

        return $this->redirectToRoute('commande_utilisateur');
    }
}
