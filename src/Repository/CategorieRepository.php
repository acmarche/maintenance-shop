<?php

namespace AcMarche\MaintenanceShop\Repository;

use AcMarche\MaintenanceShop\Doctrine\OrmCrudTrait;
use AcMarche\MaintenanceShop\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Categorie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categorie|null findOneBy(array $criteria, array $orderBy = null)
 * method Categorie[]    findAll()
 * @method Categorie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categorie::class);
    }

    public function findAll(): array
    {
        return $this->findBy([], ['nom' => 'ASC']);
    }

    public function getQbl(): QueryBuilder
    {
        return $this->createQueryBuilder('produit')
            ->addOrderBy('produit.nom', 'ASC');
    }
}
