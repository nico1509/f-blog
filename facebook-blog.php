<?php

/*
Plugin Name: Facebook Blog
Description: An Import Plugin for your Facebook Page
Author: Nico Aßfalg
Version: 1.0
Author URI: https://nico-assfalg.de
*/

function_exists('add_action') or die('Not a Wordpress Env');

//require __DIR__ . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

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
            add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ $this, 'settings_link' ] );
            add_action( 'admin_init', [ $this, 'register_setting_fields' ] );
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

        public function settings_link( $links )
        {
            $settingsLink = '<a href="admin.php?page=facebook_blog">Einstellungen</a>';
            array_push( $links, $settingsLink );
            return $links;
        }

        public function register_setting_fields()
        {
            register_setting(
                'facebook_blog_settings',
                'facebook_blog_page_token',
                [ $this, 'test_example_callback' ]
            );

            add_settings_section(
                'facebook_blog_admin_index',
                'Settings',
                function () {
                    echo 'SSSECTION';
                },
                'facebook_blog'
            );

            add_settings_field(
                'facebook_blog_page_token',
                'Facebook Page Token',
                function () {
                    echo '<input type="text" class="regular-text" name="facebook_blog_page_token" value="' . esc_attr( get_option( 'facebook_blog_page_token' ) ) . '" placeholder="Input here...">';
                },
                'facebook_blog',
                'facebook_blog_admin_index',
                [ 'label_for' => 'facebook_blog_page_token' ]
            );
        }

        public function test_example_callback( $input )
        {
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
