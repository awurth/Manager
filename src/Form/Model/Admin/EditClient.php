<?php

namespace App\Form\Model\Admin;

use App\Entity\Client;
use Symfony\Component\Validator\Constraints as Assert;

final class EditClient
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

    public function __construct(Client $client)
    {
        $this->name = $client->getName();
        $this->address = $client->getAddress();
        $this->postcode = $client->getPostcode();
        $this->city = $client->getCity();
        $this->phone = $client->getPhone();
        $this->email = $client->getEmail();
    }
}
