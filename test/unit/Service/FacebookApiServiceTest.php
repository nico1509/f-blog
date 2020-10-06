<?php

namespace Nico1509\Facebookblog\Service;

function wp_remote_get( string $url ) {
    if ($url === 'https://graph.facebook.com/v7.0/12345/feed?access_token=access_token') {
        return [
            'body' => file_get_contents(__DIR__ . '/../Fixture/page-feed-single-post.json'),
        ];
    }
    if ($url === 'https://graph.facebook.com/v7.0/100325135089197_100868891701488?fields=created_time,message,attachments&access_token=access_token') {
        return [
            'body' => file_get_contents(__DIR__ . '/../Fixture/post-attachement-image.json'),
        ];
    }

    throw new \InvalidArgumentException('WP-Remote Mock kennt URL nicht: ' . $url);
}

function wp_remote_retrieve_body( array $response ) {
    return $response['body'];
}


namespace Nico1509\Facebookblog\Test\Unit\Service;

use Nico1509\Facebookblog\Model\Log;
use Nico1509\Facebookblog\Service\FacebookApiService;
use PHPUnit\Framework\TestCase;

class FacebookApiServiceTest extends TestCase {

    private FacebookApiService $facebookApiService;
    private string $tempDir;

    protected function setUp(): void {
        parent::setUp();

        $this->tempDir = sys_get_temp_dir() . '/facebookblog-unit-api';
        mkdir($this->tempDir);

        $log = new Log($this->tempDir);
        $this->facebookApiService = new FacebookApiService($log, 'access_token');
    }

    protected function tearDown(): void {
        parent::tearDown();
        exec("rm $this->tempDir -rf");
    }

    public function testFetchFeed(): void
    {
        $feedBaseName = $this->facebookApiService->fetchFeed('12345', null);

        self::assertFileExists($feedBaseName . '.json');
        self::assertFileExists($feedBaseName . '/100325135089197_100868891701488.json');
    }
}
