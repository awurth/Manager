<?php

namespace App\Action;

use App\Upload\StorageInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/projects", name="app_project_list")
 */
class ListProjectsAction
{
    use SecurityTrait;
    use TwigTrait;

    private $projectLogoStorage;

    public function __construct(StorageInterface $projectLogoStorage)
    {
        $this->projectLogoStorage = $projectLogoStorage;
    }

    public function __invoke(): Response
    {
        $this->denyAccessUnlessLoggedIn();

        return $this->renderPage('list-projects', 'app/project/list.html.twig', [
            'projects' => $this->getUser()->getProjects(),
            'projectLogoStorage' => $this->projectLogoStorage
        ]);
    }
}
