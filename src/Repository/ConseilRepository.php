<?php

namespace App\Repository;

use App\Entity\Conseil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Conseil|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conseil|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conseil[]    findAll()
 * @method Conseil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConseilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conseil::class);
    }

    public function findByUser($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :val') // :val => contre les injections SQL
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }
   /*  public function findByUserId($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.id LIKE :val') // :val : contre les injections SQL
            ->setParameter('val', '%'.$value.'%')
            ->getQuery()
            ->getResult()
        ;
    } */

    // /**
    //  * @return Conseil[] Returns an array of Conseil objects
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
    public function findOneBySomeField($value): ?Conseil
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
