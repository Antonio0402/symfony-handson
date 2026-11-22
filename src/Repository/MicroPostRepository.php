<?php

namespace App\Repository;

use App\Entity\MicroPost;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MicroPost>
 */
class MicroPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MicroPost::class);
    }

    public function findAllWithMinLikes(int $minLikes): array
    {
        $idList = $this->createQueryBuilder('p')
            ->select('p.id')
            ->leftJoin('p.likedBy', 'l')
            ->groupBy('p.id')
            ->having('COUNT(l) >= :minLikes')
            ->setParameter('minLikes', $minLikes)
            ->getQuery()
            ->getSingleColumnResult();

        if ($idList === []) {
            return [];
        }

        return $this->findAllQuery(
            withLikes: true,
            withComments: true,
            withAuthors: true,
            withProfiles: true
        )->where('p.id IN (:idList)')
        ->setParameter('idList', $idList)
        ->getQuery()
        ->getResult();
    }

    public function findAllWithComments(): array {
        // return $this->createQueryBuilder('p')
        //     ->addSelect('c')
            // Doctrine resolves p.comments via MicroPost::$comments OneToMany targetEntity
        //     ->leftJoin('p.comments', 'c')
        //     ->orderBy('p.created', 'DESC')
        //     ->getQuery()
        //     ->getResult();

        return $this->findAllQuery(
            withComments: true
        )->getQuery()->getResult();
    }

    public function findAllByAuthor(Collection|array $author): array
    {
        return $this->findAllQuery(
            withComments: true,
            withLikes: true,
            withAuthors: true,
            withProfiles: true
        )->where('p.author IN (:author)')
        ->setParameter(
            'author',
            $author
        )->getQuery()->getResult();
    }

    public function findAllQuery(
        bool $withComments = false,
        bool $withLikes = false,
        bool $withAuthors = false,
        bool $withProfiles = false
    ): QueryBuilder {
            $query = $this->createQueryBuilder('p');

            if($withComments) {
                // Doctrine resolves p.comments via MicroPost::$comments OneToMany targetEntity
                $query->leftJoin('p.comments', 'c')
                ->addSelect('c');
            }

            if($withLikes) {
                // Doctrine resolve p.likedBy via MicroPost::$likedBy ManyToMany targetEntity
                $query->leftJoin('p.likedBy', 'l')
                ->addSelect('l');
            }
            
            // Doctrine resolve p.author via MicroPost::$author ?User classname type
            if($withAuthors) {
                $query->leftJoin('p.author', 'a')
                ->addSelect('a');
            }

            if($withProfiles) {
                $query->leftJoin('a.userProfile', 'up')
                ->addSelect('up');
            }

            return $query->orderBy('p.created', 'DESC');
    }

    //    /**
    //     * @return MicroPost[] Returns an array of MicroPost objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?MicroPost
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
