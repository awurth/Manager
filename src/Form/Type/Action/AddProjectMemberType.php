<?php

namespace App\Form\Type\Action;

use App\Entity\ProjectMember;
use App\Entity\User;
use App\Form\Model\AddProjectMember;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddProjectMemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('accessLevel', ChoiceType::class, [
            'choices' => [
                'project.member.access_level.guest' => ProjectMember::ACCESS_LEVEL_GUEST,
                'project.member.access_level.member' => ProjectMember::ACCESS_LEVEL_MEMBER
            ]
        ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event) {
            /** @var AddProjectMember $model */
            $model = $event->getData();

            $event->getForm()->add('user', EntityType::class, [
                'class' => User::class,
                'choice_value' => 'id',
                'query_builder' => static function (UserRepository $repository) use ($model) {
                    $projectMemberIds = [];
                    foreach ($model->getProject()->getMembers() as $projectMember) {
                        $projectMemberIds[] = $projectMember->getUser()->getId();
                    }

                    $qb = $repository->createQueryBuilder('u');
                    $qb->where($qb->expr()->notIn('u.id', $projectMemberIds));

                    return $qb;
                }
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AddProjectMember::class
        ]);
    }
}
