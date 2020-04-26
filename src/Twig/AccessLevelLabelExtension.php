<?php

namespace App\Twig;

use App\Entity\ProjectGroupMember;
use App\Entity\ProjectMember;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AccessLevelLabelExtension extends AbstractExtension
{
    private const ACCESS_LEVELS_PROJECT_GROUP_MEMBER = [
        ProjectGroupMember::ACCESS_LEVEL_GUEST => 'project_group.member.access_level.guest',
        ProjectGroupMember::ACCESS_LEVEL_MEMBER => 'project_group.member.access_level.member',
        ProjectGroupMember::ACCESS_LEVEL_OWNER => 'project_group.member.access_level.owner'
    ];

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
        if ($entity instanceof ProjectGroupMember) {
            $label = self::ACCESS_LEVELS_PROJECT_GROUP_MEMBER[$entity->getAccessLevel()] ?? null;
        } elseif ($entity instanceof ProjectMember) {
            $label = self::ACCESS_LEVELS_PROJECT_MEMBER[$entity->getAccessLevel()] ?? null;
        }

        return $label ? $this->translator->trans($label) : null;
    }
}
