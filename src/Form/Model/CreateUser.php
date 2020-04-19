<?php

namespace App\Form\Model;

use App\Validator\UniqueUserEmail;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordStrength;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueUserEmail()
 */
class CreateUser
{
    public const VALID_ROLES = [
        'ROLE_ADMIN',
        'ROLE_USER'
    ];

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $email;

    /**
     * @Assert\NotBlank()
     * @PasswordStrength(4)
     */
    public $plainPassword;

    /**
     * @Assert\NotBlank()
     */
    public $gender;

    /**
     * @Assert\Length(max=255)
     */
    public $firstname;

    /**
     * @Assert\Length(max=255)
     */
    public $lastname;

    /**
     * @Assert\NotBlank()
     * @Assert\Choice(choices=CreateUser::VALID_ROLES)
     */
    public $role;
}
