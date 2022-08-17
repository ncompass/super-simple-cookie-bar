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
function sscb_add_settings_page() {
	add_options_page( 'Super Simple Cookie Bar', 'Super Simple Cookie Bar', 'manage_options', 'super-simple-cookie-bar', 'sscb_render_plugin_settings_page' );
}
add_action( 'admin_menu', 'sscb_add_settings_page' );


/**
 * Render Settings Page
 *
 * Renders settings page content and form
 *
 * @since 1.0.0
 *
 *
 */
function sscb_render_plugin_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<h1><?php echo __( 'Super Simple Cookie Bar', 'super-simple-cookie-bar' ); ?></h1>

	<form action="options.php" method="post">
		<?php
			settings_fields( 'sscb_plugin_options' );
			do_settings_sections( 'sscb_plugin' );
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
function sscb_register_settings() {
	register_setting( 'sscb_plugin_options', 'sscb_plugin_options', 'sscb_plugin_options_validate' );
	add_settings_section(
		'sscb_general',
		__('General Settings', 'super-simple-cookie-bar'),
		'sscb_general_text',
		'sscb_plugin'
	);
	add_settings_field( 'sscb_plugin_setting_enabled', 'Enable Cookie Bar', 'sscb_plugin_setting_enabled', 'sscb_plugin', 'sscb_general' );
	add_settings_field( 'sscb_plugin_setting_theme', 'Theme', 'sscb_plugin_setting_theme', 'sscb_plugin', 'sscb_general' );
	add_settings_field( 'sscb_plugin_setting_position', 'Position', 'sscb_plugin_setting_position', 'sscb_plugin', 'sscb_general' );

	add_settings_section(
		'sscb_bar_styles',
		__('Cookie Bar Styles', 'super-simple-cookie-bar'),
		'sscb_bar_styles_text',
		'sscb_plugin'
	);
	add_settings_field( 'sscb_plugin_setting_bar_bg', 'Background Colour', 'sscb_plugin_setting_bar_bg', 'sscb_plugin', 'sscb_bar_styles' );
	add_settings_field( 'sscb_plugin_setting_bar_txt', 'Text Colour', 'sscb_plugin_setting_bar_txt', 'sscb_plugin', 'sscb_bar_styles' );

	add_settings_section(
		'sscb_btn_styles',
		__('Cookie Button Styles', 'super-simple-cookie-bar'),
		'sscb_btn_styles_text',
		'sscb_plugin'
	);
	add_settings_field( 'sscb_plugin_setting_btn_bg', 'Background Colour', 'sscb_plugin_setting_btn_bg', 'sscb_plugin', 'sscb_btn_styles' );
	add_settings_field( 'sscb_plugin_setting_btn_txt', 'Text Colour', 'sscb_plugin_setting_btn_txt', 'sscb_plugin', 'sscb_btn_styles' );
	add_settings_field( 'sscb_plugin_setting_btn_border', 'Border Colour', 'sscb_plugin_setting_btn_border', 'sscb_plugin', 'sscb_btn_styles' );

	add_settings_section(
		'sscb_content',
		__('Content Settings', 'super-simple-cookie-bar'),
		'sscb_content_text',
		'sscb_plugin'
	);
	add_settings_field( 'sscb_plugin_setting_message', 'Message', 'sscb_plugin_setting_message', 'sscb_plugin', 'sscb_content' );
	add_settings_field( 'sscb_plugin_setting_dismiss', 'Dismiss/Accept Text', 'sscb_plugin_setting_dismiss', 'sscb_plugin', 'sscb_content' );
	add_settings_field( 'sscb_plugin_setting_link', 'Learn More Link Text', 'sscb_plugin_setting_link', 'sscb_plugin', 'sscb_content' );
	add_settings_field( 'sscb_plugin_setting_href', 'Learn More Link', 'sscb_plugin_setting_href', 'sscb_plugin', 'sscb_content' );
}
add_action( 'admin_init', 'sscb_register_settings' );


/**
 * Validate settings on submit
 *
 * Validation and sanitisation of various settings on submit. Also used to register WPML strings for string translation support.
 *
 * @since 1.0.0
 *
 *
 */
function sscb_plugin_options_validate( $input ) {
	$old_options = get_option( 'sscb_plugin_options' );
	$has_errors = false;

	/**
	 *
	 * General Settings Validation
	 *
	 */
	$theme_allowed = array( 'block','edgeless','classic','wire' );
	if( ! in_array( $input['theme'], $theme_allowed ) ){
		//check theme is an allowed value
		add_settings_error( 'sscb_plugin_options', 'sscb_theme_error', __( 'Theme: This is not a valid value', 'super-simple-cookie-bar' ), 'error' );

    $has_errors = true;
	}

	$position_allowed = array( 'bottom','top','top-static','bottom-left','bottom-right' );
	if( ! in_array( $input['position'], $position_allowed ) ){
		//check position is an allowed value
		add_settings_error( 'sscb_plugin_options', 'sscb_position_error', __( 'Position: This is not a valid value', 'super-simple-cookie-bar' ), 'error' );

    $has_errors = true;
	}

	/**
	 *
	 * Bar Styles Validation
	 *
	 */
	if( ( $input['bar_bg'] ) && ( ! sanitize_hex_color( $input['bar_bg'] ) ) ){
		//check bar_bg is valid hex if entered.
		add_settings_error( 'sscb_plugin_options', 'sscb_bar_bg_error', __( 'Cookie Bar - Background Colour: You must enter a valid HTML hex colour code e.g. #000000 or #000', 'super-simple-cookie-bar' ), 'error' );

    $has_errors = true;
	}
	if( ( $input['bar_txt'] ) && ( ! sanitize_hex_color( $input['bar_txt'] ) ) ){
		//check bar_txt is valid hex if entered.
		add_settings_error( 'sscb_plugin_options', 'sscb_bar_txt_error', __( 'Cookie Bar - Text Colour: You must enter a valid HTML hex colour code e.g. #ffffff or #fff', 'super-simple-cookie-bar' ), 'error' );

    $has_errors = true;
	}

	/**
	 *
	 * Button Styles Validation
	 *
	 */
	if( ( $input['btn_bg'] ) && ( ! sanitize_hex_color( $input['btn_bg'] ) ) ){
		//check btn_bg is valid hex if entered.
		add_settings_error( 'sscb_plugin_options', 'sscb_btn_bg_error', __( 'Cookie Button - Background Colour: You must enter a valid HTML hex colour code e.g. #ffffff or #fff', 'super-simple-cookie-bar' ), 'error' );

    $has_errors = true;
	}
	if( ( $input['btn_txt'] ) && ( ! sanitize_hex_color( $input['btn_txt'] ) ) ){
		//check btn_txt is valid hex if entered.
		add_settings_error( 'sscb_plugin_options', 'sscb_btn_txt_error', __( 'Cookie Button - Text Colour: You must enter a valid HTML hex colour code e.g. #00000 or #000', 'super-simple-cookie-bar' ), 'error' );

    $has_errors = true;
	}
	if( ( $input['btn_border'] ) && ( ! sanitize_hex_color( $input['btn_border'] ) ) ){
		//check btn_border is valid hex if entered.
		add_settings_error( 'sscb_plugin_options', 'sscb_btn_border_error', __( 'Cookie Button - Border Colour: You must enter a valid HTML hex colour code e.g. #00000 or #000', 'super-simple-cookie-bar' ), 'error' );

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
	do_action( 'wpml_register_single_string', 'super-simple-cookie-bar', 'Message', ( $input['message'] ?? $input['message'] ?: 'This website uses cookies to ensure you get the best experience on our website.' ) );
	do_action( 'wpml_register_single_string', 'super-simple-cookie-bar', 'Dismiss/Accept Text', ( $input['dismiss'] ?? $input['dismiss'] ?: 'Got it!' ) );
	do_action( 'wpml_register_single_string', 'super-simple-cookie-bar', 'Learn More Link Text', ( $input['link'] ?? $input['link'] ?: 'Learn More' ) );

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
function sscb_general_text() {
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
function sscb_plugin_setting_enabled() {
	$sscb_options = get_option( 'sscb_plugin_options' );

	echo "<input id='sscb_plugin_setting_enabled' name='sscb_plugin_options[enabled]' type='checkbox' value='1'" . checked( 1, $sscb_options['enabled'] ??= false, false ) .  "' />";
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
function sscb_plugin_setting_theme() {
	$sscb_options = get_option( 'sscb_plugin_options' );


	echo "<select name='sscb_plugin_options[theme]'>";
		echo "<option value='block' ".selected( $sscb_options['theme'], "block" )." >".__(' Block', 'super-simple-cookie-bar' )."</option>";
		echo "<option value='edgeless' ".selected( $sscb_options['theme'], "edgeless" )." >".__( 'Edgeless', 'super-simple-cookie-bar' )."</option>";
		echo "<option value='classic' ".selected( $sscb_options['theme'], "classic" )." >".__( 'Classic', 'super-simple-cookie-bar' )."</option>";
		echo "<option value='wire' ".selected( $sscb_options['theme'], "wire" )." >".__( 'Wire', 'ncompass-cooke-bar' )."</option>";
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
function sscb_plugin_setting_position() {
	$sscb_options = get_option( 'sscb_plugin_options' );
	echo "<select name='sscb_plugin_options[position]'>";
		echo "<option value='bottom' ".selected( $sscb_options['position'], "bottom" )." >".__( 'Banner Bottom', 'super-simple-cookie-bar' )."</option>";
		echo "<option value='top' ".selected( $sscb_options['position'], "top" )." >".__( 'Banner Top', 'super-simple-cookie-bar' )."</option>";
		echo "<option value='top-static' ".selected( $sscb_options['position'], "top-static" )." >".__( 'Banner Top (Pushdown)', 'super-simple-cookie-bar' )."</option>";
		echo "<option value='bottom-left' ".selected( $sscb_options['position'], "bottom-left" )." >".__( 'Floating Left', 'super-simple-cookie-bar' )."</option>";
		echo "<option value='bottom-right' ".selected( $sscb_options['position'], "bottom-right" )." >".__( 'Floating Right', 'super-simple-cookie-bar' )."</option>";
	echo "</select>";
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
function sscb_bar_styles_text() {

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
function sscb_plugin_setting_bar_bg() {
	$sscb_options = get_option( 'sscb_plugin_options' );
	echo "<input id='sscb_plugin_setting_bar_bg' name='sscb_plugin_options[bar_bg]' type='text' placeholder='#000000' value='" . esc_attr( $sscb_options['bar_bg'] ??= '' ) . "' />";
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
function sscb_plugin_setting_bar_txt() {
	$sscb_options = get_option( 'sscb_plugin_options' );
	echo "<input id='sscb_plugin_setting_bar_txt' name='sscb_plugin_options[bar_txt]' placeholder='#ffffff' type='text' value='" . esc_attr( $sscb_options['bar_txt'] ??= '' ) . "' />";
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
function sscb_btn_styles_text() {

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
function sscb_plugin_setting_btn_bg() {
	$sscb_options = get_option( 'sscb_plugin_options' );
	echo "<input id='sscb_plugin_setting_btn_bg' name='sscb_plugin_options[btn_bg]' placeholder='#ffffff' type='text' value='" . esc_attr( $sscb_options['btn_bg'] ??= '' ) . "' />";
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
function sscb_plugin_setting_btn_txt() {
	$sscb_options = get_option( 'sscb_plugin_options' );
	echo "<input id='sscb_plugin_setting_btn_txt' name='sscb_plugin_options[btn_txt]' placeholder='#000000' type='text' value='" . esc_attr( $sscb_options['btn_txt'] ??= '' ) . "' />";
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
function sscb_plugin_setting_btn_border() {
	$sscb_options = get_option( 'sscb_plugin_options' );
	echo "<input id='sscb_plugin_setting_btn_border' name='sscb_plugin_options[btn_border]' placeholder='#000000' type='text' value='" . esc_attr( $sscb_options['btn_border'] ??= '' ) . "' />";
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
function sscb_content_text() {

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
function sscb_plugin_setting_message() {
	$sscb_options = get_option( 'sscb_plugin_options' );
	echo "<input id='sscb_plugin_setting_message' name='sscb_plugin_options[message]' type='text' placeholder='".__( 'This website uses cookies to ensure you get the best experience on our website.', 'super-simple-cookie-bar' )."' value='" . esc_attr( $sscb_options['message'] ??= '' ) . "' style='width:100%;' />";
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
function sscb_plugin_setting_dismiss() {
	$sscb_options = get_option( 'sscb_plugin_options' );
	echo "<input id='sscb_plugin_setting_dismiss' name='sscb_plugin_options[dismiss]' type='text' placeholder='".__( 'Got it!', 'super-simple-cookie-bar' )."' value='" . esc_attr( $sscb_options['dismiss'] ??= '' ) . "' />";
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
function sscb_plugin_setting_link() {
	$sscb_options = get_option( 'sscb_plugin_options' );
	echo "<input id='sscb_plugin_setting_link' name='sscb_plugin_options[link]' type='text' placeholder='".__( 'Learn more', 'super-simple-cookie-bar' )."' value='" . esc_attr( $sscb_options['link'] ??= '' ) . "' />";
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
function sscb_plugin_setting_href() {
	$sscb_options = get_option( 'sscb_plugin_options' );
	echo "<input id='sscb_plugin_setting_href' name='sscb_plugin_options[href]' type='text' placeholder='".__( 'e.g. /privacy-policy', 'super-simple-cookie-bar' )."' value='" . esc_attr( $sscb_options['href'] ??= '' ) . "' />";
}
