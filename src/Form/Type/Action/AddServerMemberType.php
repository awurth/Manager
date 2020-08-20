<?php

namespace App\Form\Type\Action;

use App\Entity\Server;
use App\Entity\ServerMember;
use App\Form\Model\AddServerMember;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AddServerMemberType extends AbstractType
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
                'server.member.access_level.guest' => ServerMember::ACCESS_LEVEL_GUEST,
                'server.member.access_level.member' => ServerMember::ACCESS_LEVEL_MEMBER
            ]
        ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var AddServerMember $model */
            $model = $event->getData();

            $event->getForm()->add('user', ChoiceType::class, [
                'choices' => $this->getUserChoices($model->getServer()),
                'choice_label' => 'email',
                'choice_value' => 'id',
                'choice_translation_domain' => false,
                'placeholder' => 'form.label.add_server_member_user_placeholder'
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AddServerMember::class
        ]);
    }

    private function getUserChoices(Server $server): array
    {
        $serverMemberUserIds = [];
        foreach ($server->getMembers() as $serverMember) {
            $serverMemberUserIds[] = $serverMember->getUser()->getId()->getBytes();
        }

        $qb = $this->userRepository->createQueryBuilder('u');

        $qb
            ->where($qb->expr()->notIn('u.id', ':users'))
            ->setParameter('users', $serverMemberUserIds);

        return $qb->getQuery()->getResult();
    }
}
