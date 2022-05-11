<?php
/**
 * Plugin Name:       NCompass Simple Cookie Bar
 * Plugin URI:        https://www.ncompass.co.uk
 * Description:       A super simple plugin that provides a super simple cookie bar notice.
 * Version:           0.0.6
 * Author:            NCompass Ltd
 * Author URI:        https://www.ncompass.co.uk
 * License:           GPLv2 or later
 * Text Domain:       ncompass-cookie-bar
 * GitHub Plugin URI: ncompass/ncompass-cookie-bar
 * GitHub Plugin URI: https://github.com/ncompass/ncompass-cookie-bar
 */

/** Settings Page **/
require('includes/ncb-settings.php');

/** Add settings link to plugins page **/
function ncb_action_links ( $actions ) {
   $mylinks = array(
      '<a href="' . admin_url( 'options-general.php?page=ncompass-cookie-bar' ) . '">Settings</a>',
   );
   $actions = array_merge( $actions, $mylinks );
   return $actions;
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'ncb_action_links' );

/** If enabled add scipt, css and json **/
if(get_option('ncb_plugin_options')){
  $ncb_options = get_option('ncb_plugin_options');
}
if ($ncb_options['enabled'] == true){
  add_action( 'wp_enqueue_scripts', 'ncb_enqueue' );
}

function ncb_enqueue() {
    wp_enqueue_style( 'ncb-styles',  'https://cdn.jsdelivr.net/npm/cookieconsent@3/build/cookieconsent.min.css' );
    wp_enqueue_script( 'ncb-scripts', 'https://cdn.jsdelivr.net/npm/cookieconsent@3/build/cookieconsent.min.js', null, null, true );

    $script = ncb_init_options();
    wp_add_inline_script('ncb-scripts', $script, 'after');
}

function ncb_init_options(){
  $ncb_options = get_option('ncb_plugin_options');

  //collect data or defaults
  $ncb_options['theme'] = ( $ncb_options['theme'] ?? $ncb_options['theme'] ?: 'block' );
  $ncb_options['position'] = ( $ncb_options['position'] ?? $ncb_options['position'] ?: 'bottom' );

  $ncb_options['bar_bg'] = ( $ncb_options['bar_bg'] ?? $ncb_options['bar_bg'] ?: '#000' );
  $ncb_options['bar_txt'] = ( $ncb_options['bar_txt'] ?? $ncb_options['bar_txt'] ?: '#fff' );

  $ncb_options['btn_bg'] = ( $ncb_options['theme'] == 'wire' ? 'transparent' : ($ncb_options['btn_bg'] ?? $ncb_options['btn_bg'] ?: '#fff') );
  $ncb_options['btn_txt'] = ( $ncb_options['theme'] == 'wire' ? ($ncb_options['btn_border'] ?? $ncb_options['btn_border'] ?: '#fff') : ($ncb_options['btn_txt'] ?? $ncb_options['btn_txt'] ?: '#000') );
  $ncb_options['btn_border'] = ( $ncb_options['btn_border'] ?? $ncb_options['btn_border'] ?: '#fff' );

  $ncb_options['message'] = ( $ncb_options['message'] ?? $ncb_options['message'] ?: 'This website uses cookies to ensure you get the best experience on our website.' );
  $ncb_options['dismiss'] = ( $ncb_options['dismiss'] ?? $ncb_options['dismiss'] ?: 'Got it!' );
  $ncb_options['link'] = ( $ncb_options['link'] ?? $ncb_options['link'] ?: 'Learn More' );


  //Apply WPML String translation
  $ncb_options['message'] = apply_filters('wpml_translate_single_string', $ncb_options['message'], 'ncompass-cookie-bar', 'Message');
  $ncb_options['dismiss'] = apply_filters('wpml_translate_single_string', $ncb_options['dismiss'], 'ncompass-cookie-bar', 'Dismiss/Accept Text');
  $ncb_options['link'] = apply_filters('wpml_translate_single_string', $ncb_options['link'], 'ncompass-cookie-bar', 'Learn More Link Text');
  $ncb_options['href'] = ( $ncb_options['href'] != NULL ? apply_filters('wpml_permalink', $ncb_options['href'], apply_filters( 'wpml_current_language', NULL ), true) : NULL);

  //build json init script
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
    if ($ncb_options['position'] == 'top-static'){
      $script .= '"position": "top",
      "static": true,
      ';
    }
    else{
      $script .= '"position": "'.$ncb_options['position'].'",
      ';
    }

    //content
    $script .= '"content": {
      "message": "'.$ncb_options['message'].'",
      "dismiss": "'.$ncb_options['dismiss'].'",
      ';
    if ($ncb_options['href'] != NULL){

      $script .= '"link": "'.$ncb_options['link'].'",
      "href": "'.$ncb_options['href'].'"
      ';
    }
    else{
      $script .= '"link": false
      ';
    }
    $script .= '}
    ';
  $script .= '});
  ';

  return $script;
}
