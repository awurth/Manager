<?php

namespace App\Action\Admin;

use App\Action\Traits\FlashTrait;
use App\Action\Traits\RoutingTrait;
use App\Action\Traits\SecurityTrait;
use App\Action\Traits\TwigTrait;
use App\Entity\LinkType;
use App\Form\Model\Admin\CreateLinkType;
use App\Form\Type\Action\Admin\CreateLinkTypeType;
use Awurth\UploadBundle\Storage\StorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/link-types/new', name: 'app_admin_link_type_create')]
final class CreateLinkTypeAction extends AbstractAdminAction
{
    use FlashTrait;
    use RoutingTrait;
    use SecurityTrait;
    use TwigTrait;

    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $formFactory;
    private StorageInterface $uploader;

    public function __construct(
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        StorageInterface $uploader
    )
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->uploader = $uploader;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $model = new CreateLinkType();
        $form = $this->formFactory->create(CreateLinkTypeType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $linkType = LinkType::createFromAdminCreationForm($model, $this->uploader);

            $this->entityManager->persist($linkType);
            $this->entityManager->flush();

            $this->flash('success', 'flash.success.link_type.create');

            return $this->redirectToRoute('app_admin_link_type_list');
        }

        return $this->renderPage('admin-create-link-type', 'app/admin/create_link_type.html.twig', [
            'form' => $form->createView()
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        parent::configureBreadcrumbs();

        $this->breadcrumbs
            ->addRouteItem('breadcrumb.admin.link_type.list', 'app_admin_link_type_list')
            ->addItem('breadcrumb.admin.link_type.create');
    }
}
