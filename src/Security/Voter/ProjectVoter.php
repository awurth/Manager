<?php

namespace App\Security\Voter;

use App\Entity\Project;
use App\Entity\ProjectGroupMember;
use App\Entity\ProjectMember;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ProjectVoter extends Voter
{
    private const PROJECT_ACCESS_LEVELS = [
        'GUEST' => ProjectMember::ACCESS_LEVEL_GUEST,
        'MEMBER' => ProjectMember::ACCESS_LEVEL_MEMBER,
        'OWNER' => ProjectMember::ACCESS_LEVEL_OWNER
    ];

    private const PROJECT_GROUP_ACCESS_LEVELS = [
        'GUEST' => ProjectGroupMember::ACCESS_LEVEL_GUEST,
        'MEMBER' => ProjectGroupMember::ACCESS_LEVEL_MEMBER,
        'OWNER' => ProjectGroupMember::ACCESS_LEVEL_OWNER
    ];

    private AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    protected function supports($attribute, $subject): bool
    {
        $supportsAccessLevel = array_key_exists($attribute, self::PROJECT_ACCESS_LEVELS)
            || array_key_exists($attribute, self::PROJECT_GROUP_ACCESS_LEVELS)
            || in_array($attribute, ['DELETE', 'EDIT', 'VIEW']);

        return $supportsAccessLevel && $subject instanceof Project;
    }

    /**
     * @param string         $attribute
     * @param Project        $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $projectMember = $subject->getMemberByUser($user);
        $projectGroupMember = $subject->getProjectGroup()->getMemberByUser($user);

        if (!$projectMember && !$projectGroupMember) {
            return false;
        }

        if ($projectMember) {
            if ($accessLevel = self::PROJECT_ACCESS_LEVELS[$attribute] ?? null) {
                return $projectMember->getAccessLevel() >= $accessLevel;
            }
        } elseif ($accessLevel = self::PROJECT_GROUP_ACCESS_LEVELS[$attribute] ?? null) {
            return $projectGroupMember->getAccessLevel() >= $accessLevel;
        }

        switch ($attribute) {
            case 'DELETE':
            case 'EDIT':
                return $projectMember
                    ? $projectMember->getAccessLevel() >= ProjectMember::ACCESS_LEVEL_OWNER
                    : $projectGroupMember->getAccessLevel() >= ProjectGroupMember::ACCESS_LEVEL_OWNER;
            case 'VIEW':
                return $projectMember
                    ? $projectMember->getAccessLevel() >= ProjectMember::ACCESS_LEVEL_MEMBER
                    : $projectGroupMember->getAccessLevel() >= ProjectGroupMember::ACCESS_LEVEL_OWNER;
        }

        return false;
    }
}
