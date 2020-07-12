<?php

namespace App\Form\Model;

use App\Entity\User;
use App\Validator\UniqueUserEmail;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueUserEmail()
 */
class EditProfile
{
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    public $firstname;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    public $lastname;

    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->email = $user->getEmail();
        $this->firstname = $user->getFirstname();
        $this->lastname = $user->getLastname();
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
