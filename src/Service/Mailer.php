<?php

namespace AcMarche\MaintenanceShop\Service;

use AcMarche\MaintenanceShop\Entity\Commande;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class Mailer
{
    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
        private MailerInterface $mailer,
        private ParameterBagInterface $parameterBag
    ) {

    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendPanier(Commande $commande): void
    {
        $css = file_get_contents(
            $this->projectDir.'/src/AcMarche/MaintenanceShop/public/css/mail.css'
        );
        $mail = (new TemplatedEmail())
            ->subject('Nouvelle commande de fournitures')
            ->from($this->parameterBag->get('acmarche_maintenanceshop.email'))
            ->to($this->parameterBag->get('acmarche_maintenance_shop.to1'))
            ->textTemplate('@AcMarcheMaintenanceShop/mail/commande.txt.twig')
            ->htmlTemplate('@AcMarcheMaintenanceShop/mail/commande.html.twig')
            ->context(
                [
                    'commande' => $commande,
                    'css' => $css,
                ]
            );

        $to2 = $this->parameterBag->get('acmarche_maintenance_shop.to2');
        if (filter_var($to2, FILTER_VALIDATE_EMAIL)) {
            $mail->addTo($to2);
        }
        $this->mailer->send($mail);
    }
}
