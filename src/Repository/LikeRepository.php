<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Like;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Like>
 *
 * @method Like|null find($id, $lockMode = null, $lockVersion = null)
 * @method Like|null findOneBy(array $criteria, array $orderBy = null)
 * @method Like[]    findAll()
 * @method Like[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Like::class);
    }
    /**
     * Vérifie si un utilisateur a déjà aimé un article
     */
    public function findLikeByUserAndArticle(User $user, Article $article): ?Like
{
    return $this->createQueryBuilder('l')
        ->andWhere('l.user = :user')
        ->andWhere('l.article = :article')
        ->setParameter('user', $user->getId())  // Assure-toi que c'est bien un entier
        ->setParameter('article', $article->getId())
        ->getQuery()
        ->getOneOrNullResult();
}
    

    /**
     * Compte le nombre de likes pour un article donné
     */
    public function countLikesForArticle($article): int
    {
        return $this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->andWhere('l.article = :article')
            ->setParameter('article', $article)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Récupère tous les articles aimés par un utilisateur
     */
    public function findLikedArticlesByUser($user)
    {
        return $this->createQueryBuilder('l')
            ->join('l.article', 'a')
            ->addSelect('a')
            ->andWhere('l.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * Supprime un like
     */
    public function removeLike(Like $like): void
    {
        $em = $this->getEntityManager();
        $em->remove($like);
        $em->flush();
    }

}
