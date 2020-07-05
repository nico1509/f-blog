<?php

/*
Plugin Name: Facebook Blog
Description: An Import Plugin for your Facebook Page
Author: Nico Aßfalg
Version: 1.0
Author URI: https://nico-assfalg.de
*/

/**
 * This file shall be used for bootstrapping only
 */
use Nico1509\Facebookblog\Service\FacebookApiService;

require __DIR__ . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

function do_stuff(): void {
    $facebookApiService = new FacebookApiService();
    printf(
        '<p>%s</p>',
        $facebookApiService->getText()
    );
}

add_action( 'admin_notices', 'do_stuff' );
