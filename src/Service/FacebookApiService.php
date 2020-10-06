<?php


namespace Nico1509\Facebookblog\Service;


use \DateTime;
use Exception;
use Nico1509\Facebookblog\Model\Log;
use \RuntimeException;

class FacebookApiService {

    private const API_BASE = 'https://graph.facebook.com/v7.0';
    private const API_FEED = '/%s/feed';
    private const API_POST_DETAILS = '/%s?fields=created_time,message,attachments';
    private const API_AUTH = 'access_token=%s';

    private const EXT_JSON = '.json';

    private Log $log;
    private string $accessToken;

    public function __construct( Log $log, string $accessToken ) {
        $this->log         = $log;
        $this->accessToken = $accessToken;
    }

    /**
     * Lädt den Feed als JSON-Datei in das data-Verzeichnis
     * und gibt den Dateipfad zurück
     *
     * @param string $pageId
     *
     * @param DateTime|null $since
     *
     * @return string
     * @throws Exception
     */
    public function fetchFeed( string $pageId, ?DateTime $since = null ): string {
        $feedUrl  = $this->getFeedUrl( $pageId );
        $feedJson = $this->fetchApiData( $feedUrl );

        $now = new DateTime();
        $feedBaseName = 'feed_' . $now->format('Y-m-d_H-i-s');
        $this->saveJsonFile( $feedJson, $feedBaseName );
        mkdir( $this->log->getDataDir() . DIRECTORY_SEPARATOR . $feedBaseName );

        $feedData = json_decode( $feedJson, true );
        if ( ! isset( $feedData['data'] ) ) {
            throw new RuntimeException( 'Ungültige Feed-Daten für Page ID "' . $pageId . '", URL: "' . $feedUrl . '"' );
        }
        foreach ( $feedData['data'] as $postData ) {
            $postId         = $postData['id'];
            $postDetailsUrl = $this->getPostDetailsUrl( $postId );

            if ( $since !== null ) {
                $postDateTime = new DateTime( $postData['created_time'] );
                if ( $postDateTime->getTimestamp() <= $since->getTimestamp() ) {
                    continue;
                }
            }

            $postDetailsJson = $this->fetchApiData( $postDetailsUrl );
            $this->saveJsonFile( $postDetailsJson, $postData['id'], $feedBaseName );
        }

        return $this->log->getDataDir() . DIRECTORY_SEPARATOR . $feedBaseName;
    }

    private function getFeedUrl( string $pageId ): string {
        return self::API_BASE . sprintf( self::API_FEED, $pageId )
               . '?' . sprintf( self::API_AUTH, $this->accessToken );
    }

    private function getPostDetailsUrl( string $postId ): string {
        return self::API_BASE . sprintf( self::API_POST_DETAILS, $postId )
               . '&' . sprintf( self::API_AUTH, $this->accessToken );
    }

    private function fetchApiData( string $url ): string {
        $response = wp_remote_get( $url );

        return wp_remote_retrieve_body( $response );
    }

    private function saveJsonFile( string $content, string $fileName, string $subDir = '' ): void {
        $dir = $this->log->getDataDir();
        if ($subDir !== '') {
            $dir .= DIRECTORY_SEPARATOR . $subDir;
        }
        $filePath = $dir . DIRECTORY_SEPARATOR . $fileName . self::EXT_JSON;
        file_put_contents( $filePath, $content );
    }

}
