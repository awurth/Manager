<?php

namespace App\Action\Server;

use App\Action\RoutingTrait;
use App\Action\TwigTrait;
use App\Entity\ServerMember;
use App\Form\Model\AddServerMember;
use App\Form\Type\Action\AddServerMemberType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/members", name="app_server_members")
 */
class ServerMembersAction extends AbstractServerAction
{
    use RoutingTrait;
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

    public function __invoke(Request $request, string $id): Response
    {
        $this->preInvoke($id);

        $this->denyAccessUnlessGranted('MEMBER', $this->server);

        $model = new AddServerMember($this->server);
        $form = $this->formFactory->create(AddServerMemberType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->server->addMember(
                (new ServerMember())
                    ->setUser($model->user)
                    ->setAccessLevel($model->accessLevel)
            );

            $this->entityManager->persist($this->server);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'flash.success.server.member.add');

            return $this->redirectToRoute('app_server_members', ['id' => $this->server->getId()]);
        }

        return $this->renderPage('server-members', 'app/server/members.html.twig', [
            'form' => $form->createView(),
            'server' => $this->server
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        $this->breadcrumbs->addItem('breadcrumb.server.members');
    }
}
