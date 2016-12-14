<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Platforms;

use Webaccess\ProjectSquarePayment\Contracts\Logger;
use Webaccess\ProjectSquarePayment\Entities\Platform;
use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;
use Webaccess\ProjectSquarePayment\Requests\Platforms\DebitPlatformAccountRequest;

class DebitPlatformsAccountsInteractor
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

    public function execute()
    {
        $this->logger->logRequest(self::class);

        foreach ($this->platformRepository->getAll() as $platform) {
            if ($this->isPlatformStatusValidForDebit($platform->getStatus())) {
                (new DebitPlatformAccountInteractor($this->platformRepository, $this->logger))->execute(new DebitPlatformAccountRequest([
                    'platformID' => $platform->getID(),
                ]));
            }
        }

        $this->logger->logResponse(self::class);
    }

    /**
     * @param $platformStatus
     * @return bool
     */
    private function isPlatformStatusValidForDebit($platformStatus)
    {
        return $platformStatus == Platform::PLATFORM_STATUS_IN_USE;
    }
}