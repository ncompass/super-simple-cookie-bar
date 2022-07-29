<?php

// if uninstall.php is not called by WordPress, die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

//delete plugin options from DB on uninstall
delete_option( 'ncb_plugin_options' );
