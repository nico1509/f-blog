<?php


namespace Nico1509\Facebookblog\Cron;


use DirectoryIterator;
use Exception;
use Nico1509\Facebookblog\Model\Log;
use Nico1509\Facebookblog\Service\FacebookApiService;
use Nico1509\Facebookblog\Service\FacebookPostService;
use Nico1509\Facebookblog\Service\WordpressPostService;

class ImportTask
{
    public const CRON_HOOK = 'facebook_blog_cron_hook';

    public function register()
    {
        add_action( self::CRON_HOOK, [ $this, 'cron_exec' ] );
        if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
            wp_schedule_event( time(), 'daily', self::CRON_HOOK );
        }
    }

    public function unregister()
    {
        $timestamp = wp_next_scheduled( self::CRON_HOOK );
        wp_unschedule_event( $timestamp, self::CRON_HOOK );
    }

    public function cron_exec()
    {
        $log = new Log( __DIR__ . '/../../data' );
        $access_token = get_option( 'facebook_blog_page_token' );
        if ( ! $access_token ) {
            $log->addError('Access Token ist nicht konfiguriert');
            $this->write_log($log);
            return;
        }
        $page_id = get_option( 'facebook_blog_page_id' );
        if ( ! $page_id ) {
            $log->addError('Page-ID ist nicht konfiguriert');
            $this->write_log($log);
            return;
        }
        $since = get_option('facebook_blog_latest_import');
        $sinceDateTime = $since ? new \DateTime($since) : null;

        $facebook_api_service = new FacebookApiService( $log, $access_token );
        try {
            $feed_json_basename = $facebook_api_service->fetchFeed( $page_id, $sinceDateTime );
        } catch ( Exception $exception ) {
            $log->addError($exception->getMessage());
            $this->write_log($log);
            return;
        }

        $now = new \DateTime();
        update_option( 'facebook_blog_latest_import', $now->format( \DateTime::ISO8601 ) );

        $feed_data = [
            'data' => [],
        ];
        foreach ( new DirectoryIterator( $feed_json_basename ) as $post_json_file ) {
            if ($post_json_file->isDot()) continue;
            $post_data = json_decode( file_get_contents( $post_json_file->getRealPath() ), true );
            $feed_data['data'][] = $post_data;
        }

        $facebook_post_service = new FacebookPostService( $log );
        try {
            $facebook_post_list = $facebook_post_service->getValidatedPostList( $feed_data );
        } catch ( Exception $exception ) {
            $log->addError($exception->getMessage());
            $this->write_log($log);
            return;
        }

        $wordpress_post_service = new WordpressPostService( $log );
        foreach ( $facebook_post_list as $facebook_post ) {
            try {
                $wordpress_post_service->createBlogPost( $facebook_post );
            } catch (Exception $exception) {
                $log->addError($exception->getMessage());
            }
        }

        $this->write_log( $log );
    }

    public function write_log( Log $log )
    {
        if ( ! empty( $log->getErrors() ) ) {
            error_log( 'Facebook-Blog Import Error: ' . implode( ' + ', $log->getErrors() ) );
            $this->send_email_notifications( $log );
        }
    }

    private function send_email_notifications( Log $log )
    {
        $email_list = get_option( 'facebook_blog_notification_emails' );
        foreach ( explode( ';', $email_list ) as $email ) {
            wp_mail( $email, 'Wordpress Facebook-Blog Fehler', implode( ' + ', $log->getErrors() ) );
        }
    }
}
