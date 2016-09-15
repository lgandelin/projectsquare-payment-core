<?php

namespace Webaccess\ProjectSquarePayment\Responses\Administrators;

use Webaccess\ProjectSquarePayment\Responses\Response;

class CreateAdministratorResponse extends Response
{
    const REPOSITORY_CREATION_FAILED = -1;
    const ADMINISTRATOR_LAST_NAME_REQUIRED = -2;
    const ADMINISTRATOR_FIRST_NAME_REQUIRED = -3;
    const ADMINISTRATOR_EMAIL_REQUIRED = -4;
    const ADMINISTRATOR_PASSWORD_REQUIRED = -5;
    const PLATFORM_ID_REQUIRED = -6;

    public $success;
    public $errorCode;
    public $administratorID;
}