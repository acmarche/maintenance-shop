<?php

namespace AcMarche\MaintenanceShop\Service;

use AcMarche\MaintenanceShop\Entity\Commande;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

Class Mailer
{
    /**
     * @var string
     */
    private $from;
    /**
     * @var array
     */
    private $to;

    public function __construct(
        MailerInterface $mailer,
        string $from,
        array $to
    ) {
        $this->mailer = $mailer;
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @param Commande $commande
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendPanier(Commande $commande)
    {
        $mail = (new TemplatedEmail())
            ->subject("Nouvelle commande de fournitures")
            ->from($this->from)
            ->to(...$this->to)
            ->textTemplate('@AcMarcheMaintenanceShop/mail/commande.txt.twig')
            ->context(
                array(
                    'commande' => $commande,
                )
            );

        $this->mailer->send($mail);
    }

}
