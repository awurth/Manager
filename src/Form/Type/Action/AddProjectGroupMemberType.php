<?php

namespace App\Form\Type\Action;

use App\Entity\ProjectGroup;
use App\Entity\ProjectGroupMember;
use App\Form\Model\AddProjectGroupMember;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AddProjectGroupMemberType extends AbstractType
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
                'project_group.member.access_level.guest' => ProjectGroupMember::ACCESS_LEVEL_GUEST,
                'project_group.member.access_level.member' => ProjectGroupMember::ACCESS_LEVEL_MEMBER
            ]
        ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var AddProjectGroupMember $model */
            $model = $event->getData();

            $event->getForm()->add('user', ChoiceType::class, [
                'choices' => $this->getUserChoices($model->getProjectGroup()),
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
            'data_class' => AddProjectGroupMember::class
        ]);
    }

    private function getUserChoices(ProjectGroup $projectGroup): array
    {
        $projectGroupMemberUserIds = [];
        foreach ($projectGroup->getMembers() as $projectGroupMember) {
            $projectGroupMemberUserIds[] = $projectGroupMember->getUser()->getId()->getBytes();
        }

        $qb = $this->userRepository->createQueryBuilder('u');

        $qb
            ->where($qb->expr()->notIn('u.id', ':users'))
            ->setParameter('users', $projectGroupMemberUserIds);

        return $qb->getQuery()->getResult();
    }
}
