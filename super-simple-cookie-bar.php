<?php
/**
 * Plugin Name:       Super Simple Cookie Bar
 * Description:       A super simple plugin that provides a super simple cookie bar notice.
 * Version:           1.0.0
 * Author:            NCompass
 * Author URI:        https://www.ncompass.co.uk
 * License:           GPLv2 or later
 * Text Domain:       super-simple-cookie-bar
 * GitHub Plugin URI: ncompass/super-simple-cookie-bar
 * GitHub Plugin URI: https://github.com/ncompass/super-simple-cookie-bar
 */

 /**
  * Registers settings in admin
  */
require_once __DIR__ . '/includes/sscb-settings.php';

/**
 * Register settings link
 *
 * Register settings page link in wordpress options
 *
 * @since 1.0.0
 *
 *
 * @param type $actions Existing settings links array collected from "plugin_action_links"
 * @return type Merged array with our settings link
 */
function sscb_action_links ( $actions ) {
	$mylinks = array(
		'<a href="' . admin_url( 'options-general.php?page=super-simple-cookie-bar' ) . '">Settings</a>',
	);
	$actions = array_merge( $actions, $mylinks );

	return $actions;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'sscb_action_links' );

/** If enabled add script, css and json **/
if( get_option( 'sscb_plugin_options' ) ){
	$sscb_options = get_option( 'sscb_plugin_options' );

	if ( ( isset( $sscb_options['enabled'] ) ) && ( true == $sscb_options['enabled'] ) ){
		add_action( 'wp_enqueue_scripts', 'sscb_enqueue' );
	}
}


/**
 * Enqueue Assets
 *
 * Enqueues css, js, and inline init script on front end
 *
 * @since 1.0.0
 *
 *
 */
function sscb_enqueue() {
	$sscb_options = get_option( 'sscb_plugin_options' );

	wp_enqueue_style( 'sscb-styles',  plugin_dir_url( __FILE__ ) . 'public/css/cookieconsent.min.css' );
	wp_enqueue_script( 'sscb-scripts', plugin_dir_url( __FILE__ ) . 'public/js/cookieconsent.min.js', null, null, true );

	$script = sscb_init_options();
	wp_add_inline_script( 'sscb-scripts', $script, 'after' );
}

/**
 * Build Init
 *
 * Builds the json init script based on the users chosen settings
 *
 * @since 1.0.0
 *
 *
 */
function sscb_init_options(){
	/**
	 *
	 * Collect data or use defaults
	 *
	 */
	$sscb_options = get_option( 'sscb_plugin_options' );



	$sscb_options['theme'] = $sscb_options['theme'] ?? $sscb_options['theme'] ?: 'block';
	$sscb_options['position'] = $sscb_options['position'] ?? $sscb_options['position'] ?: 'bottom';

	$sscb_options['bar_bg'] = $sscb_options['bar_bg'] ?? $sscb_options['bar_bg'] ?: '#000000';
	$sscb_options['bar_txt'] = $sscb_options['bar_txt'] ?? $sscb_options['bar_txt'] ?: '#ffffff';

	$sscb_options['btn_bg'] = $sscb_options['theme'] == 'wire' ? 'transparent' : ( $sscb_options['btn_bg'] ?? $sscb_options['btn_bg'] ?: '#ffffff' );
	//no btn_bg if using wire theme.
	$sscb_options['btn_txt'] = $sscb_options['theme'] == 'wire' ? ( $sscb_options['btn_txt'] ?? $sscb_options['btn_txt'] ?: ( $sscb_options['btn_border'] ?? $sscb_options['btn_border'] ?: '#ffffff' ) ) : ( $sscb_options['btn_txt'] ?? $sscb_options['btn_txt'] ?: '#000000' );
	$sscb_options['btn_border'] = $sscb_options['theme'] == 'wire' ? ( $sscb_options['btn_border'] ?? $sscb_options['btn_border'] ?: ( $sscb_options['btn_txt'] ?? $sscb_options['btn_txt'] ?: '#ffffff' ) ) : ( $sscb_options['btn_border'] ?? $sscb_options['btn_border'] ?: '#ffffff' );
	//this ties btn_border and btn_txt cols together if using wire theme - unless the user is explicitly setting a value for both - otherwise grab indv values or defaults.

	$sscb_options['message'] = $sscb_options['message'] ?? $sscb_options['message'] ?: 'This website uses cookies to ensure you get the best experience on our website.';
	$sscb_options['dismiss'] = $sscb_options['dismiss'] ?? $sscb_options['dismiss'] ?: 'Got it!';
	$sscb_options['link'] = $sscb_options['link'] ?? $sscb_options['link'] ?: 'Learn More';

	/**
	 *
	 * Apply WPML Filters
	 *
	 */
	$sscb_options['message'] = apply_filters( 'wpml_translate_single_string', $sscb_options['message'], 'super-simple-cookie-bar', 'Message' );
	$sscb_options['dismiss'] = apply_filters( 'wpml_translate_single_string', $sscb_options['dismiss'], 'super-simple-cookie-bar', 'Dismiss/Accept Text' );
	$sscb_options['link'] = apply_filters( 'wpml_translate_single_string', $sscb_options['link'], 'super-simple-cookie-bar', 'Learn More Link Text' );
	$sscb_options['href'] = apply_filters( 'wpml_permalink', $sscb_options['href'], apply_filters( 'wpml_current_language', NULL ), true );

	/**
	 *
	 * Santise collected data
	 *
	 */
	$theme_allow = array( 'block','edgeless','classic','wire' );
 	$pos_allow = array( 'bottom','top','top-static','bottom-left','bottom-right' );
	$sscb_options['theme'] = in_array( $sscb_options['theme'], $theme_allow ) ? $sscb_options['theme'] : 'block';
	$sscb_options['position'] = in_array( $sscb_options['position'], $pos_allow ) ? $sscb_options['position'] : 'bottom';

	$sscb_options['bar_bg'] = sanitize_hex_color( $sscb_options['bar_bg'] );
	$sscb_options['bar_txt'] = sanitize_hex_color( $sscb_options['bar_txt'] );

	if( $sscb_options['btn_bg'] != 'transparent' ){
		$sscb_options['btn_bg'] = sanitize_hex_color( $sscb_options['btn_bg'] );
	}
	$sscb_options['btn_txt'] = sanitize_hex_color( $sscb_options['btn_txt'] );
	$sscb_options['btn_border'] = sanitize_hex_color( $sscb_options['btn_border'] );

	$sscb_options['message'] = sanitize_text_field( $sscb_options['message'] );
	$sscb_options['dismiss'] = sanitize_text_field( $sscb_options['dismiss'] );
	$sscb_options['link'] = sanitize_text_field( $sscb_options['link'] );
	$sscb_options['href'] = sanitize_url( $sscb_options['href'], array( 'http','https' ) );

	/**
	 *
	 * Build json init script
	 *
	 */
	$script = 'window.cookieconsent.initialise({
		"palette": {
			"popup": {
				"background": "'.$sscb_options['bar_bg'].'",
				"text": "'.$sscb_options['bar_txt'].'"
			},
			"button": {
				"background": "'.$sscb_options['btn_bg'].'",
				"text": "'.$sscb_options['btn_txt'].'",
				"border": "'.$sscb_options['btn_border'].'"
			},
		},
		"theme": "'.$sscb_options['theme'].'",
		';

		//position
		if ( 'top-static' == $sscb_options['position'] ){
			$script .= '"position": "top",
			"static": true,
			';
		} else{
			$script .= '"position": "'.$sscb_options['position'].'",
			';
		}

		//content
		$script .= '"content": {
			"message": "' . $sscb_options['message'] . '",
			"dismiss": "' . $sscb_options['dismiss'] . '",
			';
			if ( NULL != $sscb_options['href'] ){
				$script .= '"link": "' . $sscb_options['link'] . '",
				"href": "' . $sscb_options['href'] . '"
				';
			} else{
				$script .= '"link": false
				';
			}
		$script .= '}
		';//end content

	$script .= '});
	';//end init
	return $script;
}
