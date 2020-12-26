<?php

namespace App\Security\Voter;

use App\Entity\Server;
use App\Entity\ServerMember;
use App\Repository\ServerMemberRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class ServerVoter extends Voter
{
    private const ACCESS_LEVELS = [
        'GUEST' => ServerMember::ACCESS_LEVEL_GUEST,
        'MEMBER' => ServerMember::ACCESS_LEVEL_MEMBER,
        'OWNER' => ServerMember::ACCESS_LEVEL_OWNER
    ];

    private AuthorizationCheckerInterface $authorizationChecker;
    private ServerMemberRepository $serverMemberRepository;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        ServerMemberRepository $serverMemberRepository
    )
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->serverMemberRepository = $serverMemberRepository;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return (array_key_exists($attribute, self::ACCESS_LEVELS) || in_array($attribute, ['DELETE', 'EDIT', 'VIEW']))
            && $subject instanceof Server;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user) {
            return false;
        }

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if (!$serverMember = $this->serverMemberRepository->findOneBy(['user' => $user, 'server' => $subject])) {
            return false;
        }

        if (($accessLevel = self::ACCESS_LEVELS[$attribute] ?? null) !== null) {
            return $serverMember->getAccessLevel() >= $accessLevel;
        }

        switch ($attribute) {
            case 'DELETE':
            case 'EDIT':
                return $serverMember->getAccessLevel() >= ServerMember::ACCESS_LEVEL_OWNER;
            case 'VIEW':
                return $serverMember->getAccessLevel() >= ServerMember::ACCESS_LEVEL_MEMBER;
        }

        return false;
    }
}
