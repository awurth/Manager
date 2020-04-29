<?php

namespace App\Action\Admin;

use App\Action\AbstractAction;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/users", name="app_admin_user_list")
 */
class ListUsersAction extends AbstractAction
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $this->userRepository->findAll();

        return $this->renderPage('admin-list-users', 'app/admin/list_users.html.twig', [
            'users' => $users
        ]);
    }
}
