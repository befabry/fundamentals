<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentAdminController extends AbstractController
{
    /**
     * @Route("admin/comment/", name="comment_admin")
     * @IsGranted("ROLE_ADMIN_COMMENT")
     * @param Request $request
     * @param CommentRepository $commentRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, CommentRepository $commentRepository, PaginatorInterface $paginator)
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN');
        $q = $request->query->get('q');

        $queryBuilder = $commentRepository->getWithSearchQueryBuilder($q);

        /** @var \Knp\Component\Pager\Pagination\PaginationInterface $pagination */
        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('comment_admin/index.html.twig', [
            'pagination' => $pagination
        ]);
    }
}
