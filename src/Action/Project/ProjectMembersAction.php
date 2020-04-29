<?php

namespace App\Action\Project;

use App\Action\AbstractAction;
use App\Entity\ProjectMember;
use App\Form\Model\AddProjectMember;
use App\Form\Type\AddProjectMemberType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/project/{slug}/members", name="app_project_members")
 */
class ProjectMembersAction extends AbstractAction
{
    private $entityManager;
    private $flashBag;
    private $formFactory;
    private $projectRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        FormFactoryInterface $formFactory,
        ProjectRepository $projectRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->formFactory = $formFactory;
        $this->projectRepository = $projectRepository;
    }

    public function __invoke(Request $request, string $slug): Response
    {
        $this->denyAccessUnlessLoggedIn();

        $project = $this->projectRepository->findOneBy(['slug' => $slug]);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        $this->denyAccessUnlessGranted('MEMBER', $project);

        $model = new AddProjectMember($project);
        $form = $this->formFactory->create(AddProjectMemberType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->addMember(
                (new ProjectMember())
                    ->setUser($model->user)
                    ->setAccessLevel($model->accessLevel)
            );

            $this->entityManager->persist($project);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'flash.success.project.member.add');

            return $this->redirectToRoute('app_project_view', ['slug' => $project->getSlug()]);
        }

        return $this->renderPage('project-members', 'app/project/members.html.twig', [
            'form' => $form->createView(),
            'project' => $project
        ]);
    }
}
