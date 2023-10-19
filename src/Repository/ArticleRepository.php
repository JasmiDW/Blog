<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @return lastArticles[] Returns an array of lastArticles objects
     */
    public function findLastArticle($nbArticles): array {
        return $this->createQueryBuilder('l')
            ->orderBy('l.createdAt', 'DESC')
            ->setMaxResults($nbArticles)
            ->getQuery()
            ->getResult();
    }

    /**
     *  Returns an array of articles per page
     */
    public function findAllWithPaging($currentPage, $articlePerPage): Paginator
    {
        $query = $this->createQueryBuilder('a')
            ->orderBy('a.createdAt', 'DESC')
            ->leftJoin('a.comments', 'c')
            //addSelect() pour ajouter des sélections supplémentaires à une requête Doctrine
            ->addSelect('c')
            ->leftJoin('a.categories', 'cat')
            ->addOrderBy('c.createdAt', 'DESC')
            ->setFirstResult(($currentPage - 1) * $articlePerPage)
            ->setMaxResults($articlePerPage)
            ->getQuery();

        //dump($query);
        return new Paginator($query);

    }

    public function findByCategories($categoryId) : array {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.categories', 'cat')
            ->addSelect('cat')
            ->where('cat.id=:id')->setParameter('id',$categoryId)
            ->getQuery()
            ->getResult();

    }
//    /**
//     * @return Article[] Returns an array of Article objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Article
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
