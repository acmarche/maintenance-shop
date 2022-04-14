<?php

namespace AcMarche\MaintenanceShop\Command;

use AcMarche\MaintenanceShop\Repository\CommandeRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VidePanierCommand extends Command
{
    protected static $defaultName = 'commande:vider-panier';

    public function __construct(private CommandeRepository $commandeRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Vide le panier');
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
