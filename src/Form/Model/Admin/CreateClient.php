<?php

namespace App\Form\Model\Admin;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateClient
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    public $name;

    /**
     * @Assert\Length(max=255)
     */
    public $address;

    /**
     * @Assert\Length(max=255)
     */
    public $postcode;

    /**
     * @Assert\Length(max=255)
     */
    public $city;

    /**
     * @Assert\Length(max=255)
     */
    public $phone;

    /**
     * @Assert\Email()
     * @Assert\Length(max=255)
     */
    public $email;
}
