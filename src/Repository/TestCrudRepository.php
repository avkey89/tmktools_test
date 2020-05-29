<?php

namespace App\Repository;

use App\Entity\TestCrud;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TestCrud|null find($id, $lockMode = null, $lockVersion = null)
 * @method TestCrud|null findOneBy(array $criteria, array $orderBy = null)
 * @method TestCrud[]    findAll()
 * @method TestCrud[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TestCrudRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TestCrud::class);
    }

    // /**
    //  * @return TestCrud[] Returns an array of TestCrud objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TestCrud
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
