<?php

namespace AcMarche\MaintenanceShop\Repository;

use AcMarche\MaintenanceShop\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function findAll()
    {
        return $this->findBy(array(), array('nom' => 'ASC'));
    }

    public function setCriteria($args)
    {
        $nom = isset($args['nom']) ? $args['nom'] : null;
        $categorie = isset($args['categorie']) ? $args['categorie'] : 0;

        $qb = $this->createQueryBuilder('produit');
        $qb->leftJoin('produit.categorie', 'categorie', 'WITH');
        $qb->addSelect('categorie');

        if ($nom) {
            $qb->andWhere('produit.nom LIKE :mot OR produit.description LIKE :mot ')
                ->setParameter('mot', '%' . $nom . '%');
        }

        if ($categorie) {
            $qb->andWhere('categorie.id = :cat')
                ->setParameter('cat', $categorie);
        }

        return $qb;
    }

    public function search($args)
    {
        $qb = $this->setCriteria($args);

        $qb->addOrderBy('produit.nom', 'ASC');

        $query = $qb->getQuery();

        // echo  $query->getSQL();

        $results = $query->getResult();

        return $results;
    }

    public function remove(Produit $produit)
    {
        $this->_em->remove($produit);
    }
}
