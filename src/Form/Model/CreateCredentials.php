<?php

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class CreateCredentials
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    public $name;

    /**
     * @Assert\Length(max=255)
     */
    public $username;

    /**
     * @Assert\Email()
     * @Assert\Length(max=255)
     */
    public $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    public $password;

    /**
     * @Assert\Url()
     * @Assert\Length(max=255)
     */
    public $website;

    public $description;

    public $users;
}
