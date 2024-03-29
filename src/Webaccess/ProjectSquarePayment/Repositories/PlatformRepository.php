<?php
namespace Webaccess\ProjectSquarePayment\Repositories;

use Webaccess\ProjectSquarePayment\Entities\Platform;

interface PlatformRepository
{
    public function getByID($platformID);

    public function getBySlug($platformSlug);

    public function getAll();

    public function persist(Platform $platform);

    public function deleteByID($platformID);

    public function updatePlatformNodeIdentifier($platformID, $nodeIdentifier);
}