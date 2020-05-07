<?php

namespace App\Form\Type\Action;

use App\Entity\ServerMember;
use App\Entity\User;
use App\Form\Model\AddServerMember;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddServerMemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('accessLevel', ChoiceType::class, [
            'choices' => [
                'server.member.access_level.guest' => ServerMember::ACCESS_LEVEL_GUEST,
                'server.member.access_level.member' => ServerMember::ACCESS_LEVEL_MEMBER
            ]
        ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event) {
            /** @var AddServerMember $model */
            $model = $event->getData();

            $event->getForm()->add('user', EntityType::class, [
                'class' => User::class,
                'query_builder' => static function (UserRepository $repository) use ($model) {
                    $serverMemberIds = [];
                    foreach ($model->getServer()->getMembers() as $serverMember) {
                        $serverMemberIds[] = $serverMember->getUser()->getId();
                    }

                    $qb = $repository->createQueryBuilder('u');
                    $qb->where($qb->expr()->notIn('u.id', $serverMemberIds));

                    return $qb;
                }
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AddServerMember::class
        ]);
    }
}
