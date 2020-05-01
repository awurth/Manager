<?php

namespace App\Action\ProjectGroup;

use App\Action\AbstractAction;
use App\Action\RoutingTrait;
use App\Action\SecurityTrait;
use App\Action\TwigTrait;
use App\Form\Type\EditProjectGroupType;
use App\Form\Model\EditProjectGroup;
use App\Repository\ProjectGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/group/{slug}/edit", name="app_project_group_edit")
 */
class EditProjectGroupAction extends AbstractAction
{
    use RoutingTrait;
    use SecurityTrait;
    use TwigTrait;

    private $entityManager;
    private $flashBag;
    private $formFactory;
    private $projectGroupRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        FormFactoryInterface $formFactory,
        ProjectGroupRepository $projectGroupRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->formFactory = $formFactory;
        $this->projectGroupRepository = $projectGroupRepository;
    }

    public function __invoke(Request $request, string $slug): Response
    {
        $this->denyAccessUnlessLoggedIn();

        $group = $this->projectGroupRepository->findOneBy(['slug' => $slug]);

        if (!$group) {
            throw $this->createNotFoundException('Project group not found');
        }

        // $this->denyAccessUnlessGranted('MEMBER', $group);

        $model = new EditProjectGroup($group);
        $form = $this->formFactory->create(EditProjectGroupType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $group
                ->setCustomer($model->customer)
                ->setDescription($model->description)
                ->setName($model->name);

            $this->entityManager->persist($group);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'flash.success.project_group.edit');

            return $this->redirectToRoute('app_project_group_view', ['slug' => $group->getSlug()]);
        }

        return $this->renderPage('edit-project-group', 'app/project_group/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
