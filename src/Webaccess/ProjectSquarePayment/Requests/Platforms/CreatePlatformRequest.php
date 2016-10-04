<?php

namespace Webaccess\ProjectSquarePayment\Requests\Platforms;

use Webaccess\ProjectSquarePayment\Requests\Request;

class CreatePlatformRequest extends Request
{
    public $name;
    public $slug;
    public $usersCount;
    public $platformMonthlyCost;
    public $userMonthlyCost;
}