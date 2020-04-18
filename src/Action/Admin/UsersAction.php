<?php

namespace App\Action\Admin;

use App\Action\AbstractAction;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/users", name="app_admin_users")
 */
class UsersAction extends AbstractAction
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

        return $this->renderPage('admin-users', 'app/admin/users.html.twig', [
            'users' => $users
        ]);
    }
}
