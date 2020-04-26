<?php

namespace App\Security\Voter;

use App\Entity\ProjectGroup;
use App\Entity\ProjectGroupMember;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ProjectGroupVoter extends Voter
{
    private const ACCESS_LEVELS = [
        'GUEST' => ProjectGroupMember::ACCESS_LEVEL_GUEST,
        'MEMBER' => ProjectGroupMember::ACCESS_LEVEL_MEMBER,
        'OWNER' => ProjectGroupMember::ACCESS_LEVEL_OWNER
    ];

    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    protected function supports($attribute, $subject): bool
    {
        return (array_key_exists($attribute, self::ACCESS_LEVELS) || 'DELETE')
            && $subject instanceof ProjectGroup;
    }

    /**
     * @param string         $attribute
     * @param ProjectGroup   $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if (!$groupMember = $this->getGroupMember($user, $subject)) {
            return false;
        }

        if ('DELETE' === $attribute) {
            return $groupMember->getAccessLevel() >= ProjectGroupMember::ACCESS_LEVEL_OWNER;
        }

        return $groupMember->getAccessLevel() >= self::ACCESS_LEVELS[$attribute];
    }

    private function getGroupMember(UserInterface $user, ProjectGroup $group): ?ProjectGroupMember
    {
        foreach ($group->getMembers() as $groupMember) {
            if ($groupMember->getUser() === $user) {
                return $groupMember;
            }
        }

        return null;
    }
}
