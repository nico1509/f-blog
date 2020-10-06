<?php


namespace Nico1509\Facebookblog\Service;


use Nico1509\Facebookblog\Model\Log;
use Nico1509\Facebookblog\Model\FacebookPost;
use RuntimeException;

class WordpressPostService {

    public const META_FACEBOOK_ID_KEY = '_facebook-blog_id';

    private Log $log;

    public function __construct( Log $log ) {
        $this->log = $log;
    }

    /**
     * Erstellt aus einem {@see FacebookPost}-Objekt einen Wordpress-Blogeintrag
     *
     * @param FacebookPost $post
     *
     * @return int
     */
    public function createBlogPost( FacebookPost $post ): int {
        $wordpressPostId = wp_insert_post([
            'post_title' => $post->getCreatedTime()->format('d.m.Y'),
            'post_content' => $post->getMessage(),
            'post_date' => $post->getCreatedTime()->format('Y-m-d H:i:s'),
            'post_status' => 'publish',
        ], true);

        if (!is_int($wordpressPostId) || $wordpressPostId === 0) {
            throw new RuntimeException('Fehler beim Erstellen von Wordpress-Post für Facebook-Post "' . $post->getId() . '"');
        }

        if ($post->getImageSource() !== null) {
            $this->attachImage($wordpressPostId, $post->getImageSource());
        }

        add_post_meta($wordpressPostId, self::META_FACEBOOK_ID_KEY, $post->getId(), true);

        return $wordpressPostId;
    }

    private function attachImage( int $postId, string $imageUrl ): void
    {
        $downloadedImagePath = $this->downloadImage($postId, $imageUrl);
        $attachmentId = wp_insert_attachment([
            'post_mime_type' => 'image/png',
            'post_title' => basename( $downloadedImagePath ),
            'post_status' => 'inherit',
        ], $downloadedImagePath, $postId);
        $attachmentData = wp_generate_attachment_metadata($attachmentId, $downloadedImagePath);
        wp_update_attachment_metadata($attachmentId, $attachmentData);
        $postThumbnailSuccess = set_post_thumbnail($postId, $attachmentId);
        if ( ! $postThumbnailSuccess ) {
            $this->log->addError( 'Fehler beim setzen des Thumbnails: "' . $postThumbnailSuccess . '". Beitrag "' . $postId . '", Attachment "' . $attachmentId . '"' );
        }
    }

    private function downloadImage( string $postId, string $imageUrl ): string
    {
        $tempFile = download_url($imageUrl, 5);
        if (is_wp_error($tempFile)) {
            throw new RuntimeException('Fehler beim Herunterladen von "' . $imageUrl . '": ' . $tempFile->get_error_message());
        }

        $file = [
            'name' => $postId . '.png',
            'type' => 'image/png',
            'tmp_name' => $tempFile,
            'error' => 0,
            'size' => filesize($tempFile),
        ];

        $uploadOptions = [
            // Bild wird nicht über Formular hochgeladen
            'test_form' => false,
            // Keine leere Datei erlauben
            'test_size' => true,
            // MIME-Types für Bilder erlauben
            'mimes' => [
                'jpg|jpeg|jpe'  => 'image/jpeg',
                'gif'           => 'image/gif',
                'png'           => 'image/png',
            ],
        ];

        $uploadResult = wp_handle_sideload($file, $uploadOptions);
        if (!empty($uploadResult['error'])) {
            throw new RuntimeException('Fehler bei Überprüfung von heruntergeladenem Bild "' . $imageUrl . '": ' . $uploadResult['error'] );
        }

        return $uploadResult['file'];
    }

}
