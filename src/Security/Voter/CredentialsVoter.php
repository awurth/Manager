<?php

namespace App\Security\Voter;

use App\Entity\Credentials;
use App\Entity\CredentialsUser;
use App\Repository\CredentialsUserRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class CredentialsVoter extends Voter
{
    private const ACCESS_LEVELS = [
        'USER' => CredentialsUser::ACCESS_LEVEL_USER,
        'OWNER' => CredentialsUser::ACCESS_LEVEL_OWNER
    ];

    private AuthorizationCheckerInterface $authorizationChecker;
    private CredentialsUserRepository $credentialsUserRepository;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        CredentialsUserRepository $credentialsUserRepository
    )
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->credentialsUserRepository = $credentialsUserRepository;
    }

    protected function supports($attribute, $subject): bool
    {
        return (array_key_exists($attribute, self::ACCESS_LEVELS) || in_array($attribute, ['DELETE', 'EDIT', 'VIEW']))
            && $subject instanceof Credentials;
    }

    /**
     * @param string         $attribute
     * @param Credentials    $credentials
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $credentials, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if (!$credentialsUser = $this->credentialsUserRepository->findOneBy(['user' => $user, 'credentials' => $credentials])) {
            return false;
        }

        if (($accessLevel = self::ACCESS_LEVELS[$attribute] ?? null) !== null) {
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
}
