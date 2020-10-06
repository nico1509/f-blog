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
     * Erstellt aus den JSON Feed-Daten und eine {@see FacebookPost}-Liste
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
        if (!isset($postData['attachments'])) {
            return;
        }
        // Annahme: Nur ein Anhang pro Post
        $attachementData = $postData['attachments']['data'][0];
        switch ($attachementData['type']) {
            case 'photo':
                $this->addPhotoAttachement($facebookPost, $attachementData);
                break;
            case 'video':
            case 'video_autoplay':
            case 'video_inline':
                $this->addVideoAttachement($facebookPost, $attachementData);
                break;
            case 'share':
                $this->addLinkAttachement($facebookPost, $attachementData);
                break;
            default:
                $this->log->addError('Unbekannter Attachment-Typ: "' . $attachementData['type'] . '"');
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
