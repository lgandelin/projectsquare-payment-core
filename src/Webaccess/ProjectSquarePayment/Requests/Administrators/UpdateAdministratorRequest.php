<?php

namespace Webaccess\ProjectSquarePayment\Requests\Administrators;

use Webaccess\ProjectSquarePayment\Requests\Request;

class UpdateAdministratorRequest extends Request
{
    public $administratorID;
    public $email;
    public $password;
    public $lastName;
    public $firstName;
    public $billingAddress;
    public $zipcode;
    public $city;
}