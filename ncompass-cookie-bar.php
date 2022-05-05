<?php
/**
 * Plugin Name:       NCompass Simple Cookie Bar
 * Plugin URI:        https://www.ncompass.co.uk
 * Description:       A super simple plugin that provides a super simple cookie bar notice.
 * Version:           0.0.1
 * Author:            NCompass Ltd
 * Author URI:        https://www.ncompass.co.uk
 * License:           GPLv2 or later
 * Text Domain:       ncompass-cookie-bar
 * GitHub Plugin URI: ncompass/ncompass-cookie-bar
 * GitHub Plugin URI: https://github.com/ncompass/ncompass-cookie-bar
 */

/** Settings Page **/
require('includes/ncb-settings.php');

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
        "background": "'.$ncb_options['bar_bg'].'",
        "text": "'.$ncb_options['bar_txt'].'"
      },
      "button": {
        "background": "'.$ncb_options['btn_bg'].'",
        "text": "'.$ncb_options['btn_txt'].'",
        "border": "'.$ncb_options['btn_border'].'"
      },
    },
    "content": {
      link: false
    },
    "theme": "edgeless"
  });';
  return $script;
}
