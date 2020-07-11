<?php

namespace App\Action\Server;

use App\Action\FlashTrait;
use App\Action\PaginationTrait;
use App\Action\RoutingTrait;
use App\Action\TwigTrait;
use App\Entity\ServerMember;
use App\Form\Model\AddServerMember;
use App\Form\Type\Action\AddServerMemberType;
use App\Repository\ServerMemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/members", name="app_server_members")
 */
class ServerMembersAction extends AbstractServerAction
{
    use FlashTrait;
    use PaginationTrait;
    use RoutingTrait;
    use TwigTrait;

    private $entityManager;
    private $formFactory;
    private $serverMemberRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        ServerMemberRepository $serverMemberRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->serverMemberRepository = $serverMemberRepository;
    }

    public function __invoke(Request $request, string $id): Response
    {
        $this->preInvoke($id);

        $this->denyAccessUnlessGranted('MEMBER', $this->server);

        $model = new AddServerMember($this->server);
        $form = $this->formFactory->create(AddServerMemberType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $member = ServerMember::createFromServerMemberAdditionForm($model);

            $this->entityManager->persist($member);
            $this->entityManager->flush();

            $this->flash('success', 'flash.success.server.member.add');

            return $this->redirectToRoute('app_server_members', ['id' => $this->server->getId()]);
        }

        $pager = $this->paginate($this->getQueryBuilder(), $request);

        return $this->renderPage('server-members', 'app/server/members.html.twig', [
            'form' => $form->createView(),
            'server' => $this->server,
            'members' => $pager->getCurrentPageResults(),
            'pager' => $pager
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        $this->breadcrumbs->addItem('breadcrumb.server.members');
    }

    private function getQueryBuilder(): QueryBuilder
    {
        return $this->serverMemberRepository->createQueryBuilder('m')
            ->join('m.user', 'u')->addSelect('u')
            ->where('m.server = :server')
            ->setParameter('server', $this->server->getId(), 'uuid_binary')
            ->orderBy('m.accessLevel', 'DESC')
            ->addOrderBy('m.createdAt', 'DESC');
    }
}
