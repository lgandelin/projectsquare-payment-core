<?php

namespace Webaccess\ProjectSquarePayment\Interactors\Platforms;

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
            (new DebitPlatformAccountInteractor($this->platformRepository))->execute(new DebitPlatformAccountRequest([
                'platformID' => $platform->getID(),
            ]));
        }
    }
}