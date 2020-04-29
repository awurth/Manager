<?php

namespace App\Action\Credentials;

use App\Action\AbstractAction;
use App\Entity\Credentials;
use App\Entity\CredentialsUser;
use App\Form\Type\CreateCredentialsType;
use App\Form\Model\CreateCredentials;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/credentials/new", name="app_credentials_create")
 */
class CreateCredentialsAction extends AbstractAction
{
    private $entityManager;
    private $flashBag;
    private $formFactory;

    public function __construct(EntityManagerInterface $entityManager, FlashBagInterface $flashBag, FormFactoryInterface $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
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
            $credentials = (new Credentials($model->name, $model->password))
                ->setUsername($model->username)
                ->setEmail($model->email)
                ->setWebsite($model->website)
                ->setDescription($model->description)
                ->addCredentialsUser(
                    (new CredentialsUser())
                        ->setUser($this->getUser())
                        ->setAccessLevel(CredentialsUser::ACCESS_LEVEL_OWNER)
                );

            foreach ($model->users as $user) {
                $credentials->addCredentialsUser(
                    (new CredentialsUser())
                        ->setUser($user)
                        ->setAccessLevel(CredentialsUser::ACCESS_LEVEL_USER)
                );
            }

            $this->entityManager->persist($credentials);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'flash.success.credentials.create');

            return $this->redirectToRoute('app_credentials_list');
        }

        return $this->renderPage('create-credentials', 'app/credentials/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
