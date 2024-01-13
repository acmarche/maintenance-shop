<?php

namespace AcMarche\MaintenanceShop\Command;

use AcMarche\MaintenanceShop\Repository\CommandeRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'commande:vider-panier', description: 'Vide les paniers'
)]
class VidePanierCommand extends Command
{
    public function __construct(private CommandeRepository $commandeRepository)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $commande = $this->commandeRepository->getCommandeActive();
        if ($commande) {
            $this->commandeRepository->remove($commande);
        }

        return Command::SUCCESS;
    }
}
