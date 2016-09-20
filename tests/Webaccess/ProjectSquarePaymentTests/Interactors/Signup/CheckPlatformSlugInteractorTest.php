<?php

use Webaccess\ProjectSquarePayment\Interactors\Signup\CheckPlatformSlugInteractor;
use Webaccess\ProjectSquarePayment\Requests\Signup\CheckPlatformSlugRequest;
use Webaccess\ProjectSquarePayment\Responses\Signup\CheckPlatformSlugResponse;
use Webaccess\ProjectSquarePaymentTests\ProjectsquareTestCase;

class CheckPlatformSlugInteractorTest extends ProjectsquareTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new CheckPlatformSlugInteractor($this->platformRepository);
    }

    public function testCheckValidPlatformSlug()
    {
        $response = $this->interactor->execute(new CheckPlatformSlugRequest([
            'slug' => 'webaccess',
        ]));

        $this->assertInstanceOf(CheckPlatformSlugResponse::class, $response);
        $this->assertTrue($response->success);
    }

    public function testCheckUnavailablePlatformSlug()
    {
        $this->createSamplePlatform();
        $response = $this->interactor->execute(new CheckPlatformSlugRequest([
            'slug' => 'webaccess',
        ]));

        $this->assertInstanceOf(CheckPlatformSlugResponse::class, $response);
        $this->assertFalse($response->success);
        $this->assertEquals(CheckPlatformSlugResponse::PLATFORM_SLUG_UNAVAILABLE, $response->errorCode);
    }

    public function testCheckInvalidPlatformSlug()
    {
        $response = $this->interactor->execute(new CheckPlatformSlugRequest([
            'slug' => 'web/access',
        ]));

        $this->assertInstanceOf(CheckPlatformSlugResponse::class, $response);
        $this->assertFalse($response->success);
        $this->assertEquals(CheckPlatformSlugResponse::PLATFORM_SLUG_INVALID, $response->errorCode);
    }
}