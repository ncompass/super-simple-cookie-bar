<?php
/**
 * Plugin Name:       NCompass Simple Cookie Bar
 * Plugin URI:        https://www.ncompass.co.uk
 * Description:       A super simple plugin that provides a super simple cookie bar notice.
 * Version:           0.0.3
 * Author:            NCompass Ltd
 * Author URI:        https://www.ncompass.co.uk
 * License:           GPLv2 or later
 * Text Domain:       ncompass-cookie-bar
 * GitHub Plugin URI: ncompass/ncompass-cookie-bar
 * GitHub Plugin URI: https://github.com/ncompass/ncompass-cookie-bar
 */

/** Settings Page **/
require('includes/ncb-settings.php');
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

  $script = 'window.cookieconsent.initialise({
    "palette": {
      "popup": {
        "background": "'.($ncb_options['bar_bg'] ?? '#000').'",
        "text": "'.($ncb_options['bar_txt'] ?? '#fff').'"
      },
      "button": {
        "background": "'.($ncb_options['btn_bg'] ?? '#fff').'",
        "text": "'.($ncb_options['btn_txt'] ?? '#000').'",
        "border": "'.($ncb_options['btn_border'] ?? '#fff').'"
      },
    },
    "content": {
      link: false
    },
    "theme": "edgeless",';
    if ($ncb_options['position'] == 'top-static'){
      $script .= '"position": "top",
      "static": true';
    }
    else{
      $script .= '"position": "'.($ncb_options['position'] ?? 'bottom').'"';
    }
  $script .= '});';
  return $script;
}
