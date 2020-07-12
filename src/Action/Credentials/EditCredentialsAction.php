<?php

namespace App\Action\Credentials;

use App\Action\Traits\FlashTrait;
use App\Action\Traits\RoutingTrait;
use App\Action\Traits\SecurityTrait;
use App\Action\Traits\TwigTrait;
use App\Form\Type\Action\EditCredentialsType;
use App\Form\Model\EditCredentials;
use App\Repository\CredentialsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/credentials/{id}/edit", name="app_credentials_edit")
 */
class EditCredentialsAction
{
    use FlashTrait;
    use RoutingTrait;
    use SecurityTrait;
    use TwigTrait;

    private CredentialsRepository $credentialsRepository;
    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $formFactory;

    public function __construct(
        CredentialsRepository $credentialsRepository,
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory
    )
    {
        $this->credentialsRepository = $credentialsRepository;
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
    }

    public function __invoke(Request $request, string $id): Response
    {
        $this->denyAccessUnlessLoggedIn();

        $credentials = $this->credentialsRepository->find($id);

        if (!$credentials) {
            throw new NotFoundHttpException('Credentials not found');
        }

        $this->denyAccessUnlessGranted('EDIT', $credentials);

        $model = new EditCredentials($credentials);
        $form = $this->formFactory->create(EditCredentialsType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $credentials->updateFromEditionForm($model, $this->getUser());

            $this->entityManager->persist($credentials);
            $this->entityManager->flush();

            $this->flash('success', 'flash.success.credentials.edit');

            return $this->redirectToRoute('app_credentials_list');
        }

        return $this->renderPage('edit-credentials', 'app/credentials/edit.html.twig', [
            'credentials' => $credentials,
            'form' => $form->createView()
        ]);
    }
}
