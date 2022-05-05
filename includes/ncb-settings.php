<?php
/** Add Settings Page and Fields **/
function dbi_add_settings_page() {
    add_options_page( 'NCompass Cookie Bar', 'NCompass Cookie Bar', 'manage_options', 'ncompass-cookie-bar', 'ncb_render_plugin_settings_page' );
}
add_action( 'admin_menu', 'dbi_add_settings_page' );

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

    add_settings_section( 'ncb_bar_styles', 'Cookie Bar Styles', 'ncb_bar_styles_text', 'ncb_plugin' );
    add_settings_field( 'ncb_plugin_setting_bar_bg', 'Background Colour', 'ncb_plugin_setting_bar_bg', 'ncb_plugin', 'ncb_bar_styles' );
    add_settings_field( 'ncb_plugin_setting_bar_txt', 'Text Colour', 'ncb_plugin_setting_bar_txt', 'ncb_plugin', 'ncb_bar_styles' );

    add_settings_section( 'ncb_btn_styles', 'Cookie Button Styles', 'ncb_button_styles_text', 'ncb_plugin' );
    add_settings_field( 'ncb_plugin_setting_btn_bg', 'Background Colour', 'ncb_plugin_setting_btn_bg', 'ncb_plugin', 'ncb_btn_styles' );
    add_settings_field( 'ncb_plugin_setting_btn_txt', 'Text Colour', 'ncb_plugin_setting_btn_txt', 'ncb_plugin', 'ncb_btn_styles' );
    add_settings_field( 'ncb_plugin_setting_btn_border', 'Border Colour', 'ncb_plugin_setting_btn_border', 'ncb_plugin', 'ncb_btn_styles' );
}
add_action( 'admin_init', 'ncb_register_settings' );



function ncb_plugin_options_validate( $input ) {
    return $input;
}

function ncb_plugin_setting_enabled() {
    $options = get_option( 'ncb_plugin_options' );
    echo "<input id='ncb_plugin_setting_enabled' name='ncb_plugin_options[enabled]' type='checkbox' value='1'" . checked( 1, $options['enabled'], false ) .  "' />";
}

function ncb_bar_styles_text() {

}
function ncb_plugin_setting_bar_bg() {
    $options = get_option( 'ncb_plugin_options' );
    echo "<input id='ncb_plugin_setting_bar_bg' name='ncb_plugin_options[bar_bg]' type='text' value='" . esc_attr( $options['bar_bg'] ) . "' />";
}
function ncb_plugin_setting_bar_txt() {
    $options = get_option( 'ncb_plugin_options' );
    echo "<input id='ncb_plugin_setting_bar_txt' name='ncb_plugin_options[bar_txt]' type='text' value='" . esc_attr( $options['bar_txt'] ) . "' />";
}


function ncb_btn_styles_text() {

}
function ncb_plugin_setting_btn_bg() {
    $options = get_option( 'ncb_plugin_options' );
    echo "<input id='ncb_plugin_setting_btn_bg' name='ncb_plugin_options[btn_bg]' type='text' value='" . esc_attr( $options['btn_bg'] ) . "' />";
}
function ncb_plugin_setting_btn_txt() {
    $options = get_option( 'ncb_plugin_options' );
    echo "<input id='ncb_plugin_setting_btn_txt' name='ncb_plugin_options[btn_txt]' type='text' value='" . esc_attr( $options['btn_txt'] ) . "' />";
}
function ncb_plugin_setting_btn_border() {
    $options = get_option( 'ncb_plugin_options' );
    echo "<input id='ncb_plugin_setting_btn_border' name='ncb_plugin_options[btn_border]' type='text' value='" . esc_attr( $options['btn_border'] ) . "' />";
}
