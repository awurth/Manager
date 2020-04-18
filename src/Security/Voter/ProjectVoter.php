<?php

namespace App\Security\Voter;

use App\Entity\Project;
use App\Entity\ProjectMember;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ProjectVoter extends Voter
{
    private const ACCESS_LEVELS = [
        'GUEST' => ProjectMember::ACCESS_LEVEL_GUEST,
        'MEMBER' => ProjectMember::ACCESS_LEVEL_MEMBER,
        'OWNER' => ProjectMember::ACCESS_LEVEL_OWNER
    ];

    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    protected function supports($attribute, $subject): bool
    {
        return array_key_exists($attribute, self::ACCESS_LEVELS)
            && $subject instanceof Project;
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
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if (!$projectMember = $this->getProjectMember($user, $subject)) {
            return false;
        }

        return $projectMember->getAccessLevel() >= self::ACCESS_LEVELS[$attribute];
    }

    private function getProjectMember(UserInterface $user, Project $project): ?ProjectMember
    {
        foreach ($project->getMembers() as $projectMember) {
            if ($projectMember->getUser() === $user) {
                return $projectMember;
            }
        }

        return null;
    }
}
