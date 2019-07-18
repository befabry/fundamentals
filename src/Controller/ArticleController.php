<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Service\SlackClient;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends AbstractController
{
    /**
     * @param ArticleRepository $repository
     * @return Response
     *
     * @Route("/", name="app_homepage")
     */
    public function homepage(ArticleRepository $repository)
    {
        //$repository = $em->getRepository(Article::class);
        $articles = $repository->findAllPublishedOrderedByNewest();

        return $this->render('article/homepage.html.twig', [
            'articles' => $articles,
        ]);
    }


    /**
     * @param Article $article
     * @param SlackClient $slackClient
     * @return Response
     *
     * @Route("/news/{slug}", name="article_show")
     */
    public function show(Article $article, SlackClient $slackClient)
    {

        if($article->getSlug() === 'khaaaaaan'){
            $slackClient->sendMessage('Khan', 'Ah, Kirk, my old friend');
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/news/{slug}/heart", name="article_toggle_heart", methods={"POST"})
     * @param Article $article
     * @param LoggerInterface $logger
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function toggleArticleHeart(Article $article, LoggerInterface $logger, EntityManagerInterface $em)
    {
        $article->incrementHeartCount();
        $em->flush();

        $logger->info('Article is being hearted!');

        return new JsonResponse(['hearts' => $article->getHeartCount()]);
    }
}
