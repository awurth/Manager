<?php

namespace App\Security\Voter;

use App\Entity\Credentials;
use App\Entity\CredentialsUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CredentialsVoter extends Voter
{
    private const ACCESS_LEVELS = [
        'USER' => CredentialsUser::ACCESS_LEVEL_USER,
        'OWNER' => CredentialsUser::ACCESS_LEVEL_OWNER
    ];

    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    protected function supports($attribute, $subject): bool
    {
        return (array_key_exists($attribute, self::ACCESS_LEVELS) || in_array($attribute, ['DELETE', 'EDIT', 'VIEW']))
            && $subject instanceof Credentials;
    }

    /**
     * @param string         $attribute
     * @param Credentials    $subject
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

        if (!$credentialsUser = $this->getCredentialsUser($user, $subject)) {
            return false;
        }

        if ($accessLevel = self::ACCESS_LEVELS[$attribute] ?? null) {
            return $credentialsUser->getAccessLevel() >= $accessLevel;
        }

        switch ($attribute) {
            case 'DELETE':
            case 'EDIT':
                return $credentialsUser->getAccessLevel() >= CredentialsUser::ACCESS_LEVEL_OWNER;
            case 'VIEW':
                return $credentialsUser->getAccessLevel() >= CredentialsUser::ACCESS_LEVEL_USER;
        }

        return false;
    }

    private function getCredentialsUser(UserInterface $user, Credentials $credentials): ?CredentialsUser
    {
        foreach ($credentials->getCredentialsUsers() as $credentialsUser) {
            if ($credentialsUser->getUser() === $user) {
                return $credentialsUser;
            }
        }

        return null;
    }
}
