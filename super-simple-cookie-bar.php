<?php

/**
 * Plugin Name:       Super Simple Cookie Bar
 * Description:       A super simple plugin that provides a super simple cookie bar notice.
 * Version:           1.0.2
 * Author:            NCompass
 * Author URI:        https://www.ncompass.co.uk
 * License:           GPLv2 or later
 * Text Domain:       super-simple-cookie-bar
 * GitHub Plugin URI: ncompass/super-simple-cookie-bar
 * GitHub Plugin URI: https://github.com/ncompass/super-simple-cookie-bar
 */

// Define constants for plugin paths
define( 'SSCB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SSCB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if ( version_compare( PHP_VERSION, '7.4', '<' ) || version_compare( get_bloginfo( 'version' ), '5.7', '<' ) ) {
  wp_die( __( 'This plugin requires PHP 7.4+ and WordPress 5.7+.', 'super-simple-cookie-bar' ) );
}

/**
 * Registers settings in admin
 */
require_once SSCB_PLUGIN_DIR . 'includes/sscb-settings.php';

/**
 * Register settings link
 *
 * Register settings page link in WordPress options
 *
 * @since 1.0.0
 *
 * @param array $actions Existing settings links array collected from "plugin_action_links".
 * @return array Merged array with our settings link.
 */
function sscb_action_links( $actions ) {
  $mylinks = array(
		'<a href="' . admin_url( 'options-general.php?page=super-simple-cookie-bar' ) . '">Settings</a>',
  );
	$actions = array_merge( $actions, $mylinks );

  return $actions;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'sscb_action_links' );

/** If enabled, add script, CSS, and JSON **/
$sscb_options = get_option( 'sscb_plugin_options' ) ?: array(); // Ensure $sscb_options is always an array
if ( ! empty( $sscb_options['enabled'] ) && true === $sscb_options['enabled'] ) {
  add_action( 'wp_enqueue_scripts', 'sscb_enqueue' );
}

/**
 * Enqueue Assets
 *
 * Enqueues CSS, JS, and inline init script on the front end.
 *
 * @since 1.0.0
 */
function sscb_enqueue() {
  wp_enqueue_style( 'sscb-styles', SSCB_PLUGIN_URL . 'public/css/cookieconsent.min.css' );
  wp_enqueue_script( 'sscb-scripts', SSCB_PLUGIN_URL . 'public/js/cookieconsent.min.js', array(), null, array( 'strategy' => 'defer', 'in_footer' => true ) );

  $script = sscb_init_options();
  wp_add_inline_script( 'sscb-scripts', $script, 'after' );
}

/**
 * Build Init
 *
 * Builds the JSON init script based on the user's chosen settings.
 *
 * @since 1.0.0
 */
function sscb_init_options() {
  $sscb_options = get_option( 'sscb_plugin_options' ) ?: array(); // Ensure $sscb_options is always an array

  $sscb_options['theme'] = isset( $sscb_options['theme'] ) ? $sscb_options['theme'] : 'block';
  $sscb_options['position'] = isset( $sscb_options['position'] ) ? $sscb_options['position'] : 'bottom';

  $sscb_options['bar_bg'] = isset( $sscb_options['bar_bg'] ) ? $sscb_options['bar_bg'] : '#000000';
  $sscb_options['bar_txt'] = isset( $sscb_options['bar_txt'] ) ? $sscb_options['bar_txt'] : '#ffffff';

  $sscb_options['btn_bg'] = isset( $sscb_options['theme'] ) && $sscb_options['theme'] === 'wire' ? 'transparent' : ( isset( $sscb_options['btn_bg'] ) ? $sscb_options['btn_bg'] : '#ffffff' );
  $sscb_options['btn_txt'] = isset( $sscb_options['theme'] ) && $sscb_options['theme'] === 'wire' ? ( isset( $sscb_options['btn_txt'] ) ? $sscb_options['btn_txt'] : ( isset( $sscb_options['btn_border'] ) ? $sscb_options['btn_border'] : '#ffffff' ) ) : ( isset( $sscb_options['btn_txt'] ) ? $sscb_options['btn_txt'] : '#000000' );
  $sscb_options['btn_border'] = isset( $sscb_options['theme'] ) && $sscb_options['theme'] === 'wire' ? ( isset( $sscb_options['btn_border'] ) ? $sscb_options['btn_border'] : ( isset( $sscb_options['btn_txt'] ) ? $sscb_options['btn_txt'] : '#ffffff' ) ) : ( isset( $sscb_options['btn_border'] ) ? $sscb_options['btn_border'] : '#ffffff' );

  $sscb_options['message'] = isset( $sscb_options['message'] ) ? $sscb_options['message'] : 'This website uses cookies to ensure you get the best experience on our website.';
  $sscb_options['dismiss'] = isset( $sscb_options['dismiss'] ) ? $sscb_options['dismiss'] : 'Got it!';
  $sscb_options['link'] = isset( $sscb_options['link'] ) ? $sscb_options['link'] : 'Learn More';

  /**
    * Apply WPML Filters
    */
  $sscb_options['message'] = apply_filters( 'wpml_translate_single_string', $sscb_options['message'], 'super-simple-cookie-bar', 'Message' );
  $sscb_options['dismiss'] = apply_filters( 'wpml_translate_single_string', $sscb_options['dismiss'], 'super-simple-cookie-bar', 'Dismiss/Accept Text' );
  $sscb_options['link'] = apply_filters( 'wpml_translate_single_string', $sscb_options['link'], 'super-simple-cookie-bar', 'Learn More Link Text' );
  $sscb_options['href'] = apply_filters( 'wpml_permalink', isset( $sscb_options['href'] ) ? $sscb_options['href'] : '', apply_filters( 'wpml_current_language', null ), true );

  /**
    * Sanitize collected data
    */
  $theme_allow = array( 'block', 'edgeless', 'classic', 'wire' );
  $pos_allow = array( 'bottom', 'top', 'top-static', 'bottom-left', 'bottom-right' );
  $sscb_options['theme'] = in_array( $sscb_options['theme'], $theme_allow, true ) ? $sscb_options['theme'] : 'block';
  $sscb_options['position'] = in_array( $sscb_options['position'], $pos_allow, true ) ? $sscb_options['position'] : 'bottom';

  $sscb_options['bar_bg'] = sanitize_hex_color( $sscb_options['bar_bg'] );
  $sscb_options['bar_txt'] = sanitize_hex_color( $sscb_options['bar_txt'] );

  if ( isset( $sscb_options['btn_bg'] ) && $sscb_options['btn_bg'] !== 'transparent' ) {
    $sscb_options['btn_bg'] = sanitize_hex_color( $sscb_options['btn_bg'] );
  }
  $sscb_options['btn_txt'] = sanitize_hex_color( $sscb_options['btn_txt'] );
  $sscb_options['btn_border'] = sanitize_hex_color( $sscb_options['btn_border'] );

  $sscb_options['message'] = wp_kses_post( $sscb_options['message'] );
  $sscb_options['dismiss'] = sanitize_text_field( $sscb_options['dismiss'] );
  $sscb_options['link'] = sanitize_text_field( $sscb_options['link'] );
  $sscb_options['href'] = sanitize_url( $sscb_options['href'], array( 'http', 'https' ) );

  /**
    * Build JSON init script
    */
  $content = array(
    'message' => $sscb_options['message'], // Allow HTML here
    'dismiss' => $sscb_options['dismiss'],
    'link'    => isset( $sscb_options['link'] ) ? $sscb_options['link'] : false,
    'href'    => isset( $sscb_options['href'] ) ? $sscb_options['href'] : '',
  );

  $script = 'window.cookieconsent.initialise(' . wp_json_encode( array(
    'palette' => array(
			'popup' => array(
				'background' => $sscb_options['bar_bg'],
				'text'       => $sscb_options['bar_txt'],
			),
			'button' => array(
				'background' => $sscb_options['btn_bg'],
				'text'       => $sscb_options['btn_txt'],
				'border'     => $sscb_options['btn_border'],
			),
		),
		'theme'    => $sscb_options['theme'],
		'position' => $sscb_options['position'] === 'top-static' ? 'top' : $sscb_options['position'],
		'static'   => $sscb_options['position'] === 'top-static',
		'content'  => $content,
	) ) . ');';

  return $script;
}