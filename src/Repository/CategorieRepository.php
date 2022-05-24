<?php

namespace AcMarche\MaintenanceShop\Repository;

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

    public function remove(Categorie $categorie): void
    {
        $this->_em->remove($categorie);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }
}
