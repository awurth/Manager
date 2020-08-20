<?php

namespace App\Action;

use App\Action\Traits\PaginationTrait;
use App\Action\Traits\SecurityTrait;
use App\Action\Traits\TwigTrait;
use App\Repository\ServerRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/servers", name="app_server_list")
 */
final class ListServersAction
{
    use PaginationTrait;
    use SecurityTrait;
    use TwigTrait;

    private ServerRepository $serverRepository;

    public function __construct(ServerRepository $serverRepository)
    {
        $this->serverRepository = $serverRepository;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessLoggedIn();

        $pager = $this->paginate($this->getQueryBuilder(), $request);

        return $this->renderPage('list-servers', 'app/server/list.html.twig', [
            'servers' => $pager->getCurrentPageResults(),
            'pager' => $pager
        ]);
    }

    private function getQueryBuilder(): QueryBuilder
    {
        return $this->serverRepository->createQueryBuilder('s')
            ->join('s.members', 'm')
            ->where('m.user = :user')
            ->setParameter('user', $this->security->getUser()->getId(), 'uuid_binary')
            ->orderBy('s.createdAt', 'DESC');
    }
}
