<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Platforms;

use DateTime;
use Webaccess\ProjectSquarePayment\Entities\Platform;
use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;

class UpdatePlatformsStatusesInteractor
{
    private $platformRepository;

    /**
     * @param PlatformRepository $platformRepository
     */
    public function __construct(PlatformRepository $platformRepository)
    {
        $this->platformRepository = $platformRepository;
    }

    public function execute()
    {
        foreach ($this->platformRepository->getAll() as $platform) {
            if ($this->isPlatformInTrialPeriod($platform) && $this->isTrialPeriodFinished($platform)) {
                $this->updateStatus($platform, Platform::PLATFORM_STATUS_IN_USE);
            }

            if ($this->isPlatformInUse($platform) && $this->isPlatformAccountBalanceIsNegative($platform)) {
                $this->updateStatus($platform, Platform::PLATFORM_STATUS_DISABLED);
            }
        }
    }

    /**
     * @param Platform $platform
     * @return bool
     */
    private function isPlatformInTrialPeriod(Platform $platform)
    {
        return $platform->getStatus() == Platform::PLATFORM_STATUS_TRIAL_PERIOD;
    }

    /**
     * @param Platform $platform
     * @return bool
     */
    private function isTrialPeriodFinished(Platform $platform)
    {
        return $platform->getCreationDate()->diff(new DateTime())->m >= 1;
    }

    /**
     * @param Platform $platform
     * @return bool
     */
    private function isPlatformInUse(Platform $platform)
    {
        return $platform->getStatus() == Platform::PLATFORM_STATUS_IN_USE;
    }

    /**
     * @param Platform $platform
     * @return bool
     */
    private function isPlatformAccountBalanceIsNegative(Platform $platform)
    {
        return $platform->getAccountBalance() < 0;
    }

    /**
     * @param Platform $platform
     * @param $status
     */
    private function updateStatus(Platform $platform, $status)
    {
        $platform->setStatus($status);
        $this->platformRepository->persist($platform);
        //$this->projectsquareAPIService->updateStatus($request->platformID, $status);
    }
}