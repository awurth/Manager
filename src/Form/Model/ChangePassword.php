<?php

namespace App\Form\Model;

use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordStrength;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePassword
{
    /**
     * @Assert\NotBlank()
     * @SecurityAssert\UserPassword()
     */
    public $currentPassword;

    /**
     * @Assert\NotBlank()
     * @PasswordStrength(4)
     */
    public $newPassword;
}
