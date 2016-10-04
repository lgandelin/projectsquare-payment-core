<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Platforms;

use Webaccess\ProjectSquarePayment\Entities\Platform;
use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;
use Webaccess\ProjectSquarePayment\Requests\Platforms\DebitPlatformAccountRequest;

class DebitPlatformsAccountsInteractor
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
            if ($this->isPlatformStatusValidForDebit($platform->getStatus())) {
                (new DebitPlatformAccountInteractor($this->platformRepository))->execute(new DebitPlatformAccountRequest([
                    'platformID' => $platform->getID(),
                ]));
            }
        }
    }

    private function isPlatformStatusValidForDebit($platformStatus)
    {
        return $platformStatus == Platform::PLATFORM_STATUS_IN_USE;
    }
}