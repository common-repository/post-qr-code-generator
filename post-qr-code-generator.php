<?php
/*
Plugin Name: Post QR Code Generator
Plugin URI: https://github.com/faisal46/wp-qr-post-plugin
Description: Display QR Code Auto Generator for every post & page below.
Version: 1.0
Author: Md. Faisal Amir Mostafa
Author URI: www.faisal.rajtechbd.com
License: GPLv3
License URI: https://www.gnu.org/licenses/old-licenses/gpl-3.0.html
Text Domain: rtech_qr
Domain Path: /langusges/
*/
/**
Post QR Code Generator is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.

Post QR Code Generator is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Post Content Word Count.
*/
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

if( !function_exists('rtech_qr_load_text_domain')) { 
  function rtech_qr_load_text_domain(){
    load_textdomain('rtech_qr', false, dirname(__FILE__) . "/languages");
  } 
}

add_action('plugins_loaded', 'rtech_qr_load_text_domain');

if( !function_exists('rtech_qr_display_qr_code')) { 
function rtech_qr_display_qr_code( $content ){
    $current_post_id = get_the_ID();
    $current_post_title = get_the_title($current_post_id);
    $current_post_url = urlencode(get_the_permalink($current_post_id));
    $current_post_type = get_post_type($current_post_id);

    // Post Type Check
    $post_type_check = apply_filters('rtech_post_type_check', array());
    if( in_array($current_post_type, $post_type_check) ){
        return $content;
    }

    // Dimension Hook
    $height = get_option('rtech_qr_height');
    $width = get_option('rtech_qr_width');
    $height = $height ? $height : 200;
    $width = $width ? $width : 200;
    $dimension = apply_filters( 'rtech_qr_dimention', "{$width}x{$height}" );

    //Image Attributes
    $image_attributes = apply_filters('pqrc_image_attributes',null);

    $image_src = sprintf( 'https://api.qrserver.com/v1/create-qr-code/?size=%s&data=%s', esc_attr($dimension), esc_url($current_post_url) );
    $content .= sprintf("<div class='qrcode'><img %s  src='%s' alt='%s' /></div>",esc_attr($image_attributes), esc_url($image_src), esc_html($current_post_title));
    return $content;

}
}
add_filter("the_content", "rtech_qr_display_qr_code");

if( !function_exists('rtech_qr_setting_init')) { 
 function rtech_qr_setting_init(){
    add_settings_section('rtech_qr_section', __('QR Code Settings','rtech_qr'), 'rtech_qr_section_callback', 'general');
    add_settings_field('rtech_qr_height', __('QR Code Height','rtech_qr'), 'rtech_qr_display_field', 'general','rtech_qr_section',array('rtech_qr_height'));
    add_settings_field('rtech_qr_width', __('QR Code Width','rtech_qr'), 'rtech_qr_display_field', 'general','rtech_qr_section',array('rtech_qr_width'));

    register_setting('general','rtech_qr_height',array('sanitize_callback' => 'esc_attr'));
    register_setting('general','rtech_qr_width',array('sanitize_callback' => 'esc_attr'));

 }
}

if( !function_exists('rtech_qr_display_field')) { 
 function rtech_qr_display_field($args){
    $option = get_option($args[0]);
    printf('<input type="text" id="%s" name="%s" value="%s"/>', esc_attr($args[0]), esc_attr($args[0]), esc_attr($option) );
 }
}
if( !function_exists('rtech_qr_section_callback')) { 
 function rtech_qr_section_callback(){
  printf('<p>%s</p>', __('Set your QR Code Height & Width','rtech_qr'));
 }
}

add_action("admin_init", 'rtech_qr_setting_init');

