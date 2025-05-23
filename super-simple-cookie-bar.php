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

// Define constants for default values
define( 'SSCB_DEFAULT_THEME', 'block' );
define( 'SSCB_DEFAULT_POSITION', 'bottom' );
define( 'SSCB_DEFAULT_BAR_BG', '#000000' );
define( 'SSCB_DEFAULT_BAR_TXT', '#ffffff' );
define( 'SSCB_DEFAULT_BTN_BG', '#ffffff' );
define( 'SSCB_DEFAULT_BTN_TXT', '#000000' );
define( 'SSCB_DEFAULT_BTN_BORDER', '#ffffff' );
define( 'SSCB_DEFAULT_MESSAGE', 'This website uses cookies to ensure you get the best experience on our website.' );
define( 'SSCB_DEFAULT_DISMISS', 'Got it!' );
define( 'SSCB_DEFAULT_LINK', 'Learn More' );
define( 'SSCB_DEFAULT_HREF', '' );

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
$sscb_options = get_option( 'sscb_plugin_options', array() );
if ( ! is_array( $sscb_options ) ) {
  $sscb_options = array();
}
if ( ! empty( $sscb_options['enabled'] ) ) {
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
  $sscb_options = sscb_get_options_with_defaults();
  $sscb_options = sscb_apply_wpml_filters( $sscb_options );
  $sscb_options = sscb_sanitize_options( $sscb_options );

  return sscb_build_json_script( $sscb_options );
}

// Helper: Use default if value is not set, null, or empty string
function sscb_value_or_default( $value, $default ) {
    return ( isset( $value ) && $value !== '' && $value !== null ) ? $value : $default;
}

/**
 * Get Options with Defaults
 *
 * Retrieves plugin options from the database and applies default values
 * where options are not set.
 *
 * @since 1.0.2
 *
 * @return array Options with defaults applied.
 */
function sscb_get_options_with_defaults() {
  $sscb_options = get_option( 'sscb_plugin_options', array() );
  if ( ! is_array( $sscb_options ) ) {
    $sscb_options = array();
  }

  // Apply defaults for basic options
  $sscb_options['theme']    = sscb_value_or_default( $sscb_options['theme']    ?? null, SSCB_DEFAULT_THEME );
  $sscb_options['position'] = sscb_value_or_default( $sscb_options['position'] ?? null, SSCB_DEFAULT_POSITION );
  $sscb_options['bar_bg']   = sscb_value_or_default( $sscb_options['bar_bg']   ?? null, SSCB_DEFAULT_BAR_BG );
  $sscb_options['bar_txt']  = sscb_value_or_default( $sscb_options['bar_txt']  ?? null, SSCB_DEFAULT_BAR_TXT );

  // Handle button colors for "wire" theme
  if ( $sscb_options['theme'] === 'wire' ) {

    //set btn border to btn_bg if not specified. If null fallback to default.
    $sscb_options['btn_border'] = sscb_value_or_default( $sscb_options['btn_border'] ?? null, sscb_value_or_default( $sscb_options['btn_bg'] ?? null, SSCB_DEFAULT_BTN_BORDER ) );

    //set btn text, if null fallaback to border
    $sscb_options['btn_txt']    = sscb_value_or_default( $sscb_options['btn_txt'] ?? null, $sscb_options['btn_border'] );

    //set bg to transparent
    $sscb_options['btn_bg']     = 'transparent';
  } else {
    $sscb_options['btn_bg']     = sscb_value_or_default( $sscb_options['btn_bg'] ?? null, SSCB_DEFAULT_BTN_BG );
    $sscb_options['btn_txt']    = sscb_value_or_default( $sscb_options['btn_txt'] ?? null, SSCB_DEFAULT_BTN_TXT );
    $sscb_options['btn_border'] = sscb_value_or_default( $sscb_options['btn_border'] ?? null, SSCB_DEFAULT_BTN_BORDER );
  }

  // Apply defaults for other options
  $sscb_options['message'] = sscb_value_or_default( $sscb_options['message'] ?? null, SSCB_DEFAULT_MESSAGE );
  $sscb_options['dismiss'] = sscb_value_or_default( $sscb_options['dismiss'] ?? null, SSCB_DEFAULT_DISMISS );
  $sscb_options['link']    = sscb_value_or_default( $sscb_options['link']    ?? null, SSCB_DEFAULT_LINK );
  $sscb_options['href']    = sscb_value_or_default( $sscb_options['href']    ?? null, SSCB_DEFAULT_HREF );

  return $sscb_options;
}

/**
 * Apply WPML Filters
 *
 * Applies WPML translation filters to translatable options.
 *
 * @since 1.0.2
 *
 * @param array $options Options to apply WPML filters to.
 * @return array Options with WPML filters applied.
 */
function sscb_apply_wpml_filters( $options ) {
  $options['message'] = apply_filters( 'wpml_translate_single_string', $options['message'], 'super-simple-cookie-bar', 'Message' );
  $options['dismiss'] = apply_filters( 'wpml_translate_single_string', $options['dismiss'], 'super-simple-cookie-bar', 'Dismiss/Accept Text' );
  $options['link'] = apply_filters( 'wpml_translate_single_string', $options['link'], 'super-simple-cookie-bar', 'Learn More Link Text' );
  $options['href'] = apply_filters( 'wpml_permalink', $options['href'], apply_filters( 'wpml_current_language', null ), true );

  return $options;
}

/**
 * Sanitize Options
 *
 * Sanitizes plugin options to ensure they are safe for use.
 *
 * @since 1.0.2
 *
 * @param array $options Options to sanitize.
 * @return array Sanitized options.
 */
function sscb_sanitize_options( $options ) {
  $theme_allow = array( 'block', 'edgeless', 'classic', 'wire' );
  $pos_allow = array( 'bottom', 'top', 'top-static', 'bottom-left', 'bottom-right' );

  if ( isset( $options['theme'] ) && !in_array( $options['theme'], $theme_allow, true ) ) {
    unset( $options['theme'] );
  }
  if ( isset( $options['position'] ) && !in_array( $options['position'], $pos_allow, true ) ) {
    unset( $options['position'] );
  }

  if ( isset( $options['bar_bg'] ) ) {
    $options['bar_bg'] = sanitize_hex_color( $options['bar_bg'] );
  }
  if ( isset( $options['bar_txt'] ) ) {
    $options['bar_txt'] = sanitize_hex_color( $options['bar_txt'] );
  }
  if ( isset( $options['btn_bg'] ) && $options['btn_bg'] !== 'transparent' ) {
    $options['btn_bg'] = sanitize_hex_color( $options['btn_bg'] );
  }
  if ( isset( $options['btn_txt'] ) ) {
    $options['btn_txt'] = sanitize_hex_color( $options['btn_txt'] );
  }
  if ( isset( $options['btn_border'] ) ) {
    $options['btn_border'] = sanitize_hex_color( $options['btn_border'] );
  }
  if ( isset( $options['message'] ) ) {
    $options['message'] = wp_kses_post( $options['message'] );
  }
  if ( isset( $options['dismiss'] ) ) {
    $options['dismiss'] = sanitize_text_field( $options['dismiss'] );
  }
  if ( isset( $options['link'] ) ) {
    $options['link'] = sanitize_text_field( $options['link'] );
  }
  if ( isset( $options['href'] ) ) {
    $options['href'] = sanitize_url( $options['href'], array( 'http', 'https' ) );
  }

  return $options;
}

/**
 * Build JSON Script
 *
 * Builds the JSON initialization script for the cookie consent bar.
 *
 * @since 1.0.2
 *
 * @param array $options Sanitized options for the cookie bar.
 * @return string JSON initialization script.
 */
function sscb_build_json_script( $options ) {
  // Build the popup array first
  $popup = array(
    'background' => $options['bar_bg'],
    'text'       => $options['bar_txt'],
  );

  // Build the button array based on theme
  if ( $options['theme'] === 'wire' ) {
    $button = array(
      'background' => $options['btn_bg'],
      'text'       => $options['btn_txt'],
      'border'     => $options['btn_border'],
    );
  } else {
    $button = array(
      'background' => $options['btn_bg'],
      'text'       => $options['btn_txt'],
    );
    // Only add 'border' if not default
    if ( strtolower($options['btn_border']) !== strtolower(SSCB_DEFAULT_BTN_BORDER) ) {
      $button['border'] = $options['btn_border'];
    }
  }

  // Build the content array
  $content = array(
    'message' => $options['message'],
    'dismiss' => $options['dismiss'],
    'link'    => $options['link'] ?? false,
    'href'    => $options['href'] ?? '',
  );

  // Build the final config
  $config = array(
    'palette'  => array(
      'popup'  => $popup,
      'button' => $button,
    ),
    'theme'    => $options['theme'],
    'position' => $options['position'] === 'top-static' ? 'top' : $options['position'],
    'static'   => $options['position'] === 'top-static',
    'content'  => $content,
  );

  return 'window.cookieconsent.initialise(' . wp_json_encode( $config ) . ');';
}