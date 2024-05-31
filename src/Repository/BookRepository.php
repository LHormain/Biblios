<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function findByFilter(array $filtres = []): QueryBuilder
    {
        $qb = $this->createQueryBuilder('b');

        if (\array_key_exists('start', $filtres)) {
            $qb->andWhere('b.editedAt >= :start')
               ->setParameter('start', new \DateTimeImmutable($filtres['start']));
        }

        if (\array_key_exists('end', $filtres)) {
            $qb->andWhere('b.editedAt <= :end')
               ->setParameter('end', new \DateTimeImmutable($filtres['end']));
        }

        if (\array_key_exists('pays', $filtres)) {
            $qb->andWhere('b.langue LIKE :pays')
               ->setParameter('pays', '%'.$filtres['pays'].'%');
        }

        if (\array_key_exists('category', $filtres)) {
            $qb->andWhere('b.category = :category')
               ->setParameter('category', $filtres['category']);
        }

        if (\array_key_exists('editor', $filtres)) {
            $qb->andWhere('b.editor = :editor')
               ->setParameter('editor', $filtres['editor']);
        }

        if (\array_key_exists('mediaType', $filtres)) {
            $qb->andWhere('b.mediaType = :mediaType')
               ->setParameter('mediaType', $filtres['mediaType']);
        }

        return $qb;
    }
    //    /**
    //     * @return Book[] Returns an array of Book objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Book
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
