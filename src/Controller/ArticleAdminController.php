<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleFormType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ArticleAdminController
 * @package App\Controller
 */
class ArticleAdminController extends AbstractController
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
        $form = $this->createForm(ArticleFormType::class)
            ->add('save', SubmitType::class, [
                'label' => 'Create Task',
                'attr' => ['class' => 'btn btn-primary'],
            ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Article $article */
            $article = $form->getData();
            $article->setAuthor($this->getUser());

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
     * @IsGranted("MANAGE", subject="article")
     */
    public function edit(Article $article)
    {
        $this->denyAccessUnlessGranted('MANAGE', $article);

        dd($article);
    }

    /**
     * @Route("/admin/article", name="admin_article_list")
     */
    public function list(ArticleRepository $articleRepository)
    {
        $articles = $articleRepository->findAll();

        return $this->render('article_admin/list.html.twig', [
            'articles' => $articles,
        ]);
    }
}
