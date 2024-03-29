<?php

namespace AcMarche\MaintenanceShop\Repository;

use AcMarche\MaintenanceShop\Doctrine\OrmCrudTrait;
use AcMarche\MaintenanceShop\Entity\CommandeProduit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CommandeProduit|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommandeProduit|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommandeProduit[]    findAll()
 * @method CommandeProduit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeProduitRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommandeProduit::class);
    }
}
