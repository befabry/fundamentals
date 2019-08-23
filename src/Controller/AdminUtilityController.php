<?php


namespace App\Controller;


use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminUtilityController extends AbstractController
{
    /**
     * @Route("/admin/utility/users",
     *     name="admin_utility_users_autocomplete",
     *     methods={"GET"}
     * )
     * @IsGranted("ROLE_ADMIN_ARTICLE")
     */
    public function getUserApi(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();

        return $this->json(
            ['users' => $users],
            Response::HTTP_OK,
            [],
            ['groups' => ['main']]
        );
    }
}