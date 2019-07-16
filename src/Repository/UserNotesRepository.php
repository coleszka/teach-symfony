<?php

namespace App\Repository;

use App\Entity\UserNotes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserNotes|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserNotes|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserNotes[]    findAll()
 * @method UserNotes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserNotesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserNotes::class);
    }

    // /**
    //  * @return UserNotes[] Returns an array of UserNotes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserNotes
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
