<?php

namespace App\Action;

use App\Action\Traits\FlashTrait;
use App\Action\Traits\RoutingTrait;
use App\Action\Traits\SecurityTrait;
use App\Action\Traits\TwigTrait;
use App\Entity\Server;
use App\Entity\ServerMember;
use App\Form\Model\CreateServer;
use App\Form\Type\Action\CreateServerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/servers/new", name="app_server_create")
 */
final class CreateServerAction
{
    use FlashTrait;
    use RoutingTrait;
    use SecurityTrait;
    use TwigTrait;

    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $formFactory;

    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_SERVER_CREATE');

        $model = new CreateServer();
        $form = $this->formFactory->create(CreateServerType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $server = Server::createFromCreationForm($model, $this->security->getUser());

            $this->entityManager->persist($server);
            $this->entityManager->flush();

            $this->flash('success', 'flash.success.server.create');

            return $this->redirectToRoute('app_server_list');
        }

        return $this->renderPage('create-server', 'app/server/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
