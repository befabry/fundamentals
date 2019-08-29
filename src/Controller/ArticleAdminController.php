<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleFormType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ArticleAdminController
 * @package App\Controller
 */
class ArticleAdminController extends BaseController
{
    /**
     * @Route("/admin/article/new", name="admin_article_new")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @IsGranted("ROLE_ADMIN_ARTICLE")
     */
    public function new(EntityManagerInterface $em, Request $request)
    {
        $form = $this->createForm(ArticleFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Article $article */
            $article = $form->getData();

            $em->persist($article);
            $em->flush();

            $this->addFlash('success', 'Article Created ! Knowledge is power !');

            return $this->redirectToRoute('admin_article_list');
        }

        return $this->render('article_admin/new.html.twig', [
            'articleForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/article/{id}/edit", name="admin_article_edit")
     * @param Article $article
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @IsGranted("MANAGE", subject="article")
     */
    public function edit(Article $article, Request $request, EntityManagerInterface $em)
    {
        //$this->denyAccessUnlessGranted('MANAGE', $article);

        $form = $this->createForm(ArticleFormType::class, $article, [
            'include_published_at' => true
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($article);
            $em->flush();

            $this->addFlash('success', 'Article Updated ! Inaccuracies squashed !');

            return $this->redirectToRoute('admin_article_edit', [
                'id' => $article->getId(),
            ]);
        }

        return $this->render('article_admin/edit.html.twig', [
            'articleForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/article", name="admin_article_list")
     * @IsGranted("MANAGE")
     */
    public function list(ArticleRepository $articleRepository)
    {
        $articles = $articleRepository->findAll();

        return $this->render('article_admin/list.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/admin/article/location-select", name="admin_article_location_select")
     * @IsGranted("ROLE_USER")
     *
     * @param Request $request
     * @return Response
     */
    public function getSpecificLocationSelect(Request $request)
    {
        #First make sure the user is logged (IsGranted)
        #The User is admin and he has articles created
        #Method can be moved to a Voter, but only used here
        if (!$this->isGranted('ROLE_ADMIN_ARTICLE') && $this->getUser()->getArticles()->isEmpty()) {
            throw $this->createAccessDeniedException();
        }

        $article = new Article();
        $article->setLocation($request->query->get('location'));
        $form = $this->createForm(ArticleFormType::class, $article);

        if (!$form->has('specificLocationName')) {
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        return $this->render('article_admin/_specific_location_name.html.twig', [
            'articleForm' => $form->createView()
        ]);
    }
}
