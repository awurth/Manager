<?php

namespace App\Form\Model;

final class GeneratePassword
{
    public $uppercase = true;
    public $lowercase = true;
    public $numbers = true;
    public $symbols = true;
    public $length = 10;
}
