<?php

namespace App\Action;

use App\Entity\Server;
use App\Entity\ServerMember;
use App\Form\Model\CreateServer;
use App\Form\Type\Action\CreateServerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/servers/new", name="app_server_create")
 */
class CreateServerAction
{
    use RoutingTrait;
    use SecurityTrait;
    use TwigTrait;

    private $entityManager;
    private $flashBag;
    private $formFactory;

    public function __construct(
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        FormFactoryInterface $formFactory
    )
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
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
            $server = (new Server($model->name))
                ->setIp($model->ip)
                ->setOperatingSystem($model->operatingSystem);

            $server->addMember(
                (new ServerMember())
                    ->setUser($this->getUser())
                    ->setAccessLevel(ServerMember::ACCESS_LEVEL_OWNER)
            );

            $this->entityManager->persist($server);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'flash.success.server.create');

            return $this->redirectToRoute('app_server_list');
        }

        return $this->renderPage('create-server', 'app/server/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
