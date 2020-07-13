<?php

namespace App\Form\Type\Action;

use App\Entity\ProjectGroupMember;
use App\Entity\User;
use App\Form\Model\AddProjectGroupMember;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddProjectGroupMemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('accessLevel', ChoiceType::class, [
            'choices' => [
                'project_group.member.access_level.guest' => ProjectGroupMember::ACCESS_LEVEL_GUEST,
                'project_group.member.access_level.member' => ProjectGroupMember::ACCESS_LEVEL_MEMBER
            ]
        ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event) {
            /** @var AddProjectGroupMember $model */
            $model = $event->getData();

            $event->getForm()->add('user', EntityType::class, [
                'class' => User::class,
                'choice_value' => 'id',
                'placeholder' => 'form.label.add_project_group_member_user_placeholder',
                'query_builder' => static function (UserRepository $repository) use ($model) {
                    $projectGroupMemberIds = [];
                    foreach ($model->getProjectGroup()->getMembers() as $projectGroupMember) {
                        $projectGroupMemberIds[] = $projectGroupMember->getUser()->getId();
                    }

                    $qb = $repository->createQueryBuilder('u');
                    $qb->where($qb->expr()->notIn('u.id', $projectGroupMemberIds));

                    return $qb;
                }
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AddProjectGroupMember::class
        ]);
    }
}
