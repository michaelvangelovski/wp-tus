<?php

/**
 * Plugin Name: WP Tus
 * Plugin URI: <plugin URI>
 * Description: Tus Server in WordPress.
 * Version: 0.0.0
 * Author: <author name>
 * Author URI: <author URI>
 * License: GPL2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// Avoid direct calls to this file.
if ( ! defined( 'ABSPATH' )) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );

    die( 'Access Forbidden' );
}

require __DIR__ . '/vendor/autoload.php';

add_action( 'init', function () {
    add_rewrite_tag( '%tus%', '([^&]+)' );
    add_rewrite_rule( '^wp-tus/([^/]*)/?', 'index.php?tus=$matches[1]', 'top' );
    add_rewrite_rule( '^wp-tus/?', 'index.php?tus', 'top' );
} );

add_action('parse_request', function ( $wp ) {
    // Return if it is a normal request.

    if ( ! isset($wp->query_vars['tus'])) {
        return;
    }

    \TusPhp\Config::set([
        'file' => [
            'dir' => '/tmp/',
            'name' => 'tus_php.cache',
        ],
    ]);

    $server = new \TusPhp\Tus\Server(); 

    $server
        ->setApiPath( '/wp-tus' ) // tus server endpoint.
        ->setUploadDir( __DIR__ . '/../../../../wp-tus-uploads' );


    $response = $server->serve();

    $response->send();

} );

