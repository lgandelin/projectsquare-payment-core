<?php

namespace Webaccess\ProjectSquarePayment\Requests\Administrators;

use Webaccess\ProjectSquarePayment\Requests\Request;

class CreateAdministratorRequest extends Request
{
    public $email;
    public $password;
    public $lastName;
    public $firstName;
    public $billingAddress;
    public $zipCode;
    public $city;
    public $platformID;
}