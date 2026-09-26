<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SortDirection;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @return Book[] Returns an array of Book objects
     */
    public function findByLikeNameField(string $value): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.name LIKE :val')
            ->setParameter('val', '%' . $value . '%')
            ->orderBy('b.id', SortDirection::Ascending)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Book[] Returns an array of Book objects
     */
    public function findByIsbnField(string $value): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.isbn = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', SortDirection::Ascending)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Book[] Returns an array of Book objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', SortDirection::Ascending)
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

    /**
     * @return Book[] Returns an array of Book objects
     */
    public function findRecentlyAdded(int $max = 15, bool $mustOwned = true): array
    {
        $query = $this->createQueryBuilder('b')
            ->orderBy('b.id', SortDirection::Descending)
            ->setMaxResults($max);

        if ($mustOwned) {
            // Check if bookId in ownedBooks.book field
            $query->leftJoin('b.ownedBooks', 'ownedBooks')
                ->andWhere('ownedBooks.id IS NOT NULL');
        }

        return $query->getQuery()
            ->getResult();
    }
}
