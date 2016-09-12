<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Platforms;

use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;

class GetPlatformUsageAmountInteractor
{
    private $platformRepository;

    public function __construct(PlatformRepository $platformRepository)
    {
        $this->platformRepository = $platformRepository;
    }

    public function getMonthlyCost($platformID)
    {
        $platform = $this->platformRepository->getByID($platformID);

        return $platform->getFixedMonthlyCost() + $platform->getUsersCount() * $platform->getUserMonthlyCost();
    }

    public function getDailyCost($platformID)
    {
        return $this->getMonthlyCost($platformID) / date('t');
    }
}