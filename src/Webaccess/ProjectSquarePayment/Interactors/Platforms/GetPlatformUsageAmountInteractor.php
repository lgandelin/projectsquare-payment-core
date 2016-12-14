<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Platforms;

use Webaccess\ProjectSquarePayment\Context;
use Webaccess\ProjectSquarePayment\Contracts\Logger;
use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;

class GetPlatformUsageAmountInteractor
{
    private $platformRepository;

    /**
     * @param PlatformRepository $platformRepository
     * @param Logger $logger
     */
    public function __construct(PlatformRepository $platformRepository, Logger $logger)
    {
        $this->platformRepository = $platformRepository;
        $this->logger = $logger;
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
        return $this->getMonthlyCost($platformID) / $this->getNumberOfDaysOfMonth();
    }

    private function getNumberOfDaysOfMonth()
    {
        return date('t', mktime(0, 0, 0, Context::getMonth(), 1, Context::getYear()));
    }
}