<?php

namespace AcMarche\MaintenanceShop\Repository;

use AcMarche\MaintenanceShop\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Commande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commande[]    findAll()
 * @method Commande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    public function getCommandeActive():?Commande
    {
        $qb = $this->createQueryBuilder('commande');
        $qb->leftJoin('commande.produits', 'produits', 'WITH');
        $qb->addSelect('produits');

        $qb->andWhere('commande.envoye = 0');

        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * @return Commande[]
     */
    public function findSended(): array
    {
        return $this->createQueryBuilder('commande')
            ->leftJoin('commande.produits', 'produits', 'WITH')
            ->addSelect('produits')
            ->andWhere('commande.envoye = 1')
            ->addOrderBy('commande.id', 'DESC')
            ->getQuery()->getResult();
    }

    public function remove(Commande $commande): void
    {
        $this->_em->remove($commande);
        $this->save();
    }

    private function save(): void
    {
        $this->_em->flush();
    }

    public function persist(Commande $commande): void
    {
        $this->_em->persist($commande);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }
}
