<?php

namespace Webaccess\ProjectSquarePayment\Responses\Administrators;

use Webaccess\ProjectSquarePayment\Responses\Response;

class CreateAdministratorResponse extends Response
{
    const REPOSITORY_INSERTION_FAILED = -1;

    public $success;
    public $errorCode;
    public $administrator;
}