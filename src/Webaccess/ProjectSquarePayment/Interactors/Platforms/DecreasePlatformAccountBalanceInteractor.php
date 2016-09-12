<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Platforms;

use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;

class DecreasePlatformAccountBalanceInteractor
{
    private $platformRepository;

    public function __construct(PlatformRepository $platformRepository)
    {
        $this->platformRepository = $platformRepository;
    }

    public function decreaseDailyCost($platformID)
    {
        $platform = $this->platformRepository->getByID($platformID);
        $dailyCost = (new GetPlatformUsageAmountInteractor($this->platformRepository))->getDailyCost($platformID);
        $platform->setAccountBalance($platform->getAccountBalance() - $dailyCost);
    }

    public function decreaseMonthlyCost($platformID)
    {
        $platform = $this->platformRepository->getByID($platformID);
        $monthlyCost = (new GetPlatformUsageAmountInteractor($this->platformRepository))->getMonthlyCost($platformID);
        $platform->setAccountBalance($platform->getAccountBalance() - $monthlyCost);
    }
}