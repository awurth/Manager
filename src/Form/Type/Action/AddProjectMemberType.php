<?php

namespace App\Form\Type\Action;

use App\Entity\Project;
use App\Entity\ProjectMember;
use App\Form\Model\AddProjectMember;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AddProjectMemberType extends AbstractType
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('accessLevel', ChoiceType::class, [
            'choices' => [
                'project.member.access_level.guest' => ProjectMember::ACCESS_LEVEL_GUEST,
                'project.member.access_level.member' => ProjectMember::ACCESS_LEVEL_MEMBER
            ]
        ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var AddProjectMember $model */
            $model = $event->getData();

            $event->getForm()->add('user', ChoiceType::class, [
                'choices' => $this->getUserChoices($model->getProject()),
                'choice_label' => 'email',
                'choice_value' => 'id',
                'choice_translation_domain' => false,
                'placeholder' => 'form.label.add_project_group_member_user_placeholder'
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AddProjectMember::class
        ]);
    }

    private function getUserChoices(Project $project): array
    {
        $projectMemberUsers = [];
        foreach ($project->getMembers() as $projectMember) {
            $projectMemberUsers[] = $projectMember->getUser();
        }

        $qb = $this->userRepository->createQueryBuilder('u');

        $qb
            ->where($qb->expr()->notIn('u', ':users'))
            ->setParameter('users', $projectMemberUsers);

        return $qb->getQuery()->getResult();
    }
}
