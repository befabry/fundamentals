<?php


namespace App\Controller;


use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminUtilityController extends AbstractController
{
    /**
     * @Route("/admin/utility/users",
     *     name="admin_utility_users",
     *     methods={"GET"}
     * )
     * @IsGranted("ROLE_ADMIN_ARTICLE")
     *
     * @param UserRepository $userRepository
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getUserApi(UserRepository $userRepository, Request $request)
    {
        $users = $userRepository->findAllMatching($request->query->get('query'));

        return $this->json(
            ['users' => $users],
            Response::HTTP_OK,
            [],
            ['groups' => ['main']]
        );
    }
}