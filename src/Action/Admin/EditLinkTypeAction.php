<?php

namespace App\Action\Admin;

use App\Action\FlashTrait;
use App\Action\RoutingTrait;
use App\Action\SecurityTrait;
use App\Action\TwigTrait;
use App\Form\Model\Admin\EditLinkType;
use App\Form\Type\Action\Admin\EditLinkTypeType;
use App\Repository\LinkTypeRepository;
use Awurth\UploadBundle\Storage\StorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/link-type/{id}/edit", requirements={"id": "\d+"}, name="app_admin_link_type_edit")
 */
class EditLinkTypeAction extends AbstractAdminAction
{
    use FlashTrait;
    use RoutingTrait;
    use SecurityTrait;
    use TwigTrait;

    private $entityManager;
    private $formFactory;
    private $linkTypeRepository;
    private $uploader;

    public function __construct(
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        LinkTypeRepository $linkTypeRepository,
        StorageInterface $uploader
    )
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->linkTypeRepository = $linkTypeRepository;
        $this->uploader = $uploader;
    }

    public function __invoke(Request $request, int $id): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $linkType = $this->linkTypeRepository->find($id);

        if (!$linkType) {
            throw new NotFoundHttpException('Link type not found');
        }

        $model = new EditLinkType($linkType);
        $form = $this->formFactory->create(EditLinkTypeType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $linkType
                ->setName($model->name)
                ->setColor($model->color)
                ->setUriPrefix($model->uriPrefix);

            if ($model->iconFile) {
                $this->uploader->upload($model->iconFile, $linkType, 'link_type_icon');
            }

            $this->entityManager->persist($linkType);
            $this->entityManager->flush();

            $this->flash('success', 'flash.success.link_type.edit');

            return $this->redirectToRoute('app_admin_link_type_list');
        }

        $this->breadcrumbs->addItem($linkType->getName(), '', [], false);

        return $this->renderPage('admin-edit-link-type', 'app/admin/edit_link_type.html.twig', [
            'form' => $form->createView()
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        parent::configureBreadcrumbs();

        $this->breadcrumbs->addRouteItem('breadcrumb.admin.link_type.list', 'app_admin_link_type_list');
    }
}
