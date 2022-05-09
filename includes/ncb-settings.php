<?php
/** Add Settings Page and Fields **/
function ncb_add_settings_page() {
    add_options_page( 'NCompass Cookie Bar', 'NCompass Cookie Bar', 'manage_options', 'ncompass-cookie-bar', 'ncb_render_plugin_settings_page' );
}
add_action( 'admin_menu', 'ncb_add_settings_page' );

function ncb_render_plugin_settings_page() {
    ?>
    <h2>Cookie Bar Settings</h2>
    <form action="options.php" method="post">
        <?php
        settings_fields( 'ncb_plugin_options' );
        do_settings_sections( 'ncb_plugin' ); ?>
        <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
    </form>
    <?php
}

function ncb_register_settings() {
    register_setting( 'ncb_plugin_options', 'ncb_plugin_options', 'ncb_plugin_options_validate' );
    add_settings_section( 'ncb_general', 'General Settings', 'ncb_general_text', 'ncb_plugin' );
    add_settings_field( 'ncb_plugin_setting_enabled', 'Enable Cookie Bar', 'ncb_plugin_setting_enabled', 'ncb_plugin', 'ncb_general');
    add_settings_field( 'ncb_plugin_setting_theme', 'Theme', 'ncb_plugin_setting_theme', 'ncb_plugin', 'ncb_general');
    add_settings_field( 'ncb_plugin_setting_position', 'Position', 'ncb_plugin_setting_position', 'ncb_plugin', 'ncb_general');

    add_settings_section( 'ncb_bar_styles', 'Cookie Bar Styles', 'ncb_bar_styles_text', 'ncb_plugin' );
    add_settings_field( 'ncb_plugin_setting_bar_bg', 'Background Colour', 'ncb_plugin_setting_bar_bg', 'ncb_plugin', 'ncb_bar_styles' );
    add_settings_field( 'ncb_plugin_setting_bar_txt', 'Text Colour', 'ncb_plugin_setting_bar_txt', 'ncb_plugin', 'ncb_bar_styles' );

    add_settings_section( 'ncb_btn_styles', 'Cookie Button Styles', 'ncb_button_styles_text', 'ncb_plugin' );
    add_settings_field( 'ncb_plugin_setting_btn_bg', 'Background Colour', 'ncb_plugin_setting_btn_bg', 'ncb_plugin', 'ncb_btn_styles' );
    add_settings_field( 'ncb_plugin_setting_btn_txt', 'Text Colour', 'ncb_plugin_setting_btn_txt', 'ncb_plugin', 'ncb_btn_styles' );
    add_settings_field( 'ncb_plugin_setting_btn_border', 'Border Colour', 'ncb_plugin_setting_btn_border', 'ncb_plugin', 'ncb_btn_styles' );

    add_settings_section( 'ncb_content', 'Content Settings', 'ncb_content_text', 'ncb_plugin' );
    add_settings_field( 'ncb_plugin_setting_message', 'Message', 'ncb_plugin_setting_message', 'ncb_plugin', 'ncb_content');
    add_settings_field( 'ncb_plugin_setting_dismiss', 'Dismiss/Accept Text', 'ncb_plugin_setting_dismiss', 'ncb_plugin', 'ncb_content');
    add_settings_field( 'ncb_plugin_setting_link', 'Learn More Link Text', 'ncb_plugin_setting_link', 'ncb_plugin', 'ncb_content');
    add_settings_field( 'ncb_plugin_setting_href', 'Learn More Link', 'ncb_plugin_setting_href', 'ncb_plugin', 'ncb_content');
}
add_action( 'admin_init', 'ncb_register_settings' );



function ncb_plugin_options_validate( $input ) {
    return $input;
}


function ncb_general_text() {

}
function ncb_plugin_setting_enabled() {
    $options = get_option( 'ncb_plugin_options' );
    echo "<input id='ncb_plugin_setting_enabled' name='ncb_plugin_options[enabled]' type='checkbox' value='1'" . checked( 1, $options['enabled'], false ) .  "' />";
}
function ncb_plugin_setting_theme() {
  $options = get_option( 'ncb_plugin_options' );
  echo "<select name='ncb_plugin_options[theme]'>";
    echo "<option value='block' ".selected($options['theme'], "block")." >Block</option>";
    echo "<option value='edgeless' ".selected($options['theme'], "edgeless")." >Edgeless</option>";
    echo "<option value='classic' ".selected($options['theme'], "classic")." >Classic</option>";
    echo "<option value='wire' ".selected($options['theme'], "wire")." >Wire</option>";
  echo "</select>";
}
function ncb_plugin_setting_position() {
  $options = get_option( 'ncb_plugin_options' );
  echo "<select name='ncb_plugin_options[position]'>";
    echo "<option value='bottom' ".selected($options['position'], "bottom")." >Banner Bottom</option>";
    echo "<option value='top' ".selected($options['position'], "top")." >Banner Top</option>";
    echo "<option value='top-static' ".selected($options['position'], "top-static")." >Banner Top (Pushdown)</option>";
    echo "<option value='bottom-left' ".selected($options['position'], "bottom-left")." >Floating Left</option>";
    echo "<option value='bottom-right' ".selected($options['position'], "bottom-right")." >Floating Right</option>";
  echo "</select>";
}

function ncb_content_text() {

}
function ncb_plugin_setting_message() {
    $options = get_option( 'ncb_plugin_options' );
    echo "<input id='ncb_plugin_setting_message' name='ncb_plugin_options[message]' type='text' placeholder='This website uses cookies to ensure you get the best experience on our website.' value='" . esc_attr( $options['message'] ) . "' style='width:100%;' />";
}
function ncb_plugin_setting_dismiss() {
    $options = get_option( 'ncb_plugin_options' );
    echo "<input id='ncb_plugin_setting_dismiss' name='ncb_plugin_options[dismiss]' type='text' placeholder='Got it!' value='" . esc_attr( $options['dismiss'] ) . "' />";
}
function ncb_plugin_setting_link() {
    $options = get_option( 'ncb_plugin_options' );
    echo "<input id='ncb_plugin_setting_link' name='ncb_plugin_options[link]' type='text' placeholder='Learn more' value='" . esc_attr( $options['link'] ) . "' />";
}
function ncb_plugin_setting_href() {
    $options = get_option( 'ncb_plugin_options' );
    echo "<input id='ncb_plugin_setting_href' name='ncb_plugin_options[href]' type='text' placeholder='e.g. /privacy-policy' value='" . esc_attr( $options['href'] ) . "' />";
}

function ncb_bar_styles_text() {

}
function ncb_plugin_setting_bar_bg() {
    $options = get_option( 'ncb_plugin_options' );
    echo "<input id='ncb_plugin_setting_bar_bg' name='ncb_plugin_options[bar_bg]' type='text' placeholder='#000' value='" . esc_attr( $options['bar_bg'] ) . "' />";
}
function ncb_plugin_setting_bar_txt() {
    $options = get_option( 'ncb_plugin_options' );
    echo "<input id='ncb_plugin_setting_bar_txt' name='ncb_plugin_options[bar_txt]' placeholder='#fff' type='text' value='" . esc_attr( $options['bar_txt'] ) . "' />";
}


function ncb_btn_styles_text() {

}
function ncb_plugin_setting_btn_bg() {
    $options = get_option( 'ncb_plugin_options' );
    echo "<input id='ncb_plugin_setting_btn_bg' name='ncb_plugin_options[btn_bg]' placeholder='#fff' type='text' value='" . esc_attr( $options['btn_bg'] ) . "' />";
}
function ncb_plugin_setting_btn_txt() {
    $options = get_option( 'ncb_plugin_options' );
    echo "<input id='ncb_plugin_setting_btn_txt' name='ncb_plugin_options[btn_txt]' placeholder='#000' type='text' value='" . esc_attr( $options['btn_txt'] ) . "' />";
}
function ncb_plugin_setting_btn_border() {
    $options = get_option( 'ncb_plugin_options' );
    echo "<input id='ncb_plugin_setting_btn_border' name='ncb_plugin_options[btn_border]' placeholder='#fff' type='text' value='" . esc_attr( $options['btn_border'] ) . "' />";
}
