<?php

namespace Nico1509\Facebookblog\Service;

use Exception;
use Nico1509\Facebookblog\Model\Log;
use Nico1509\Facebookblog\Model\FacebookPost;
use \DateTime;

class FacebookPostService {

    private Log $log;

    public function __construct( Log $log ) {
        $this->log = $log;
    }

    /**
     * Validiert die Feed-Daten und erstellt eine {@see FacebookPost}-Liste
     *
     * @param array $feedData
     *
     * @return FacebookPost[]
     * @throws Exception
     */
    public function getValidatedPostList( array $feedData ): array {
        $postList = [];
        foreach ( $feedData['data'] as $postData ) {
            $facebookPost = new FacebookPost($postData['message'], $postData['id'], new DateTime($postData['created_time']));
            $this->addAttachement($facebookPost, $postData);
            $postList[] = $facebookPost;
        }
        return $postList;
    }

    private function addAttachement(FacebookPost $facebookPost, array $postData): void
    {
        if (!isset($postData['attachements'])) {
            return;
        }
        // Annahme: Nur ein Anhang pro Post
        $attachementData = $postData['attachements']['data'][0];
        switch ($attachementData['type']) {
            case 'photo':
                $this->addPhotoAttachement($facebookPost, $attachementData);
                break;
            case 'video_autoplay':
                $this->addVideoAttachement($facebookPost, $attachementData);
                break;
            case 'share':
                $this->addLinkAttachement($facebookPost, $attachementData);
                break;
            default:
                // TODO: Log "unbekannter Attachement Typ"
                break;
        }
    }

    private function addPhotoAttachement(FacebookPost $facebookPost, array $attachementData): void
    {
        $facebookPost->setImageSource($attachementData['media']['image']['src']);
        $facebookPost->setImageLink($attachementData['target']['url']);
    }

    private function addVideoAttachement(FacebookPost $facebookPost, array $attachementData): void
    {
        $facebookPost->setImageSource($attachementData['media']['image']['src']);
        $facebookPost->setVideoSource($attachementData['media']['source']);
        $facebookPost->setVideoLink($attachementData['target']['url']);
    }

    private function addLinkAttachement(FacebookPost $facebookPost, array $attachementData): void
    {
        $facebookPost->setLink($attachementData['url']);
    }
}
