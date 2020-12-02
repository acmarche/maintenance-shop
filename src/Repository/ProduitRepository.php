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
        $qb->leftJoin('produit.associatedProducts', 'associatedProducts', 'WITH');
        $qb->addSelect('categorie', 'associatedProducts');

        if ($nom) {
            $qb->andWhere('produit.nom LIKE :mot OR produit.description LIKE :mot ')
                ->setParameter('mot', '%'.$nom.'%');
        }

        if ($categorie) {
            $qb->andWhere('categorie.id = :cat')
                ->setParameter('cat', $categorie);
        }

        if (!$nom) {
            $associated = $this->getAllAssociatedProducts();
            $qb->andWhere('produit NOT IN (:associated)')
                ->setParameter('associated', $associated);
        }

        return $qb;
    }

    /**
     * @param $args
     * @return Produit[]
     */
    public function search($args): array
    {
        $qb = $this->setCriteria($args);


        return $qb
            ->addOrderBy('produit.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Produit[]
     */
    public function getAllAssociatedProducts(): array
    {
        $data = $this->createQueryBuilder('produit')
            ->leftJoin('produit.categorie', 'categorie', 'WITH')
            ->leftJoin('produit.associatedProducts', 'associatedProducts', 'WITH')
            ->addSelect('categorie', 'associatedProducts')
            ->getQuery()->getResult();

        $products = [];
        foreach ($data as $product) {
            if (count($product->getAssociatedProducts()) > 0) {
                $products = array_merge($products, $product->getAssociatedProducts()->toArray());
            }
        }

        // $products = array_merge(...$products);

        return $products;
    }

    public function remove(Produit $produit)
    {
        $this->_em->remove($produit);
    }

    public function flush()
    {
        $this->_em->flush();
    }
}
