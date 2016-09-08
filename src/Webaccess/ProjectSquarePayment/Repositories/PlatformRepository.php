<?php
namespace Webaccess\ProjectSquarePayment\Repositories;

use Webaccess\ProjectSquarePayment\Entities\Platform;

interface PlatformRepository
{
    public function getByID($platformID);

    public function persist(Platform $platform);
}