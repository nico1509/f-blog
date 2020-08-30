<?php


namespace Nico1509\Facebookblog\Admin;


class WordpressSettings {

    private const PAGE_SLUG = 'facebook_blog';
    private const PAGE_SECTION_ID = 'facebook_blog_admin_index';
    private const PAGE_SECTION_TITLE = 'Einstellungen';

    private array $settings;

    public function __construct( array $settings )
    {
        $this->settings = $settings;
    }

    public function register()
    {
        $this->register_settings_section();
        foreach ( $this->settings as $setting ) {
            $this->register_setting_fields( $setting );
        }
    }

    private function register_setting_fields( array $setting )
    {
        register_setting( $setting['option_group'], $setting['option_name'], $setting['sanitize_callback'] );
        add_settings_field( $setting['option_name'], $setting['title'], $this->get_callback_input_text($setting['option_name']), self::PAGE_SLUG, self::PAGE_SECTION_ID );
    }

    private function register_settings_section()
    {
        add_settings_section( self::PAGE_SECTION_ID, self::PAGE_SECTION_TITLE, function () {}, self::PAGE_SLUG );
    }

    public function get_callback_input_text( $optionName, $placeholder = '' )
    {
        return function () use ($optionName, $placeholder) {
            echo '<input type="text" class="regular-text" name="' . $optionName . '" value="' . esc_attr( get_option( $optionName ) ) . '" placeholder="' . $placeholder . '">';
        };
    }
}
