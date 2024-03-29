<?php

namespace Webaccess\ProjectSquarePayment\Requests\Infrastructure;

use Webaccess\ProjectSquarePayment\Requests\Request;

class CreateInfrastructureRequest extends Request
{
    public $platformID;
    public $slug;
    public $administratorEmail;
    public $usersLimit;
}