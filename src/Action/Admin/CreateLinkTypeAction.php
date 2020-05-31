<?php

namespace App\Action\Admin;

use App\Action\RoutingTrait;
use App\Action\SecurityTrait;
use App\Action\TwigTrait;
use App\Entity\LinkType;
use App\Form\Model\Admin\CreateLinkType;
use App\Form\Type\Action\CreateLinkTypeType;
use Awurth\UploadBundle\Storage\StorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/link-types/new", name="app_admin_link_type_create")
 */
class CreateLinkTypeAction extends AbstractAdminAction
{
    use RoutingTrait;
    use SecurityTrait;
    use TwigTrait;

    private $entityManager;
    private $flashBag;
    private $formFactory;
    private $uploader;

    public function __construct(
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        FormFactoryInterface $formFactory,
        StorageInterface $uploader
    )
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
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
            $linkType = (new LinkType($model->name))
                ->setColor($model->color)
                ->setUriPrefix($model->uriPrefix);

            if ($model->iconFile) {
                $this->uploader->upload($model->iconFile, $linkType, 'link_type_icon');
            }

            $this->entityManager->persist($linkType);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'flash.success.link_type.create');

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
