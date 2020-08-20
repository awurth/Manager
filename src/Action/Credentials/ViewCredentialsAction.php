<?php

namespace App\Action\Credentials;

use App\Action\Traits\SecurityTrait;
use App\Action\Traits\TwigTrait;
use App\Entity\ValueObject\Id;
use App\Repository\CredentialsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/credentials/{id}", name="app_credentials_view")
 */
final class ViewCredentialsAction
{
    use SecurityTrait;
    use TwigTrait;

    private CredentialsRepository $credentialsRepository;

    public function __construct(CredentialsRepository $credentialsRepository)
    {
        $this->credentialsRepository = $credentialsRepository;
    }

    public function __invoke(string $id): Response
    {
        $this->denyAccessUnlessLoggedIn();

        $credentials = $this->credentialsRepository->get(Id::fromString($id));

        $this->denyAccessUnlessGranted('VIEW', $credentials);

        return $this->renderPage('view-credentials', 'app/credentials/view.html.twig', [
            'credentials' => $credentials
        ]);
    }
}
