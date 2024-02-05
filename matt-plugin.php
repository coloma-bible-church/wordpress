<?php
/*
 * Plugin Name: Matt Plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Prevent direct access
}

// Prevent syntax errors
if ( ! function_exists( 'add_shortcode' ) ) {
    function add_shortcode() {}
    exit;
}
if ( ! function_exists( 'do_shortcode' ) ) {
    function do_shortcode() {}
    exit;
}

function longcode_shortcode( $atts , $content = null ) {
	while ( $content ) {
	    $after = do_shortcode( $content );
	    if ( $after === $content ) {
	        break;
	    }
	}
	return $content;
}
add_shortcode( 'longcode', 'longcode_shortcode' );
