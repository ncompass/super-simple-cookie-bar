<?php
/**
 * Plugin Name:       Super Simple Cookie Bar by NCompass
 * Description:       A super simple plugin that provides a super simple cookie bar notice.
 * Version:           0.0.8
 * Author:            NCompass Ltd
 * Author URI:        https://www.ncompass.co.uk
 * License:           GPLv2 or later
 * Text Domain:       super-simple-cookie-bar-by-ncompass
 * GitHub Plugin URI: ncompass/super-simple-cookie-bar-by-ncompass
 * GitHub Plugin URI: https://github.com/ncompass/super-simple-cookie-bar-by-ncompass
 */

 /**
  * Registers settings in admin
  */
require_once __DIR__ . '/includes/ncb-settings.php';

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
function ncb_action_links ( $actions ) {
	$mylinks = array(
		'<a href="' . admin_url( 'options-general.php?page=super-simple-cookie-bar-by-ncompass' ) . '">Settings</a>',
	);
	$actions = array_merge( $actions, $mylinks );

	return $actions;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'ncb_action_links' );

/** If enabled add script, css and json **/
if( get_option( 'ncb_plugin_options' ) ){
	$ncb_options = get_option( 'ncb_plugin_options' );

	if ( true == $ncb_options['enabled'] ){
		add_action( 'wp_enqueue_scripts', 'ncb_enqueue' );
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
function ncb_enqueue() {
	$ncb_options = get_option( 'ncb_plugin_options' );

	if( ( isset($ncb_options['local_assets']) ) && ( true == $ncb_options['local_assets'] ) ){
		wp_enqueue_style( 'ncb-styles',  plugin_dir_url( __FILE__ ) . 'public/css/cookieconsent.min.css' );
		wp_enqueue_script( 'ncb-scripts', plugin_dir_url( __FILE__ ) . 'public/js/cookieconsent.min.js', null, null, true );
	} else{
		wp_enqueue_style( 'ncb-styles',  'https://cdn.jsdelivr.net/npm/cookieconsent@3/build/cookieconsent.min.css' );
		wp_enqueue_script( 'ncb-scripts', 'https://cdn.jsdelivr.net/npm/cookieconsent@3/build/cookieconsent.min.js', null, null, true );
	}

	$script = ncb_init_options();
	wp_add_inline_script( 'ncb-scripts', $script, 'after' );
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
function ncb_init_options(){
	/**
	 *
	 * Collect data or use defaults
	 *
	 */
	$ncb_options = get_option( 'ncb_plugin_options' );



	$ncb_options['theme'] = $ncb_options['theme'] ?? $ncb_options['theme'] ?: 'block';
	$ncb_options['position'] = $ncb_options['position'] ?? $ncb_options['position'] ?: 'bottom';

	$ncb_options['bar_bg'] = $ncb_options['bar_bg'] ?? $ncb_options['bar_bg'] ?: '#000000';
	$ncb_options['bar_txt'] = $ncb_options['bar_txt'] ?? $ncb_options['bar_txt'] ?: '#ffffff';

	$ncb_options['btn_bg'] = $ncb_options['theme'] == 'wire' ? 'transparent' : ( $ncb_options['btn_bg'] ?? $ncb_options['btn_bg'] ?: '#ffffff' );
	//no btn_bg if using wire theme.
	$ncb_options['btn_txt'] = $ncb_options['theme'] == 'wire' ? ( $ncb_options['btn_txt'] ?? $ncb_options['btn_txt'] ?: ( $ncb_options['btn_border'] ?? $ncb_options['btn_border'] ?: '#ffffff' ) ) : ( $ncb_options['btn_txt'] ?? $ncb_options['btn_txt'] ?: '#000000' );
	$ncb_options['btn_border'] = $ncb_options['theme'] == 'wire' ? ( $ncb_options['btn_border'] ?? $ncb_options['btn_border'] ?: ( $ncb_options['btn_txt'] ?? $ncb_options['btn_txt'] ?: '#ffffff' ) ) : ( $ncb_options['btn_border'] ?? $ncb_options['btn_border'] ?: '#ffffff' );
	//this ties btn_border and btn_txt cols together if using wire theme - unless the user is explicitly setting a value for both - otherwise grab indv values or defaults.

	$ncb_options['message'] = $ncb_options['message'] ?? $ncb_options['message'] ?: 'This website uses cookies to ensure you get the best experience on our website.';
	$ncb_options['dismiss'] = $ncb_options['dismiss'] ?? $ncb_options['dismiss'] ?: 'Got it!';
	$ncb_options['link'] = $ncb_options['link'] ?? $ncb_options['link'] ?: 'Learn More';

	/**
	 *
	 * Apply WPML Filters
	 *
	 */
	$ncb_options['message'] = apply_filters( 'wpml_translate_single_string', $ncb_options['message'], 'super-simple-cookie-bar-by-ncompass', 'Message' );
	$ncb_options['dismiss'] = apply_filters( 'wpml_translate_single_string', $ncb_options['dismiss'], 'super-simple-cookie-bar-by-ncompass', 'Dismiss/Accept Text' );
	$ncb_options['link'] = apply_filters( 'wpml_translate_single_string', $ncb_options['link'], 'super-simple-cookie-bar-by-ncompass', 'Learn More Link Text' );
	$ncb_options['href'] = apply_filters( 'wpml_permalink', $ncb_options['href'], apply_filters( 'wpml_current_language', NULL ), true );

	/**
	 *
	 * Santise collected data
	 *
	 */
	$theme_allow = array( 'block','edgeless','classic','wire' );
 	$pos_allow = array( 'bottom','top','top-static','bottom-left','bottom-right' );
	$ncb_options['theme'] = in_array( $ncb_options['theme'], $theme_allow ) ? $ncb_options['theme'] : 'block';
	$ncb_options['position'] = in_array( $ncb_options['position'], $pos_allow ) ? $ncb_options['position'] : 'bottom';

	$ncb_options['bar_bg'] = sanitize_hex_color( $ncb_options['bar_bg'] );
	$ncb_options['bar_txt'] = sanitize_hex_color( $ncb_options['bar_txt'] );

	$ncb_options['btn_bg'] = sanitize_hex_color( $ncb_options['btn_bg'] );
	$ncb_options['btn_txt'] = sanitize_hex_color( $ncb_options['btn_txt'] );
	$ncb_options['btn_border'] = sanitize_hex_color( $ncb_options['btn_border'] );

	$ncb_options['message'] = sanitize_text_field( $ncb_options['message'] );
	$ncb_options['dismiss'] = sanitize_text_field( $ncb_options['dismiss'] );
	$ncb_options['link'] = sanitize_text_field( $ncb_options['link'] );
	$ncb_options['href'] = sanitize_url( $ncb_options['href'], array( 'http','https' ) );

	/**
	 *
	 * Build json init script
	 *
	 */
	$script = 'window.cookieconsent.initialise({
		"palette": {
			"popup": {
				"background": "'.$ncb_options['bar_bg'].'",
				"text": "'.$ncb_options['bar_txt'].'"
			},
			"button": {
				"background": "'.$ncb_options['btn_bg'].'",
				"text": "'.$ncb_options['btn_txt'].'",
				"border": "'.$ncb_options['btn_border'].'"
			},
		},
		"theme": "'.$ncb_options['theme'].'",
		';

		//position
		if ( 'top-static' == $ncb_options['position'] ){
			$script .= '"position": "top",
			"static": true,
			';
		} else{
			$script .= '"position": "'.$ncb_options['position'].'",
			';
		}

		//content
		$script .= '"content": {
			"message": "' . $ncb_options['message'] . '",
			"dismiss": "' . $ncb_options['dismiss'] . '",
			';
			if ( NULL != $ncb_options['href'] ){
				$script .= '"link": "' . $ncb_options['link'] . '",
				"href": "' . $ncb_options['href'] . '"
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
