<?php
/*
Plugin Name:    Simple Facebook Page Widget
Plugin URI:     https://wordpress.org/plugins/simple-facebook-page-widget/
Description:    Shows the Facebook Page feed in a sidebar widget and/or via shortcode.
Version:        1.1.0
Author:         Dylan Ryan
Author URI:     https://profiles.wordpress.org/irkanu
Text Domain:    simple-facebook-twitter-widget
GitHub URI:     https://github.com/irkanu/simple-facebook-page-widget
GitHub Branch:  master
License:        GPL v3

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * Deny Direct Access
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Plugin Constants
 */
if ( ! defined( 'SIMPLE_FACEBOOK_PAGE_WIDGET_VERSION' ) ) {
	define( 'SIMPLE_FACEBOOK_PAGE_WIDGET_VERSION', '1.1.0' );
}
if ( ! defined( 'SIMPLE_FACEBOOK_PAGE_WIDGET_DIRECTORY' ) ) {
    define( 'SIMPLE_FACEBOOK_PAGE_WIDGET_DIRECTORY', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'SIMPLE_FACEBOOK_PAGE_I18N' ) ) {
    define( 'SIMPLE_FACEBOOK_PAGE_I18N', 'simple-facebook-twitter-widget' );
}

/**
 * Enqueue Facebook script required for the plugin.
 *
 * @since 1.0.0
 */
add_action( 'wp_enqueue_scripts', 'sfpp_enqueue_scripts' );
function sfpp_enqueue_scripts() {
	wp_enqueue_script( 'sfpp-fb-root', SIMPLE_FACEBOOK_PAGE_WIDGET_DIRECTORY . 'js/simple-facebook-page-root.js' , array( 'jquery' ) );
}

/**
 * Create the [facebook-page] shortcode.
 *
 * @since 1.0.0
 * @param $atts array href, width, height, hide_cover, show_facepile, show_posts
 * @return string Outputs the Facebook Page feed via shortcode.
 */
add_shortcode( 'facebook-page', 'sfpp_shortcode' );
function sfpp_shortcode( $atts ) {

	$output = '';

	$facebook_page_atts = shortcode_atts( array(
		'href'          => '',
		'width'         => '340',
		'height'        => '500',
		'hide_cover'    => 'false',
		'show_facepile' => 'false',
		'show_posts'    => 'true'
	), $atts );

	$output .= '<div class="fb-page" ';
	$output .= 'data-href="https://facebook.com/' . wp_kses_post( $facebook_page_atts['href'] ) . '" ';
	$output .= 'data-width="' . wp_kses_post( $facebook_page_atts['width'] ) . '" ';
	$output .= 'data-height="' . wp_kses_post( $facebook_page_atts['height'] ) . '" ';
	$output .= 'data-hide-cover="' . wp_kses_post( $facebook_page_atts['hide_cover'] ) . '" ';
	$output .= 'data-show-facepile="' . wp_kses_post( $facebook_page_atts['show_facepile'] ) . '" ';
	$output .= 'data-show-posts="' . wp_kses_post( $facebook_page_atts['show_posts'] ) . '">';
	$output .= '</div>';

	return $output;

}

/**
 * Registers the SFPP_Widget widget class.
 *
 * @since 1.0.0
 */
require_once ( SIMPLE_FACEBOOK_PAGE_WIDGET_DIRECTORY . 'includes/class-simple-facebook-page-plugin-widget.php' );
add_action( 'widgets_init', function () {
	register_widget( 'SFPP_Widget' );
} );


/**
 * Load the translation PO files.
 * http://codex.wordpress.org/I18n_for_WordPress_Developers
 *
 * @since 1.1.0
 */
load_plugin_textdomain( SIMPLE_FACEBOOK_PAGE_I18N, false, SIMPLE_FACEBOOK_PAGE_WIDGET_DIRECTORY . 'languages' );


/**
 * Register the Settings Page & Settings
 *
 * TODO: Add this back after custom App ID's are ready.
 */
/*
function sfpp_add_plugin_options_page(){
	add_options_page('Simple Facebook Page Settings', 'Simple Facebook Page Settings', 'manage_options', 'simple-facebook-page-settings', 'sfpp_create_admin_page');
}
function sfpp_register_options() {
	register_setting('sfpp_options', 'sfpp_settings', 'sfpp_validation');
}
if( is_admin() ) {
	add_action('admin_menu', 'sfpp_add_plugin_options_page');
	add_action('admin_init', 'sfpp_register_options');
}
*/

/**
 * Validate Settings input
 *
 * TODO: Add this back after custom App ID's are ready.
 *
 * @param $input
 *
 * @return
 */
/*
function sfpp_validation( $input ){
	if ( ! empty( $input ) ) {
		$input['sfpp_app_id'] = wp_kses_post( $input['sfpp_app_id'] );
	}
	return $input;
}
*/

/**
 * Register activation hook to populate settings
 *
 * TODO: Add this back after custom App ID's are ready.
 */
/*
register_activation_hook(__FILE__, 'sfpp_activation');
function sfpp_activation() {
	$sfpp_options = get_option('sfpp_settings');
	update_option('sfpp_settings', $sfpp_options);
}
*/

/**
 * Build the Settings Page
 *
 * TODO: Allow users to add their own App ID's.
 * TODO: Nonce it up.
 * if ( function_exists('wp_nonce_field') )
 * wp_nonce_field('facebook-page-settings-update_general_options_' . $sfpp_options);
 */
/*
function sfpp_create_admin_page(){

	function sfpp_check_admin_referer() {
		check_admin_referer('facebook-page-settings-update_general_options_' . $sfpp_options);
	}

	ob_start(); ?>

	<div class="wrap">
		<h2>Simple Facebook Page Settings</h2>

		<form action="options.php" method="post">

			<?php settings_fields('sfpp_options'); ?>
			<?php $sfpp_options = get_option('sfpp_settings', SIMPLE_FACEBOOK_PAGE_I18N); ?>
			<?php update_option('sfpp_settings', $sfpp_options); ?>

			<table class="form-table">
				<tr valign="top"><th scope="row"><label class="description" for="sfpp_settings[sfpp_app_id]"><?php _e('Enter App ID:', SIMPLE_FACEBOOK_PAGE_I18N); ?></label></th></tr>
				<tr valign="top"><th scope="row"><input type="text" id="sfpp_settings[sfpp_app_id]" name="sfpp_settings[sfpp_app_id]" value="<?php echo $sfpp_options['sfpp_app_id']; ?>"/></th></tr>
				<tr valign="top"><th scope="row"><input type="submit" class="button-primary" value="<?php _e('Save Settings', SIMPLE_FACEBOOK_PAGE_I18N); ?>"/></th></tr>

				<?php

				?>
			</table>
	</form>

	</div>
	<?php
	echo ob_get_clean();
}
*/
/**
 * Quick navigation link to settings page from plugin list.
 *
 * TODO: Add this back after custom App ID's are ready.
 */
/*
add_filter( 'plugin_action_links', 'sfpp_settings_link', 2, 2 );
function sfpp_settings_link( $actions, $file ) {

	if ( false !== strpos( $file, 'simple-facebook-page-plugin' ) ) {
		$actions['settings'] = '<a href="options-general.php?page=simple-facebook-page-settings">Settings</a>';
	}

	return $actions;

}
*/