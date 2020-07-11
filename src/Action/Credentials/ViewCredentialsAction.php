<?php

namespace App\Action\Credentials;

use App\Action\Traits\SecurityTrait;
use App\Action\Traits\TwigTrait;
use App\Repository\CredentialsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/credentials/{id}", name="app_credentials_view")
 */
class ViewCredentialsAction
{
    use SecurityTrait;
    use TwigTrait;

    private $credentialsRepository;

    public function __construct(CredentialsRepository $credentialsRepository)
    {
        $this->credentialsRepository = $credentialsRepository;
    }

    public function __invoke(string $id): Response
    {
        $this->denyAccessUnlessLoggedIn();

        $credentials = $this->credentialsRepository->find($id);

        if (!$credentials) {
            throw new NotFoundHttpException('Credentials not found');
        }

        $this->denyAccessUnlessGranted('VIEW', $credentials);

        return $this->renderPage('view-credentials', 'app/credentials/view.html.twig', [
            'credentials' => $credentials
        ]);
    }
}
