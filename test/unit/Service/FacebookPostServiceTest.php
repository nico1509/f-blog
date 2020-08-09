<?php

use Nico1509\Facebookblog\Model\Log;
use Nico1509\Facebookblog\Model\FacebookPost;
use Nico1509\Facebookblog\Service\FacebookPostService;
use PHPUnit\Framework\TestCase;

class FacebookPostServiceTest extends TestCase {

    private FacebookPostService $facebookFeedService;

    public function testGetValidatedPostList(): void {
//        $feedData = [
//            'message'      => 'asidhasofihasoihfoaishfoi',
//            'id'           => '100325135089197_100868891701488',
//            'created_time' => '2020-07-05T10:31:50+0000',
//            'attachements' => [
//                'data' => [
//                    [
//                        'media'  => [
//                            'image' => [
//                                'src' => 'https',
//                            ],
//                        ],
//                        'target' => [
//                            'url' => 'abc',
//                        ],
//                        'type' => 'photo'
//                    ],
//                ],
//            ],
//        ];

        $pageFeedJson = file_get_contents(__DIR__ . '/../Fixture/page-feed-single-post.json');
        // TODO: Alle Types testen
        $feedData = json_decode($pageFeedJson, true);

        $imagePostJson = file_get_contents(__DIR__ . '/../Fixture/post-attachement-image.json');
        $imagePostData = json_decode($imagePostJson, true);

        foreach ( $feedData['data'] as &$post ) {
            if ($post['id'] === $imagePostData['id']) {
                $post = array_merge($post, $imagePostData);
            }
        }

        // TODO: Medien einbinden, gescheite Fixture anlegen
        $expectedPostList   = [];
        $expectedPostList[] = new FacebookPost(
            'asidhasofihasoihfoaishfoi',
            '100325135089197_100868891701488',
            new \DateTime( '2020-07-05T10:31:50+0000' )
        );

        $postList = $this->facebookFeedService->getValidatedPostList( $feedData );
        self::assertEquals( $expectedPostList, $postList);
    }

    protected function setUp(): void {
        parent::setUp();
        $log                       = new Log( sys_get_temp_dir() );
        $this->facebookFeedService = new FacebookPostService( $log );
    }
}
