<?php
/*
Plugin Name:    Simple Facebook Page Widget & Shortcode
Plugin URI:     https://wordpress.org/plugins/simple-facebook-page-widget/
Description:    Shows the Facebook Page feed in a sidebar widget and/or via shortcode.
Version:        1.4.0
Author:         Dylan Ryan
Author URI:     https://profiles.wordpress.org/irkanu
Domain Path:    /languages
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


/**********************
 * Deny Direct Access *
 **********************/
if ( ! defined( 'WPINC' ) ) {
	die;
}


/********************
 * Plugin Constants *
 ********************/
if ( ! defined( 'SIMPLE_FACEBOOK_PAGE_WIDGET_VERSION' ) ) {
	define( 'SIMPLE_FACEBOOK_PAGE_WIDGET_VERSION', '1.3.1' );
}
if ( ! defined( 'SIMPLE_FACEBOOK_PAGE_WIDGET_PLUGIN_NAME' ) ) {
	define( 'SIMPLE_FACEBOOK_PAGE_WIDGET_PLUGIN_NAME', 'Simple Facebook Page Widget & Shortcode' );
}
if ( ! defined( 'SIMPLE_FACEBOOK_PAGE_WIDGET_DIRECTORY' ) ) {
    define( 'SIMPLE_FACEBOOK_PAGE_WIDGET_DIRECTORY', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'SIMPLE_FACEBOOK_PAGE_I18N' ) ) {
    define( 'SIMPLE_FACEBOOK_PAGE_I18N', 'simple-facebook-twitter-widget' );
}


/********************
 * Global Variables *
 ********************/
$sfpp_options = get_option( 'sfpp_settings' );


/**
 * Load the translation PO files.
 * http://codex.wordpress.org/I18n_for_WordPress_Developers
 *
 * @since 1.1.0
 */
load_plugin_textdomain( SIMPLE_FACEBOOK_PAGE_I18N, false, SIMPLE_FACEBOOK_PAGE_WIDGET_DIRECTORY . 'languages' );


/**
 * Enqueue Facebook script required for the plugin.
 *
 * @since 1.0.0
 *
 * @modified 1.4.0 Localized the script for language option
 */
add_action( 'wp_enqueue_scripts', 'sfpp_enqueue_scripts' );
function sfpp_enqueue_scripts() {

	global $sfpp_options;

	//* Prepare the javascript for manipulation.
	wp_enqueue_script( 'sfpp-fb-root', SIMPLE_FACEBOOK_PAGE_WIDGET_DIRECTORY . 'js/simple-facebook-page-root.js' , array( 'jquery' ) );

	//* Pass the language option from the database to javascript.
	wp_localize_script( 'sfpp-fb-root', 'sfpp_script_vars', array(
			'language'  =>  ( $sfpp_options['language'] )
		)
	);

}


/**
 * Create the [facebook-page] shortcode.
 *
 * @since 1.0.0
 *
 * @modified 1.2.0 Wrapped shortcode in comment for debug/tracking.
 * @modified 1.3.0 Added alignment parameter.
 *
 * @param $atts array href, width, height, hide_cover, show_facepile, show_posts, align
 *
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
		'show_posts'    => 'true',
		'align'         => 'initial',
	), $atts );

	$output .= '<!-- Begin Facebook Page Shortcode - https://wordpress.org/plugins/simple-facebook-twitter-widget/ -->';

	//* Wrapper for alignment
	$output .= '<div id="simple-facebook-widget" style="text-align:' . wp_kses_post( $facebook_page_atts['align'] ) . ';">';

	//* Main Facebook Feed
	$output .= '<div class="fb-page" ';
	$output .= 'data-href="https://facebook.com/' . wp_kses_post( $facebook_page_atts['href'] ) . '" ';
	$output .= 'data-width="' . wp_kses_post( $facebook_page_atts['width'] ) . '" ';
	$output .= 'data-height="' . wp_kses_post( $facebook_page_atts['height'] ) . '" ';
	$output .= 'data-hide-cover="' . wp_kses_post( $facebook_page_atts['hide_cover'] ) . '" ';
	$output .= 'data-show-facepile="' . wp_kses_post( $facebook_page_atts['show_facepile'] ) . '" ';
	$output .= 'data-show-posts="' . wp_kses_post( $facebook_page_atts['show_posts'] ) . '">';
	$output .= '</div>';

	$output .= '</div>';

	$output .= '<!-- End Facebook Page Shortcode -->';

	return $output;

}


/**
 * Registers the SFPP_Widget widget class.
 *
 * @since 1.0.0
 *
 * @modified 1.2.1 Added compatibility for PHP 5.2 with create_function
 * https://wordpress.org/support/topic/plugin-activation-error-9
 */
require_once( 'includes/class-simple-facebook-page-plugin-widget.php' );
add_action( 'widgets_init',
	create_function( '', 'return register_widget("SFPP_Widget");' )
);


/**
 * Registers the admin settings menu
 * https://developer.wordpress.org/plugins/settings/custom-settings-page/#creating-the-menu-item
 *
 * @since 1.4.0
 */
add_action( 'admin_menu', 'sfpp_admin_settings_menu' );
function sfpp_admin_settings_menu() {

	add_options_page(
		'Simple Facebook Page Options',
		'Simple Facebook Page Options',
		'manage_options',
		'sfpp-settings',        //slug
		'sfpp_options_page'     //callback function to display the page
	);

}

/**
 * Registers the settings, sections, and fields.
 * https://developer.wordpress.org/plugins/settings/creating-and-using-options/
 *
 * @since 1.4.0
 */
add_action( 'admin_init', 'sfpp_register_settings' );
function sfpp_register_settings() {

	register_setting(
		'sfpp_settings_group',      // settings section (group) - used on the admin page itself to setup fields
		'sfpp_settings'             // setting name - get_option() to retrieve from database - retrieve it and store it in global variable
	);

	add_settings_section(
		'sfpp_language_section',
		'Language Settings',
		'sfpp_language_section_callback',
		'sfpp-settings'
	);

	add_settings_field(
		'sfpp_settings',
		'Select a language:',
		'sfpp_language_select_callback',
		'sfpp-settings',
		'sfpp_language_section'
	);

}

/**
 * Function that echos out any content at the top of the section (between heading and fields).
 *
 * @since 1.4.0
 */
function sfpp_language_section_callback() {

}

/**
 * Function that fills the field with the desired form inputs. The function should echo its output.
 *
 * @since 1.4.0
 */
function sfpp_language_select_callback() {

	global $sfpp_options;

	?>

	<select id="sfpp_settings[language]" name="sfpp_settings[language]">
		<option value="en_EN" <?php selected( $sfpp_options['language'], 'English' ); ?>>English</option>
		<option value="de_DE" <?php selected( $sfpp_options['language'], 'German' ); ?>>German</option>
	</select>

	<?php

}


/**
 * Displays the settings page
 * https://developer.wordpress.org/plugins/settings/custom-settings-page/#creating-the-page
 *
 * @since 1.4.0
 */
function sfpp_options_page() {

	global $sfpp_options;

	ob_start();

?>

	<div class="wrap">

		<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

		<form method="post" action="options.php">

			<?php

			settings_fields( 'sfpp_settings_group' );

			do_settings_sections( 'sfpp-settings' );

			submit_button();

			echo $sfpp_options['language'];

			?>

		</form>

	</div>

<?php

	echo ob_get_clean();

}