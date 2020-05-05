<?php

namespace App\Security\Voter;

use App\Entity\Server;
use App\Entity\ServerMember;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ServerVoter extends Voter
{
    private const ACCESS_LEVELS = [
        'GUEST' => ServerMember::ACCESS_LEVEL_GUEST,
        'MEMBER' => ServerMember::ACCESS_LEVEL_MEMBER,
        'OWNER' => ServerMember::ACCESS_LEVEL_OWNER
    ];

    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    protected function supports($attribute, $subject): bool
    {
        return (array_key_exists($attribute, self::ACCESS_LEVELS) || 'DELETE')
            && $subject instanceof Server;
    }

    /**
     * @param string         $attribute
     * @param Server         $subject
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

        if (!$serverMember = $this->getServerMember($user, $subject)) {
            return false;
        }

        if ('DELETE' === $attribute) {
            return $serverMember->getAccessLevel() >= ServerMember::ACCESS_LEVEL_OWNER;
        }

        return $serverMember->getAccessLevel() >= self::ACCESS_LEVELS[$attribute];
    }

    private function getServerMember(UserInterface $user, Server $server): ?ServerMember
    {
        foreach ($server->getMembers() as $serverMember) {
            if ($serverMember->getUser() === $user) {
                return $serverMember;
            }
        }

        return null;
    }
}
