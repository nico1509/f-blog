<?php

use Nico1509\Facebookblog\Service\FacebookApiService;
use PHPUnit\Framework\TestCase;

class FacebookApiServiceTest extends TestCase {

    private FacebookApiService $facebookApiService;

    public function testGetText(): void {
        self::assertEquals( 'Facebook', $this->facebookApiService->getText() );
    }

    protected function setUp(): void {
        parent::setUp();
        $this->facebookApiService = new FacebookApiService();
    }
}
