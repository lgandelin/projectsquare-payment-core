<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Platforms;

use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;

class GetPlatformUsageAmountInteractor
{
    private $platformRepository;

    /**
     * @param PlatformRepository $platformRepository
     */
    public function __construct(PlatformRepository $platformRepository)
    {
        $this->platformRepository = $platformRepository;
    }

    /**
     * @param $platformID
     * @return float
     */
    public function getMonthlyCost($platformID)
    {
        $platform = $this->platformRepository->getByID($platformID);
        return $platform->getPlatformMonthlyCost() + $platform->getUsersCount() * $platform->getUserMonthlyCost();
    }

    /**
     * @param $platformID
     * @return float
     */
    public function getDailyCost($platformID)
    {
        return $this->getMonthlyCost($platformID) / date('t');
    }
}