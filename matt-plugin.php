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

function longcode_shortcode( $atts , string $content = null ) {
    $xml = new DOMDocument();
    @ $xml->loadXML( $content );
    foreach ( $xml->childNodes as $child ) {
        foreach ( $child->childNodes as $grandchild ) {
            if ( ! is_a( $grandchild, 'DOMText' ) ) {
                continue;
            }
            /**
             * @var DOMText $grandchild
             */
            $json = $grandchild->wholeText;
            $array = json_decode( $json, true );
            return longcode_shortcode_core( $array );
        }
    }
    return $content;
}

/**
 * @param array|mixed $array
 */
function longcode_shortcode_core( $array ) {
    if ( is_array( $array ) ) {
        if ( array_key_exists( 'name', $array ) ) {
            // Treat as shortcode definition
            $name = $array['name'];
            $shortcode = '[' . $name;
            if ( array_key_exists( 'atts', $array ) ) {
                $atts = $array['atts'];
                foreach ( $atts as $key => $value ) {
                    $value = longcode_shortcode_core( $value );
                    $shortcode .= ' ' . $key . '="' . $value . '"';
                }
            }
            if ( array_key_exists( 'content', $array ) ) {
                $content = $array['content'];
                $content = longcode_shortcode_core( $content );
                $shortcode .= ']' . $content . '[/' . $name;
            }
            $shortcode .= ']';

            return do_shortcode( $shortcode );
        } else {
            // Concat elements
            return array_reduce(
                $array,
                function( $carry, $item ) {
                    return $carry . longcode_shortcode_core( $item );
                },
                ''
            );
        }
    } else {
        return $array;
    }
}

add_shortcode( 'longcode', 'longcode_shortcode' );
