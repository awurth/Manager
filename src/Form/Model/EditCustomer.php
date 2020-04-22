<?php

namespace App\Form\Model;

use App\Entity\Customer;
use Symfony\Component\Validator\Constraints as Assert;

class EditCustomer
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

    public function __construct(Customer $customer)
    {
        $this->name = $customer->getName();
        $this->address = $customer->getAddress();
        $this->postcode = $customer->getPostcode();
        $this->city = $customer->getCity();
        $this->phone = $customer->getPhone();
        $this->email = $customer->getEmail();
    }
}
