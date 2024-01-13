<?php

namespace AcMarche\MaintenanceShop\Controller;

use AcMarche\MaintenanceShop\Entity\User;
use AcMarche\MaintenanceShop\Form\UtilisateurEditType;
use AcMarche\MaintenanceShop\Form\UtilisateurPasswordType;
use AcMarche\MaintenanceShop\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/utilisateur/password')]
#[IsGranted(data: 'ROLE_MAINTENANCE_ADMIN')]
class PasswordController extends AbstractController
{
    public function __construct(private UserRepository $userRepository, private UserPasswordHasherInterface $passwordEncoder, private ManagerRegistry $managerRegistry)
    {
    }

    /**
     * Displays a form to edit an existing Utilisateur utilisateur.
     *
     * @todo
     */
    #[Route(path: '/{id}/password', name: 'commande_utilisateur_password', methods: ['GET', 'POST'])]
    public function passord(Request $request, User $utilisateur): RedirectResponse|Response
    {
        $em = $this->managerRegistry->getManager();
        $editForm = $this->createForm(UtilisateurEditType::class, $utilisateur);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('commande_utilisateur');
        }

        return $this->render(
            '@AcMarcheMaintenanceShop/utilisateur/password.html.twig',
            [
                'utilisateur' => $utilisateur,
                'edit_form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing categorie entity.
     */
    #[Route(path: '/password/{id}', name: 'commande_utilisateur_password', methods: ['GET', 'POST'])]
    public function password(Request $request, User $user, UserPasswordHasherInterface $userPasswordEncoder): RedirectResponse|Response
    {
        $em = $this->managerRegistry->getManager();
        $form = $this->createForm(UtilisateurPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $userPasswordEncoder->hashPassword($user, $form->getData()->getPlainPassword());
            $user->setPassword($password);
            $em->flush();

            $this->addFlash('success', 'Mot de passe changé');

            return $this->redirectToRoute('commande_utilisateur_show', ['id' => $user->getId()]);
        }

        return $this->render(
            '@AcMarcheMaintenanceShop/utilisateur/password.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }
}
