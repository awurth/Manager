<?php

namespace App\Form\Filter;

use App\Entity\ProjectType as ProjectTypeEntity;
use App\Repository\ProjectTypeRepository;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    private $projectTypeRepository;

    public function __construct(ProjectTypeRepository $projectTypeRepository)
    {
        $this->projectTypeRepository = $projectTypeRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', Filter\EntityFilterType::class, [
                'class' => ProjectTypeEntity::class,
                'choices' => $this->projectTypeRepository->findBy([], ['name' => 'ASC'])
            ])
            ->add('name', Filter\TextFilterType::class)
            ->add('description', Filter\TextFilterType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'method' => 'GET'
        ]);
    }
}
