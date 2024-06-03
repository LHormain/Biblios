<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findByMedia(array $media = []): QueryBuilder
    {
        $qb = $this-> createQueryBuilder('c');

        if (\array_key_exists('book', $media)) {
            $qb->andWhere('c.book = :book')
               ->setParameter('book', $media['book']);
        }

        if (\array_key_exists('status', $media)) {
            $qb->andWhere('c.status = :status')
               ->setParameter('status', $media['status']);
        }

        return $qb;
    }
    //    /**
    //     * @return Comment[] Returns an array of Comment objects
    //     */
    //    public function findByExampleField($value): array
    //    {
        //    return $this->createQueryBuilder('c')
            //    ->andWhere('c.exampleField = :val')
            //    ->setParameter('val', $value)
            //    ->orderBy('c.id', 'ASC')
            //    ->setMaxResults(10)
            //    ->getQuery()
            //    ->getResult()
        //    ;
    //    }

    //    public function findOneBySomeField($value): ?Comment
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
