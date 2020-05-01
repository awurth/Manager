<?php

namespace App\Action\Admin;

use App\Action\AbstractAction;
use App\Action\SecurityTrait;
use App\Action\TwigTrait;
use App\Repository\ProjectTypeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/projects/types", name="app_admin_project_type_list")
 */
class ListProjectTypesAction extends AbstractAction
{
    use SecurityTrait;
    use TwigTrait;

    private $projectTypeRepository;

    public function __construct(ProjectTypeRepository $projectTypeRepository)
    {
        $this->projectTypeRepository = $projectTypeRepository;
    }

    public function __invoke(): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $types = $this->projectTypeRepository->findAll();

        return $this->renderPage('admin-list-project-types', 'app/admin/list_project_types.html.twig', [
            'types' => $types
        ]);
    }
}
