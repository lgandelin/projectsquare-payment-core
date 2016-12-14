<?php

namespace Webaccess\ProjectSquarePayment\Responses\Administrators;

use Webaccess\ProjectSquarePayment\Responses\Response;

class UpdateAdministratorResponse extends Response
{
    const ADMINISTRATOR_NOT_FOUND_ERROR = -28;
    const REPOSITORY_CREATION_FAILED = -29;
    const ADMINISTRATOR_EMAIL_REQUIRED = -30;
    const ADMINISTRATOR_LAST_NAME_REQUIRED = -31;
    const ADMINISTRATOR_FIRST_NAME_REQUIRED = -32;
    const ADMINISTRATOR_CITY_REQUIRED = -33;
    const ADMINISTRATOR_BILLING_ADDRESS_REQUIRED = -34;
    const ADMINISTRATOR_ZIPCODE_REQUIRED = -35;

    public $success;
    public $errorCode;
}