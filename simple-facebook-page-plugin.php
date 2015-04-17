<?php
/*
Plugin Name:    Simple Facebook Page Widget & Shortcode
Plugin URI:     https://wordpress.org/plugins/simple-facebook-page-widget/
Description:    Shows the Facebook Page feed in a sidebar widget and/or via shortcode.
Version:        1.2.1
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
	define( 'SIMPLE_FACEBOOK_PAGE_WIDGET_VERSION', '1.2.1' );
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
 */
add_action( 'wp_enqueue_scripts', 'sfpp_enqueue_scripts' );
function sfpp_enqueue_scripts() {
	wp_enqueue_script( 'sfpp-fb-root', SIMPLE_FACEBOOK_PAGE_WIDGET_DIRECTORY . 'js/simple-facebook-page-root.js' , array( 'jquery' ) );
}

/**
 * Create the [facebook-page] shortcode.
 * Wrapped in comment @since 1.2.0
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

	$output .= '<!-- Begin Facebook Page Shortcode - https://wordpress.org/plugins/simple-facebook-twitter-widget/ -->';
	$output .= '<div class="fb-page" ';
	$output .= 'data-href="https://facebook.com/' . wp_kses_post( $facebook_page_atts['href'] ) . '" ';
	$output .= 'data-width="' . wp_kses_post( $facebook_page_atts['width'] ) . '" ';
	$output .= 'data-height="' . wp_kses_post( $facebook_page_atts['height'] ) . '" ';
	$output .= 'data-hide-cover="' . wp_kses_post( $facebook_page_atts['hide_cover'] ) . '" ';
	$output .= 'data-show-facepile="' . wp_kses_post( $facebook_page_atts['show_facepile'] ) . '" ';
	$output .= 'data-show-posts="' . wp_kses_post( $facebook_page_atts['show_posts'] ) . '">';
	$output .= '</div>';
	$output .= '<!-- End Facebook Page Shortcode -->';

	return $output;

}

/**
 * Registers the SFPP_Widget widget class.
 *
 * @since 1.0.0
 * @modified 1.2.1 Added compatibility for PHP 5.2 with create_function
 * https://wordpress.org/support/topic/plugin-activation-error-9
 */
require_once( 'includes/class-simple-facebook-page-plugin-widget.php' );
add_action( 'widgets_init', function () {
    create_function('', 'return register_widget("SFPP_Widget");');
} );