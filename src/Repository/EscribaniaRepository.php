<?php

namespace App\Repository;

use App\Entity\Escribania;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Escribania|null find($id, $lockMode = null, $lockVersion = null)
 * @method Escribania|null findOneBy(array $criteria, array $orderBy = null)
 * @method Escribania[]    findAll()
 * @method Escribania[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EscribaniaRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Escribania::class);
    }

    // /**
    //  * @return Escribania[] Returns an array of Escribania objects
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
    public function findOneBySomeField($value): ?Escribania
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
