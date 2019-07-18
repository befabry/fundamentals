<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleAdminController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN_ARTICLE")
     * @Route("/admin/article/new", name="admin_article_new")
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function new(EntityManagerInterface $em)
    {
        die('todo');

        return new Response(sprintf(
            'Hiya! New article id: %d slug %s',
            $article->getId(),
            $article->getSlug()
        ));
    }
}
