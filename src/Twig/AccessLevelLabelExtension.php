<?php

namespace App\Twig;

use App\Entity\ProjectGroupMember;
use App\Entity\ProjectMember;
use App\Entity\ServerMember;
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

    private const ACCESS_LEVELS_SERVER_MEMBER = [
        ServerMember::ACCESS_LEVEL_GUEST => 'server.member.access_level.guest',
        ServerMember::ACCESS_LEVEL_MEMBER => 'server.member.access_level.member',
        ServerMember::ACCESS_LEVEL_OWNER => 'server.member.access_level.owner'
    ];

    private TranslatorInterface $translator;

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

    public function getAccessLevelLabel(object $entity): ?string
    {
        $label = null;
        if ($entity instanceof ProjectGroupMember) {
            $label = self::ACCESS_LEVELS_PROJECT_GROUP_MEMBER[$entity->getAccessLevel()] ?? null;
        } elseif ($entity instanceof ProjectMember) {
            $label = self::ACCESS_LEVELS_PROJECT_MEMBER[$entity->getAccessLevel()] ?? null;
        } elseif ($entity instanceof ServerMember) {
            $label = self::ACCESS_LEVELS_SERVER_MEMBER[$entity->getAccessLevel()] ?? null;
        }

        return $label ? $this->translator->trans($label) : null;
    }
}
