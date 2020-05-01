<?php

namespace App\Action\ProjectGroup;

use App\Action\TwigTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="app_project_group_view")
 */
class ViewProjectGroupAction extends AbstractProjectGroupAction
{
    use TwigTrait;

    public function __invoke(string $slug): Response
    {
        $this->preInvoke($slug);

        $this->denyAccessUnlessGranted('GUEST', $this->projectGroup);

        return $this->renderPage('view-project-group', 'app/project_group/view.html.twig', [
            'group' => $this->projectGroup
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        $this->breadcrumbs->addItem('breadcrumb.project_group.overview');
    }
}
