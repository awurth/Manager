<?php

namespace App\Twig;

use App\Entity\ProjectMember;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AccessLevelLabelExtension extends AbstractExtension
{
    private const ACCESS_LEVELS_PROJECT_MEMBER = [
        ProjectMember::ACCESS_LEVEL_GUEST => 'project.member.access_level.guest',
        ProjectMember::ACCESS_LEVEL_MEMBER => 'project.member.access_level.member',
        ProjectMember::ACCESS_LEVEL_OWNER => 'project.member.access_level.owner'
    ];

    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('access_level_label', [$this, 'getAccessLevelLabel'])
        ];
    }

    public function getAccessLevelLabel($entity): ?string
    {
        $label = null;
        if ($entity instanceof ProjectMember) {
            $label = self::ACCESS_LEVELS_PROJECT_MEMBER[$entity->getAccessLevel()] ?? null;
        }

        return $label ? $this->translator->trans($label) : null;
    }
}
