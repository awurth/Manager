<?php

namespace App\Security\Voter;

use App\Entity\ProjectGroup;
use App\Entity\ProjectGroupMember;
use App\Entity\User;
use App\Repository\ProjectGroupMemberRepository;
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

    private AuthorizationCheckerInterface $authorizationChecker;
    private ProjectGroupMemberRepository $projectGroupMemberRepository;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        ProjectGroupMemberRepository $projectGroupMemberRepository
    )
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->projectGroupMemberRepository = $projectGroupMemberRepository;
    }

    protected function supports($attribute, $subject): bool
    {
        return (array_key_exists($attribute, self::ACCESS_LEVELS) || in_array($attribute, ['DELETE', 'EDIT', 'VIEW']))
            && $subject instanceof ProjectGroup;
    }

    /**
     * @param string         $attribute
     * @param ProjectGroup   $projectGroup
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $projectGroup, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if (!$groupMember = $this->projectGroupMemberRepository->findOneBy(['user' => $user, 'projectGroup' => $projectGroup])) {
            return false;
        }

        if (($accessLevel = self::ACCESS_LEVELS[$attribute] ?? null) !== null) {
            return $groupMember->getAccessLevel() >= $accessLevel;
        }

        switch ($attribute) {
            case 'DELETE':
            case 'EDIT':
                return $groupMember->getAccessLevel() >= ProjectGroupMember::ACCESS_LEVEL_OWNER;
            case 'VIEW':
                return $groupMember->getAccessLevel() >= ProjectGroupMember::ACCESS_LEVEL_MEMBER;
        }

        if ('DELETE' === $attribute) {
            return $groupMember->getAccessLevel() >= ProjectGroupMember::ACCESS_LEVEL_OWNER;
        }

        return false;
    }
}
