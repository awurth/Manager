<?php

namespace App\Action\Credentials;

use App\Action\Traits\PaginationTrait;
use App\Action\Traits\SecurityTrait;
use App\Action\Traits\TwigTrait;
use App\Repository\CredentialsRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/credentials", name="app_credentials_list")
 */
final class ListCredentialsAction
{
    use PaginationTrait;
    use SecurityTrait;
    use TwigTrait;

    private CredentialsRepository $credentialsRepository;

    public function __construct(CredentialsRepository $credentialsRepository)
    {
        $this->credentialsRepository = $credentialsRepository;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessLoggedIn();

        $pager = $this->paginate($this->getQueryBuilder(), $request);

        return $this->renderPage('list-credentials', 'app/credentials/list.html.twig', [
            'credentials' => $pager->getCurrentPageResults(),
            'pager' => $pager
        ]);
    }

    private function getQueryBuilder(): QueryBuilder
    {
        return $this->credentialsRepository->createQueryBuilder('c')
            ->join('c.credentialsUsers', 'cu')
            ->where('cu.user = :user')
            ->setParameter('user', $this->getUser()->getId(), 'uuid_binary')
            ->orderBy('c.createdAt', 'DESC');
    }
}
