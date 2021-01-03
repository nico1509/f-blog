<?php

/*
Plugin Name: Facebook Blog
Description: An Import Plugin for your Facebook Page
Author: Nico Aßfalg
Version: 1.0
Author URI: https://nico-assfalg.de
*/

use Nico1509\Facebookblog\Admin\WordpressSettings;
use Nico1509\Facebookblog\Cron\ImportTask;

function_exists('add_action') or die('Not a Wordpress Env');

require __DIR__ . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

if ( ! class_exists( 'FacebookBlog' ) ) {

    class FacebookBlog
    {
        function activate()
        {
            flush_rewrite_rules();
        }

        function deactivate()
        {
            flush_rewrite_rules();
            $this->remove_cron();
        }

        function register()
        {
            add_action( 'admin_menu', [ $this, 'add_admin_pages' ] );
            add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ $this, 'add_settings_link' ] );
            $this->add_settings();
            $this->add_cron();
        }

        public function add_admin_pages()
        {
            add_menu_page(
                'Facebook Blog',
                'Facebook Blog',
                'manage_options',
                'facebook_blog',
                [ $this, 'admin_index' ],
                'dashicons-facebook',
                110
            );
        }

        public function admin_index()
        {
            require_once plugin_dir_path( __FILE__ ) . 'templates/admin.php';
        }

        public function add_settings_link( $links )
        {
            $settingsLink = '<a href="admin.php?page=facebook_blog">Einstellungen</a>';
            array_push( $links, $settingsLink );
            return $links;
        }

        public function add_settings()
        {
            $wordpressSettings = new WordpressSettings( [
                [
                    'title'             => 'Facebook Access Token',
                    'option_name'       => 'facebook_blog_page_token',
                    'option_group'      => 'facebook_blog_settings',
                    'type'              => 'text',
                    'sanitize_callback' => function ( $input ) { return $input; },
                ],
                [
                    'title'             => 'Facebook Page ID',
                    'option_name'       => 'facebook_blog_page_id',
                    'option_group'      => 'facebook_blog_settings',
                    'type'              => 'text',
                    'sanitize_callback' => function ( $input ) { return $input; },
                ],
                [
                    'title'             => 'Letzte Übertragung',
                    'option_name'       => 'facebook_blog_latest_import',
                    'option_group'      => 'facebook_blog_settings',
                    'type'              => 'text',
                    'sanitize_callback' => function ( $input ) {
                        if ($this->validate_date( $input ) === false) {
                            add_settings_error( 'facebook_blog_latest_import',
                                'facebook_blog_latest_import',
                                'Falsches Datumsformat in "Letzte Übertragung"' );
                            return get_option( 'facebook_blog_latest_import' );
                        }
                        return $input;
                    },
                ],
                [
                    'title'             => 'E-Mails für Benachrichtigungen',
                    'option_name'       => 'facebook_blog_notification_emails',
                    'option_group'      => 'facebook_blog_settings',
                    'type'              => 'text',
                    'sanitize_callback' => function ( $input ) {
                        if ($this->validate_email_list( $input ) === false) {
                            add_settings_error( 'facebook_blog_notification_emails',
                                'facebook_blog_notification_emails',
                                'Falsches E-Mail Format in "E-Mails für Benachrichtigungen"' );
                            return get_option( 'facebook_blog_notification_emails' );
                        }
                        return $input;
                    },
                ],
            ] );
            add_action( 'admin_init', [ $wordpressSettings, 'register' ] );
        }

        public function add_cron()
        {
            $importTask = new ImportTask();
            $importTask->register();
        }

        public function remove_cron()
        {
            $importTask = new ImportTask();
            $importTask->unregister();
        }

        private function validate_date( $input ) {
            try {
                new DateTime( $input );
            } catch ( Exception $e ) {

                return false;
            }

            return $input;
        }

        private function validate_email_list( $input ) {
            $email_list = explode( ';', $input );
            if ( ! $email_list ) {
                return false;
            }
            foreach ( $email_list as $email ) {
                if ( ! is_email( $email ) ) {
                    return false;
                }
            }

            return $input;
        }
    }
}

$facebookBlog = new FacebookBlog();
$facebookBlog->register();

// activation
register_activation_hook( __FILE__, [ $facebookBlog, 'activate' ]);

// deactivation
register_deactivation_hook( __FILE__, [ $facebookBlog, 'deactivate' ]);

// uninstall
