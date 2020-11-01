<?php

namespace AcMarche\MaintenanceShop\Service;

use AcMarche\MaintenanceShop\Entity\Commande;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;

class Mailer
{
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(MailerInterface $mailer, ParameterBagInterface $parameterBag)
    {
        $this->mailer = $mailer;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @param Commande $commande
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendPanier(Commande $commande)
    {
        $mail = (new TemplatedEmail())
            ->subject("Nouvelle commande de fournitures")
            ->from($this->parameterBag->get('acmarche_maintenanceshop.email'))
            ->to($this->parameterBag->get('acmarche_maintenance_shop.to1'))
            ->textTemplate('@AcMarcheMaintenanceShop/mail/commande.txt.twig')
            ->context(
                array(
                    'commande' => $commande,
                )
            );

        $to2 = $this->parameterBag->get('acmarche_maintenance_shop.to2');
        if (filter_var($to2, FILTER_VALIDATE_EMAIL)) {
            $mail->addTo($to2);
        }
        $this->mailer->send($mail);
    }

}
