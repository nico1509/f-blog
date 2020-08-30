<?php

/*
Plugin Name: Facebook Blog
Description: An Import Plugin for your Facebook Page
Author: Nico Aßfalg
Version: 1.0
Author URI: https://nico-assfalg.de
*/

use Nico1509\Facebookblog\Admin\WordpressSettings;

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
        }

        function register()
        {
            add_action( 'admin_menu', [ $this, 'add_admin_pages' ] );
            add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ $this, 'add_settings_link' ] );
            $this->add_settings();
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
                    'title'             => 'Facebook Page Token',
                    'option_name'       => 'facebook_blog_page_token',
                    'option_group'      => 'facebook_blog_settings',
                    'sanitize_callback' => function ( $input ) { return $input; },
                ],
            ] );
            add_action( 'admin_init', [ $wordpressSettings, 'register' ] );
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
