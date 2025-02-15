<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Like;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class LikeController extends AbstractController
{
    #[Route('/like/{id}', name: 'like_article', methods: ['POST'])]
public function likeArticle($id, LikeRepository $likeRepository, EntityManagerInterface $em, Request $request): JsonResponse
{
    $user = $this->getUser();
    $article = $em->getRepository(Article::class)->find($id);

    if (!$user || !$article) {
        return new JsonResponse(['error' => 'Unauthorized'], 403);
    }

    $existingLike = $likeRepository->findLikeByUserAndArticle($user, $article);
    $liked = false;

    if ($existingLike) {
        $likeRepository->removeLike($existingLike);
        $article->setNbrLikes($article->getNbrLikes() - 1);
    } else {
        $like = new Like();
        $like->setUser($user);
        $like->setArticle($article);
        $em->persist($like);
        $article->setNbrLikes($article->getNbrLikes() + 1);
        $liked = true;
    }

    $em->flush();

    return new JsonResponse([
        'liked' => $liked,
        'nbrLikes' => $article->getNbrLikes()
    ]);
}

}
