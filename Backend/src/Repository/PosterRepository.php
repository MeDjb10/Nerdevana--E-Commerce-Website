<?php

namespace App\Repository;

use App\Entity\Poster;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Poster>
 *
 * @method Poster|null find($id, $lockMode = null, $lockVersion = null)
 * @method Poster|null findOneBy(array $criteria, array $orderBy = null)
 * @method Poster[]    findAll()
 * @method Poster[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PosterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Poster::class);
    }


    public function findByArtist(string $artist)
    {
        return $this->createQueryBuilder('p')
            ->where('p.artist = :artist')
            ->setParameter('artist', $artist)
            ->getQuery()
            ->getResult();
    }


    
    public function findByAlbumWord(string $word)
    {
            return $this->createQueryBuilder('p')
                ->where('p.album LIKE :word')
                ->setParameter('word', '%' . $word . '%')
                ->getQuery()
                ->getResult();
    }

//    /**
//     * @return Poster[] Returns an array of Poster objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Poster
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
