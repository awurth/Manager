<?php

namespace App\Form\Model;

use App\Entity\Credentials;
use Symfony\Component\Validator\Constraints as Assert;

final class EditCredentials
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

    public function __construct(Credentials $credentials)
    {
        $this->name = $credentials->getName();
        $this->username = $credentials->getUsername();
        $this->email = $credentials->getEmail();
        $this->password = $credentials->getPassword();
        $this->website = $credentials->getWebsite();
        $this->description = $credentials->getDescription();

        $this->users = [];
        foreach ($credentials->getCredentialsUsers() as $credentialsUser) {
            $this->users[] = $credentialsUser->getUser();
        }
    }
}
