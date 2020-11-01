<?php

namespace AcMarche\MaintenanceShop\Repository;

use AcMarche\MaintenanceShop\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function findAll()
    {
        return $this->findBy(array(), array('nom' => 'ASC'));
    }

    /**
     * Pour formulaire avec liste deroulante
     * @return Categorie[]
     */
    public function getForSearch()
    {
        $qb = $this->createQueryBuilder('c');

        $qb->orderBy('c.nom');
        $query = $qb->getQuery();

        $results = $query->getResult();
        $categories = array();

        foreach ($results as $categorie) {
            $categories[$categorie->getNom()] = $categorie->getId();
        }

        return $categories;
    }

    public function remove(Categorie $categorie)
    {
        $this->_em->remove($categorie);
    }

    public function flush()
    {
        $this->_em->flush();
    }
}
