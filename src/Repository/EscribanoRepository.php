<?php

namespace App\Repository;

use App\Entity\Escribano;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Escribano|null find($id, $lockMode = null, $lockVersion = null)
 * @method Escribano|null findOneBy(array $criteria, array $orderBy = null)
 * @method Escribano[]    findAll()
 * @method Escribano[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EscribanoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Escribano::class);
    }

    // /**
    //  * @return Escribano[] Returns an array of Escribano objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Escribano
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
