<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Platforms;

use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;

class DecreasePlatformAccountBalanceInteractor
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
     */
    public function decreaseDailyCost($platformID)
    {
        $platform = $this->platformRepository->getByID($platformID);
        $dailyCost = (new GetPlatformUsageAmountInteractor($this->platformRepository))->getDailyCost($platformID);
        $platform->setAccountBalance($platform->getAccountBalance() - $dailyCost);
    }

    /**
     * @param $platformID
     */
    public function decreaseMonthlyCost($platformID)
    {
        $platform = $this->platformRepository->getByID($platformID);
        $monthlyCost = (new GetPlatformUsageAmountInteractor($this->platformRepository))->getMonthlyCost($platformID);
        $platform->setAccountBalance($platform->getAccountBalance() - $monthlyCost);
    }
}