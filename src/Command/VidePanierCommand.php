<?php

namespace AcMarche\MaintenanceShop\Command;

use AcMarche\MaintenanceShop\Repository\CommandeRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VidePanierCommand extends Command
{
    /**
     * @var CommandeRepository
     */
    private $commandeRepository;

    public function __construct(CommandeRepository $commandeRepository)
    {
        parent::__construct();
        $this->commandeRepository = $commandeRepository;
    }

    protected function configure()
    {
        $this
            ->setName('acmaintenance:viderpanier')
            ->setDescription('Vide le panier');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commande = $this->commandeRepository->getCommandeActive();
        if ($commande) {
            $this->commandeRepository->remove($commande);
        }
        return 0;
    }

}
