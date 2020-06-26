<?php

namespace App\Repository;

use App\Entity\CategorieAliment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CategorieAliment|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategorieAliment|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategorieAliment[]    findAll()
 * @method CategorieAliment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieAlimentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorieAliment::class);
    }

    // /**
    //  * @return CategorieAliment[] Returns an array of CategorieAliment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CategorieAliment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
