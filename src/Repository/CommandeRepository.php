<?php

namespace AcMarche\MaintenanceShop\Repository;

use AcMarche\MaintenanceShop\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

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

    /**
     * @return Commande
     */
    public function getCommandeActive()
    {
        $qb = $this->createQueryBuilder('commande');
        $qb->leftJoin('commande.produits', 'produits', 'WITH');
        $qb->addSelect('produits');

        $qb->andWhere('commande.envoye = 0');

        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }

    public function remove(Commande $commande)
    {
        $this->_em->remove($commande);
        $this->save();
    }

    private function save()
    {
        $this->_em->flush();
    }
}
