<?php

namespace Nico1509\Facebookblog\Test\Unit\Service;

use DateTime;
use Nico1509\Facebookblog\Model\Log;
use Nico1509\Facebookblog\Model\FacebookPost;
use Nico1509\Facebookblog\Service\FacebookPostService;
use PHPUnit\Framework\TestCase;

class FacebookPostServiceTest extends TestCase {

    private FacebookPostService $facebookFeedService;

    public function testGetValidatedPostList(): void {
        $pageFeedJson = file_get_contents(__DIR__ . '/../Fixture/page-feed-single-post.json');
        $feedData = json_decode($pageFeedJson, true);

        $imagePostJson = file_get_contents(__DIR__ . '/../Fixture/post-attachement-image.json');
        $imagePostData = json_decode($imagePostJson, true);

        foreach ( $feedData['data'] as &$postData ) {
            if ($postData['id'] === $imagePostData['id']) {
                $postData['attachments'] = $imagePostData['attachments'];
            }
        }

        $expectedPostList   = [];
        $expectedPostList[] = new FacebookPost(
            'asidhasofihasoihfoaishfoi',
            '100325135089197_100868891701488',
            new DateTime( '2020-07-05T10:31:50+0000' ),
            'https://scontent.ffra2-1.fna.fbcdn.net/v/t1.0-9/106790674_100868708368173_619122657616213457_n.png?_nc_cat=109&_nc_sid=8024bb&_nc_oc=AQl4JAIojNl_r5dcybHXR-MuxDlDyQ-7GsZWXjn-VLzBEue7cX1TsIc0Ic_V6cyhjScoK-pHAPZEb1FVKt7N-BNS&_nc_ht=scontent.ffra2-1.fna&oh=98c33be74d1841873c48127e799cae40&oe=5F28216D',
            'https://www.facebook.com/100325135089197/photos/a.100868868368157/100868705034840/?type=3'
        );

        // FIXME: Attachments nicht erkannt

        $postList = $this->facebookFeedService->getValidatedPostList( $feedData );
        self::assertEquals( $expectedPostList, $postList);
    }

    protected function setUp(): void {
        parent::setUp();
        $log                       = new Log( sys_get_temp_dir() );
        $this->facebookFeedService = new FacebookPostService( $log );
    }
}
