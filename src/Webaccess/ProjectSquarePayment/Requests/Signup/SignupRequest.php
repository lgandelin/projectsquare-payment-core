<?php

namespace Webaccess\ProjectSquarePayment\Requests\Signup;

use Webaccess\ProjectSquarePayment\Requests\Request;

class SignupRequest extends Request
{
    public $platformName;
    public $platformSlug;
    public $platformUsersCount;
    public $administratorEmail;
    public $administratorPassword;
    public $administratorLastName;
    public $administratorFirstName;
    public $administratorBillingAddress;
    public $administratorZipcode;
    public $administratorCity;
}
