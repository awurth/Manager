<?php

namespace App\Action\Server;

use App\Action\Traits\FlashTrait;
use App\Action\Traits\RoutingTrait;
use App\Action\Traits\TwigTrait;
use App\Form\Model\EditServer;
use App\Form\Type\Action\EditServerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/edit", name="app_server_edit")
 */
final class EditServerAction extends AbstractServerAction
{
    use FlashTrait;
    use RoutingTrait;
    use TwigTrait;

    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $formFactory;

    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
    }

    public function __invoke(Request $request, string $id): Response
    {
        $this->preInvoke($id);

        $this->denyAccessUnlessGranted('EDIT', $this->server);

        $model = new EditServer($this->server);
        $form = $this->formFactory->create(EditServerType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->server->updateFromEditionForm($model);

            $this->entityManager->persist($this->server);
            $this->entityManager->flush();

            $this->flash('success', 'flash.success.server.edit');

            return $this->redirectToRoute('app_server_edit', ['id' => $this->server->getId()]);
        }

        return $this->renderPage('edit-server', 'app/server/edit.html.twig', [
            'form' => $form->createView(),
            'server' => $this->server
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        $this->breadcrumbs->addItem('breadcrumb.server.edit');
    }
}
