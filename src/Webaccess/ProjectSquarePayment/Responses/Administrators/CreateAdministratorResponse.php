<?php

namespace Webaccess\ProjectSquarePayment\Responses\Administrators;

use Webaccess\ProjectSquarePayment\Responses\Response;

class CreateAdministratorResponse extends Response
{
    const REPOSITORY_CREATION_FAILED = -7;
    const ADMINISTRATOR_LAST_NAME_REQUIRED = -8;
    const ADMINISTRATOR_FIRST_NAME_REQUIRED = -9;
    const ADMINISTRATOR_EMAIL_REQUIRED = -10;
    const ADMINISTRATOR_PASSWORD_REQUIRED = -11;
    const PLATFORM_ID_REQUIRED = -12;
    const ADMINISTRATOR_CITY_REQUIRED = -13;
    const ADMINISTRATOR_BILLING_ADDRESS_REQUIRED = -14;
    const ADMINISTRATOR_ZIPCODE_REQUIRED = -15;

    public $success;
    public $errorCode;
    public $administratorID;
}