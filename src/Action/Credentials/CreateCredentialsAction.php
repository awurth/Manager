<?php

namespace App\Action\Credentials;

use App\Action\FlashTrait;
use App\Action\RoutingTrait;
use App\Action\SecurityTrait;
use App\Action\TwigTrait;
use App\Entity\Credentials;
use App\Form\Type\Action\CreateCredentialsType;
use App\Form\Model\CreateCredentials;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/credentials/new", name="app_credentials_create")
 */
class CreateCredentialsAction
{
    use FlashTrait;
    use RoutingTrait;
    use SecurityTrait;
    use TwigTrait;

    private $entityManager;
    private $formFactory;

    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_CREDENTIALS_CREATE');

        $model = new CreateCredentials();
        $form = $this->formFactory->create(CreateCredentialsType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $credentials = Credentials::createFromCreationForm($model, $this->getUser());

            $this->entityManager->persist($credentials);
            $this->entityManager->flush();

            $this->flash('success', 'flash.success.credentials.create');

            return $this->redirectToRoute('app_credentials_list');
        }

        return $this->renderPage('create-credentials', 'app/credentials/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
