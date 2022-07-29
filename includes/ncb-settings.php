<?php
/**
 * Register Settings Page
 *
 * Registers the settings page to wp-admin
 *
 * @since 1.0.0
 *
 *
 */
function ncb_add_settings_page() {
	add_options_page( 'Super Simple Cookie Bar', 'Super Simple Cookie Bar', 'manage_options', 'super-simple-cookie-bar-by-ncompass', 'ncb_render_plugin_settings_page' );
}
add_action( 'admin_menu', 'ncb_add_settings_page' );


/**
 * Render Settings Page
 *
 * Renders settings page content and form
 *
 * @since 1.0.0
 *
 *
 */
function ncb_render_plugin_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<h1><?php echo __( 'Super Simple Cookie Bar by NCompass', 'super-simple-cookie-bar-by-ncompass' ); ?></h1>

	<form action="options.php" method="post">
		<?php
			settings_fields( 'ncb_plugin_options' );
			do_settings_sections( 'ncb_plugin' );
		?>
		<input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
	</form>
	<?php
}


/**
 * Register Settings
 *
 * Registers all necessary settings
 *
 * @since 1.0.0
 *
 *
 */
function ncb_register_settings() {
	register_setting( 'ncb_plugin_options', 'ncb_plugin_options', 'ncb_plugin_options_validate' );
	add_settings_section(
		'ncb_general',
		__('General Settings', 'super-simple-cookie-bar-by-ncompass'),
		'ncb_general_text',
		'ncb_plugin'
	);
	add_settings_field( 'ncb_plugin_setting_enabled', 'Enable Cookie Bar', 'ncb_plugin_setting_enabled', 'ncb_plugin', 'ncb_general' );
	add_settings_field( 'ncb_plugin_setting_theme', 'Theme', 'ncb_plugin_setting_theme', 'ncb_plugin', 'ncb_general' );
	add_settings_field( 'ncb_plugin_setting_position', 'Position', 'ncb_plugin_setting_position', 'ncb_plugin', 'ncb_general' );
	add_settings_field( 'ncb_plugin_setting_local_assets', 'Load Local Assets', 'ncb_plugin_setting_local_assets', 'ncb_plugin', 'ncb_general' );

	add_settings_section(
		'ncb_bar_styles',
		__('Cookie Bar Styles', 'super-simple-cookie-bar-by-ncompass'),
		'ncb_bar_styles_text',
		'ncb_plugin'
	);
	add_settings_field( 'ncb_plugin_setting_bar_bg', 'Background Colour', 'ncb_plugin_setting_bar_bg', 'ncb_plugin', 'ncb_bar_styles' );
	add_settings_field( 'ncb_plugin_setting_bar_txt', 'Text Colour', 'ncb_plugin_setting_bar_txt', 'ncb_plugin', 'ncb_bar_styles' );

	add_settings_section(
		'ncb_btn_styles',
		__('Cookie Button Styles', 'super-simple-cookie-bar-by-ncompass'),
		'ncb_btn_styles_text',
		'ncb_plugin'
	);
	add_settings_field( 'ncb_plugin_setting_btn_bg', 'Background Colour', 'ncb_plugin_setting_btn_bg', 'ncb_plugin', 'ncb_btn_styles' );
	add_settings_field( 'ncb_plugin_setting_btn_txt', 'Text Colour', 'ncb_plugin_setting_btn_txt', 'ncb_plugin', 'ncb_btn_styles' );
	add_settings_field( 'ncb_plugin_setting_btn_border', 'Border Colour', 'ncb_plugin_setting_btn_border', 'ncb_plugin', 'ncb_btn_styles' );

	add_settings_section(
		'ncb_content',
		__('Content Settings', 'super-simple-cookie-bar-by-ncompass'),
		'ncb_content_text',
		'ncb_plugin'
	);
	add_settings_field( 'ncb_plugin_setting_message', 'Message', 'ncb_plugin_setting_message', 'ncb_plugin', 'ncb_content' );
	add_settings_field( 'ncb_plugin_setting_dismiss', 'Dismiss/Accept Text', 'ncb_plugin_setting_dismiss', 'ncb_plugin', 'ncb_content' );
	add_settings_field( 'ncb_plugin_setting_link', 'Learn More Link Text', 'ncb_plugin_setting_link', 'ncb_plugin', 'ncb_content' );
	add_settings_field( 'ncb_plugin_setting_href', 'Learn More Link', 'ncb_plugin_setting_href', 'ncb_plugin', 'ncb_content' );
}
add_action( 'admin_init', 'ncb_register_settings' );


/**
 * Validate settings on submit
 *
 * Validation and sanitisation of various settings on submit. Also used to register WPML strings for string translation support.
 *
 * @since 1.0.0
 *
 *
 */
function ncb_plugin_options_validate( $input ) {
	$old_options = get_option( 'ncb_plugin_options' );
	$has_errors = false;

	/**
	 *
	 * General Settings Validation
	 *
	 */
	$theme_allowed = array( 'block','edgeless','classic','wire' );
	if( ! in_array( $input['theme'], $theme_allowed ) ){
		//check theme is an allowed value
		add_settings_error( 'ncb_plugin_options', 'ncb_theme_error', __( 'Theme: This is not a valid value', 'super-simple-cookie-bar-by-ncompass' ), 'error' );

    $has_errors = true;
	}

	$position_allowed = array( 'bottom','top','top-static','bottom-left','bottom-right' );
	if( ! in_array( $input['position'], $position_allowed ) ){
		//check position is an allowed value
		add_settings_error( 'ncb_plugin_options', 'ncb_position_error', __( 'Position: This is not a valid value', 'super-simple-cookie-bar-by-ncompass' ), 'error' );

    $has_errors = true;
	}

	/**
	 *
	 * Bar Styles Validation
	 *
	 */
	if( ( $input['bar_bg'] ) &&  ( ! sanitize_hex_color( $input['bar_bg'] ) ) ){
		//check bar_bg is valid hex if entered.
		add_settings_error( 'ncb_plugin_options', 'ncb_bar_bg_error', __( 'Cookie Bar - Background Colour: You must enter a valid HTML hex colour code e.g. #000000 or #000', 'super-simple-cookie-bar-by-ncompass' ), 'error' );

    $has_errors = true;
	}
	if( ( $input['bar_txt'] ) &&  ( ! sanitize_hex_color( $input['bar_txt'] ) ) ){
		//check bar_txt is valid hex if entered.
		add_settings_error( 'ncb_plugin_options', 'ncb_bar_txt_error', __( 'Cookie Bar - Text Colour: You must enter a valid HTML hex colour code e.g. #ffffff or #fff', 'super-simple-cookie-bar-by-ncompass' ), 'error' );

    $has_errors = true;
	}

	/**
	 *
	 * Button Styles Validation
	 *
	 */
	if( ( $input['btn_bg'] ) &&  ( ! sanitize_hex_color( $input['btn_bg'] ) ) ){
		//check btn_bg is valid hex if entered.
		add_settings_error( 'ncb_plugin_options', 'ncb_btn_bg_error', __( 'Cookie Button - Background Colour: You must enter a valid HTML hex colour code e.g. #ffffff or #fff', 'super-simple-cookie-bar-by-ncompass' ), 'error' );

    $has_errors = true;
	}
	if( ( $input['btn_txt'] ) &&  ( ! sanitize_hex_color( $input['btn_txt'] ) ) ){
		//check btn_txt is valid hex if entered.
		add_settings_error( 'ncb_plugin_options', 'ncb_btn_txt_error', __( 'Cookie Button - Text Colour: You must enter a valid HTML hex colour code e.g. #00000 or #000', 'super-simple-cookie-bar-by-ncompass' ), 'error' );

    $has_errors = true;
	}
	if( ( $input['btn_border'] ) &&  ( ! sanitize_hex_color( $input['btn_border'] ) ) ){
		//check btn_border is valid hex if entered.
		add_settings_error( 'ncb_plugin_options', 'ncb_btn_border_error', __( 'Cookie Button - Border Colour: You must enter a valid HTML hex colour code e.g. #00000 or #000', 'super-simple-cookie-bar-by-ncompass' ), 'error' );

    $has_errors = true;
	}

	/**
	 *
	 * Sanitize Content fields
	 *
	 */
	$input['message'] = sanitize_text_field( $input['message'] );
	$input['dismiss'] = sanitize_text_field( $input['dismiss'] );
	$input['link'] = sanitize_text_field( $input['link'] );
	$input['href'] = sanitize_url( $input['href'], array( 'http','https' ) );


	/**
	 *
	 * Use old options if there is an error
	 *
	 */
	if ( $has_errors ){
		$input = $old_options;
	}

	/**
	 *
	 * Register user strings (if set) or  our defaults in WPML
	 *
	 */
	do_action( 'wpml_register_single_string', 'super-simple-cookie-bar-by-ncompass', 'Message', ( $input['message'] ?? $input['message'] ?: 'This website uses cookies to ensure you get the best experience on our website.' ) );
	do_action( 'wpml_register_single_string', 'super-simple-cookie-bar-by-ncompass', 'Dismiss/Accept Text', ( $input['dismiss'] ?? $input['dismiss'] ?: 'Got it!' ) );
	do_action( 'wpml_register_single_string', 'super-simple-cookie-bar-by-ncompass', 'Learn More Link Text', ( $input['link'] ?? $input['link'] ?: 'Learn More' ) );

	return $input;
}

/**
 * Render Settings - General settings text
 *
 * Intro text for general settings section.
 *
 * @since 1.0.0
 *
 *
 */
function ncb_general_text() {
	//we could add some section info here and/or error messages
}

/**
 * Render Settings - Plugin Enable/Disable
 *
 * Adds a tickbox to settings page so the user can turn the frontend output of plugin on/off
 *
 * @since 1.0.0
 *
 *
 */
function ncb_plugin_setting_enabled() {
	$options = get_option( 'ncb_plugin_options' );
	echo "<input id='ncb_plugin_setting_enabled' name='ncb_plugin_options[enabled]' type='checkbox' value='1'" . checked( 1, $options['enabled'], false ) .  "' />";
}

/**
 * Render Settings - Cookie Bar Theme
 *
 * Adds a select field to settings page that allows user to choose the theme
 *
 * @since 1.0.0
 *
 *
 */
function ncb_plugin_setting_theme() {
	$options = get_option( 'ncb_plugin_options' );
	echo "<select name='ncb_plugin_options[theme]'>";
		echo "<option value='block' ".selected( $options['theme'], "block" )." >".__(' Block', 'super-simple-cookie-bar-by-ncompass' )."</option>";
		echo "<option value='edgeless' ".selected( $options['theme'], "edgeless" )." >".__( 'Edgeless', 'super-simple-cookie-bar-by-ncompass' )."</option>";
		echo "<option value='classic' ".selected( $options['theme'], "classic" )." >".__( 'Classic', 'super-simple-cookie-bar-by-ncompass' )."</option>";
		echo "<option value='wire' ".selected( $options['theme'], "wire" )." >".__( 'Wire', 'ncompass-cooke-bar' )."</option>";
  echo "</select>";
}

/**
 * Render Settings - Cookie Bar Position
 *
 * Adds a select field to settings page that allows user to choose the cookie bar position
 *
 * @since 1.0.0
 *
 *
 */
function ncb_plugin_setting_position() {
	$options = get_option( 'ncb_plugin_options' );
	echo "<select name='ncb_plugin_options[position]'>";
		echo "<option value='bottom' ".selected( $options['position'], "bottom" )." >".__( 'Banner Bottom', 'super-simple-cookie-bar-by-ncompass' )."</option>";
		echo "<option value='top' ".selected( $options['position'], "top" )." >".__( 'Banner Top', 'super-simple-cookie-bar-by-ncompass' )."</option>";
		echo "<option value='top-static' ".selected( $options['position'], "top-static" )." >".__( 'Banner Top (Pushdown)', 'super-simple-cookie-bar-by-ncompass' )."</option>";
		echo "<option value='bottom-left' ".selected( $options['position'], "bottom-left" )." >".__( 'Floating Left', 'super-simple-cookie-bar-by-ncompass' )."</option>";
		echo "<option value='bottom-right' ".selected( $options['position'], "bottom-right" )." >".__( 'Floating Right', 'super-simple-cookie-bar-by-ncompass' )."</option>";
	echo "</select>";
}

/**
 * Render Settings - Local Assets Enable
 *
 * Adds a tickbox to allow user to use locally hosted CSS/JS files
 *
 * @since 1.0.0
 *
 *
 */
function ncb_plugin_setting_local_assets() {
	$options = get_option( 'ncb_plugin_options' );
	echo "<input id='ncb_plugin_setting_local_assets' name='ncb_plugin_options[local_assets]' type='checkbox' value='1'" . checked( 1, $options['local_assets'], false ) .  "' />";
}

/**
 * Render Settings - Bar Style settings text
 *
 * Intro text for bar style settings section.
 *
 * @since 1.0.0
 *
 *
 */
function ncb_bar_styles_text() {

}

/**
 * Render Settings - Bar Background Colour
 *
 * Adds a text field to settings page that allows user to change the Cookie bar background colour
 *
 * @since 1.0.0
 *
 *
 */
function ncb_plugin_setting_bar_bg() {
	$options = get_option( 'ncb_plugin_options' );
	echo "<input id='ncb_plugin_setting_bar_bg' name='ncb_plugin_options[bar_bg]' type='text' placeholder='#000000' value='" . esc_attr( $options['bar_bg'] ) . "' />";
}

/**
 * Render Settings - Bar Text Colour
 *
 * Adds a text field to settings page that allows user to change the Cookie bar text colour
 *
 * @since 1.0.0
 *
 *
 */
function ncb_plugin_setting_bar_txt() {
	$options = get_option( 'ncb_plugin_options' );
	echo "<input id='ncb_plugin_setting_bar_txt' name='ncb_plugin_options[bar_txt]' placeholder='#ffffff' type='text' value='" . esc_attr( $options['bar_txt'] ) . "' />";
}

/**
 * Render Settings - Button Style settings text
 *
 * Intro text for button style settings section.
 *
 * @since 1.0.0
 *
 *
 */
function ncb_btn_styles_text() {

}

/**
 * Render Settings - Button Background Colour
 *
 * Adds a text field to settings page that allows user to change the button background colour
 *
 * @since 1.0.0
 *
 *
 */
function ncb_plugin_setting_btn_bg() {
	$options = get_option( 'ncb_plugin_options' );
	echo "<input id='ncb_plugin_setting_btn_bg' name='ncb_plugin_options[btn_bg]' placeholder='#ffffff' type='text' value='" . esc_attr( $options['btn_bg'] ) . "' />";
}

/**
 * Render Settings - Button Text Colour
 *
 * Adds a text field to settings page that allows user to change the button text colour
 *
 * @since 1.0.0
 *
 *
 */
function ncb_plugin_setting_btn_txt() {
	$options = get_option( 'ncb_plugin_options' );
	echo "<input id='ncb_plugin_setting_btn_txt' name='ncb_plugin_options[btn_txt]' placeholder='#000000' type='text' value='" . esc_attr( $options['btn_txt'] ) . "' />";
}

/**
 * Render Settings - Button Text Colour
 *
 * Adds a text field to settings page that allows user to change the button border colour
 *
 * @since 1.0.0
 *
 *
 */
function ncb_plugin_setting_btn_border() {
	$options = get_option( 'ncb_plugin_options' );
	echo "<input id='ncb_plugin_setting_btn_border' name='ncb_plugin_options[btn_border]' placeholder='#000000' type='text' value='" . esc_attr( $options['btn_border'] ) . "' />";
}

/**
 * Render Settings - Content settings text
 *
 * Intro text for content settings section.
 *
 * @since 1.0.0
 *
 *
 */
function ncb_content_text() {

}

/**
 * Render Settings - Cookie Bar Message
 *
 * Adds a text field to settings page that allows user to change the cookie bar notice/message
 *
 * @since 1.0.0
 *
 *
 */
function ncb_plugin_setting_message() {
	$options = get_option( 'ncb_plugin_options' );
	echo "<input id='ncb_plugin_setting_message' name='ncb_plugin_options[message]' type='text' placeholder='".__( 'This website uses cookies to ensure you get the best experience on our website.', 'super-simple-cookie-bar-by-ncompass' )."' value='" . esc_attr( $options['message'] ) . "' style='width:100%;' />";
}

/**
 * Render Settings - Dismiss text
 *
 * Adds a text field to settings page that allows user to set the dismiss button text
 *
 * @since 1.0.0
 *
 *
 */
function ncb_plugin_setting_dismiss() {
	$options = get_option( 'ncb_plugin_options' );
	echo "<input id='ncb_plugin_setting_dismiss' name='ncb_plugin_options[dismiss]' type='text' placeholder='".__( 'Got it!', 'super-simple-cookie-bar-by-ncompass' )."' value='" . esc_attr( $options['dismiss'] ) . "' />";
}

/**
 * Render Settings - "Learn More", Privacy Policy/Cookie Notice Link Text
 *
 * Adds a text field to settings page that allows user to change the "Learn More" privacy policy/cookie-statement link text
 *
 * @since 1.0.0
 *
 *
 */
function ncb_plugin_setting_link() {
	$options = get_option( 'ncb_plugin_options' );
	echo "<input id='ncb_plugin_setting_link' name='ncb_plugin_options[link]' type='text' placeholder='".__( 'Learn more', 'super-simple-cookie-bar-by-ncompass' )."' value='" . esc_attr( $options['link'] ) . "' />";
}

/**
 * Render Settings - "Learn More", Privacy Policy/Cookie Notice Link URL
 *
 * Adds a text field to settings page that allows user to change the "Learn More" privacy policy/cookie-statement link url
 *
 * @since 1.0.0
 *
 *
 */
function ncb_plugin_setting_href() {
	$options = get_option( 'ncb_plugin_options' );
	echo "<input id='ncb_plugin_setting_href' name='ncb_plugin_options[href]' type='text' placeholder='".__( 'e.g. /privacy-policy', 'super-simple-cookie-bar-by-ncompass' )."' value='" . esc_attr( $options['href'] ) . "' />";
}
