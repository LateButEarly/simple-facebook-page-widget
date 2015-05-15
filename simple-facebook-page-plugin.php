<?php
/**
 * Plugin Name:    Simple Facebook Page Plugin
 * Plugin URI:     https://wordpress.org/plugins/simple-facebook-twitter-widget/
 * Description:    Shows the Facebook Page feed in a sidebar widget and/or via shortcode.
 * Version:        1.4.4
 * Author:         Dylan Ryan
 * Author URI:     https://profiles.wordpress.org/irkanu
 * Domain Path:    /languages
 * Text Domain:    simple-facebook-widget
 * GitHub URI:     https://github.com/irkanu/simple-facebook-page-widget
 * GitHub Branch:  master
 * License:        GPL v3
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     Simple_Facebook
 * @subpackage  Simple_Facebook_Page_Plugin
 * @author      Dylan Ryan
 * @version     1.4.4
 */


/**
 * Deny direct access.
 *
 * Do not allow anyone to access the plugin's directory - no need for an empty index.php.
 *
 * @since 1.0.0
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Define plugin version constants.
 *
 * @since 1.0.0
 *
 * @modified 1.4.2 Organized definitions.
 */
define( 'SIMPLE_FACEBOOK_PAGE_VERSION', '1.4.4' );
if ( ! defined( 'SIMPLE_FACEBOOK_PAGE_LAST_VERSION' ) ) {
	define( 'SIMPLE_FACEBOOK_PAGE_LAST_VERSION', '1.4.3' );
}


/**
 * Define plugin directory constants.
 *
 * @since 1.0.0
 *
 * @modified 1.4.2 Organized definitions.
 */
define( 'SIMPLE_FACEBOOK_PAGE_FILE',    __FILE__ );
define( 'SIMPLE_FACEBOOK_PAGE_DIR',     plugin_dir_url( SIMPLE_FACEBOOK_PAGE_FILE ) );
define( 'SIMPLE_FACEBOOK_PAGE_LIB',     SIMPLE_FACEBOOK_PAGE_DIR . 'lib/' );


/**
 * Define plugin language constants.
 *
 * @since 1.1.0
 *
 * @modified 1.4.2 Organized definitions.
 */
define( 'SIMPLE_FACEBOOK_PAGE_I18N', 'simple-facebook-widget' );


/**
 * Define plugin key constants.
 *
 * Keys are used in add_option & add_site_option.
 *
 * @since 1.4.2
 */
define( 'SIMPLE_FACEBOOK_PAGE_KEY',             'simple-facebook-page-plugin' );
define( 'SIMPLE_FACEBOOK_PAGE_NOTICE_KEY',      'sfpp-hide-notice' );
define( 'SIMPLE_FACEBOOK_PAGE_INSTALL_DATE',    'sfpp-install-date' );


/**
 * Define global variables.
 *
 * @since 1.4.0
 */
$sfpp_options = get_option( 'sfpp_settings' );


/**
 * Tell WordPress what to do when this plugin is activated.
 *
 * Sets the current version into the options table.
 * http://www.smashingmagazine.com/2011/03/08/ten-things-every-wordpress-plugin-developer-should-know/
 *
 * @since 1.4.2
 */
register_activation_hook( __FILE__, 'sfpp_activation' );
function sfpp_activation() {

	//* Last constants
	define( 'SIMPLE_FACEBOOK_PAGE_WIDGET_PLUGIN_NAME',  'Simple Facebook Page Widget & Shortcode' );

	add_option( SIMPLE_FACEBOOK_PAGE_KEY, SIMPLE_FACEBOOK_PAGE_VERSION );
}


/**
 * Load the translation PO files.
 * http://codex.wordpress.org/I18n_for_WordPress_Developers
 *
 * @since 1.1.0
 *
 * @modified 1.4.2 Wrapped in function and hooked into init.
 */
add_action( 'init', 'sfpp_textdomain' );
function sfpp_textdomain() {

	load_plugin_textdomain( SIMPLE_FACEBOOK_PAGE_I18N, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}


/**
 * Delete options on uninstall.
 *
 * This function makes sure we clean up after ourselves.
 *
 * @since 1.4.2
 */
register_uninstall_hook( SIMPLE_FACEBOOK_PAGE_FILE, 'sfpp_uninstall' );
function sfpp_uninstall() {

	delete_option( SIMPLE_FACEBOOK_PAGE_KEY ); // remove footprint

	delete_site_option( SIMPLE_FACEBOOK_PAGE_INSTALL_DATE ); // remove install date
}

/**
 * Enqueue Facebook script required for the plugin.
 *
 * @since 1.0.0
 *
 * @modified 1.4.0 Localized the script for language option.
 */
add_action( 'wp_enqueue_scripts', 'sfpp_enqueue_scripts' );
function sfpp_enqueue_scripts() {

	global $sfpp_options;

	//* Prepare the javascript for manipulation.
	wp_enqueue_script( 'sfpp-fb-root', SIMPLE_FACEBOOK_PAGE_DIR . 'js/simple-facebook-page-root.js' , array( 'jquery' ) );

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
 * @modified 1.4.2 Added version to debug comment.
 *
 * @param   $atts   array   href, width, height, hide_cover, show_facepile, show_posts, align
 *
 * @return  string  Outputs the Facebook Page feed via shortcode.
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

	$output .= '<!-- This Facebook Page Feed was generated with Simple Facebook Page Widget & Shortcode plugin v' . SIMPLE_FACEBOOK_PAGE_VERSION . ' - https://wordpress.org/plugins/simple-facebook-twitter-widget/ -->';

	//* Wrapper for alignment
	$output .= '<div id="simple-facebook-widget" style="text-align:' . esc_attr( $facebook_page_atts['align'] ) . ';">';

	//* Main Facebook Feed
	$output .= '<div class="fb-page" ';
	$output .= 'data-href="https://facebook.com/' . esc_attr( $facebook_page_atts['href'] ) . '" ';
	$output .= 'data-width="' . esc_attr( $facebook_page_atts['width'] ) . '" ';
	$output .= 'data-height="' . esc_attr( $facebook_page_atts['height'] ) . '" ';
	$output .= 'data-hide-cover="' . esc_attr( $facebook_page_atts['hide_cover'] ) . '" ';
	$output .= 'data-show-facepile="' . esc_attr( $facebook_page_atts['show_facepile'] ) . '" ';
	$output .= 'data-show-posts="' . esc_attr( $facebook_page_atts['show_posts'] ) . '">';
	$output .= '</div>';

	$output .= '</div>';

	$output .= '<!-- End Simple Facebook Page Plugin (Shortcode) -->';

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
add_action( 'widgets_init',

	create_function( '', 'return register_widget("Simple_Facebook_Page_Feed_Widget");' )
);


/**
 * Registers the admin settings menu.
 * https://developer.wordpress.org/plugins/settings/custom-settings-page/#creating-the-menu-item
 *
 * Only loads libraries required on the settings page.
 * http://codex.wordpress.org/Function_Reference/wp_enqueue_script#Load_scripts_only_on_plugin_pages
 *
 * @since 1.4.0
 */
add_action( 'admin_menu', 'sfpp_admin_settings_menu' );
function sfpp_admin_settings_menu() {

	$page_title = 'Simple Facebook Settings';
	$menu_title = 'Simple Facebook Options';
	$capability = 'manage_options';
	$menu_slug  = 'sfpp-settings';
	$function   = 'sfpp_options_page';

	$admin_settings_page = add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function );


	/**
	 * Only loads libraries required on the settings page.
	 * http://codex.wordpress.org/Function_Reference/wp_enqueue_script#Load_scripts_only_on_plugin_pages
	 *
	 * @since 1.4.2
	 */
	add_action( 'admin_print_scripts-' . $admin_settings_page, 'sfpp_admin_enqueue_scripts_chosen' );
}


/**
 * Enqueue Chosen scripts and styles for easier language selection.
 * https://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
 *
 * http://harvesthq.github.io/chosen/
 *
 * @since 1.4.0
 */
function sfpp_admin_enqueue_scripts_chosen() {

	//* Chosen script
	wp_enqueue_script( 'chosen-js',     SIMPLE_FACEBOOK_PAGE_LIB .  'chosen/chosen.jquery.min.js', array( 'jquery' ) );

	//* Chosen stylesheet
	wp_enqueue_style( 'chosen-style',   SIMPLE_FACEBOOK_PAGE_LIB .  'chosen/chosen.min.css' );

	//* Custom admin javascript
	wp_enqueue_script( 'admin-js',      SIMPLE_FACEBOOK_PAGE_DIR .  'js/admin.js', array( 'jquery' ) );

	//* Custom admin stylesheet
	wp_enqueue_style( 'admin-css',      SIMPLE_FACEBOOK_PAGE_DIR .  'css/admin.css' );
}


/**
 * Creates a quick link to the settings page.
 *
 * @since 1.4.2
 *
 * @param   $actions
 * @param   $plugin_file
 * @return  string      Outputs a settings link to the settings page.
 */
add_filter( 'plugin_action_links_' . plugin_basename( SIMPLE_FACEBOOK_PAGE_FILE ), 'sfpp_quick_settings_link' );
function sfpp_quick_settings_link( $actions ) {

	array_unshift( $actions, sprintf( '<a href="%s">%s</a>', admin_url( 'options-general.php?page=sfpp-settings' ), __( 'Settings' ) ) );

	return $actions;
}


/**
 * Registers the settings, sections, and fields.
 * https://developer.wordpress.org/plugins/settings/creating-and-using-options/
 *
 * @since 1.4.0
 */
add_action( 'admin_init', 'sfpp_register_settings' );
function sfpp_register_settings() {

	$settings = 'sfpp_settings';
	$settings_page = 'sfpp-settings';
	$language_section = 'sfpp_language_section';

	register_setting(
		'sfpp_settings_group',      // settings section (group) - used on the admin page itself to setup fields
		$settings                   // setting name - get_option() to retrieve from database - retrieve it and store it in global variable
	);

	add_settings_section(
		$language_section,                  // setup language section
		'Language Settings',                // title of section
		'sfpp_language_section_callback',   // display after the title & before the settings
		$settings_page                      // setting page
	);

	add_settings_field(
		$settings,                          // setting name
		'Select a language:',               // text before the display
		'sfpp_language_select_callback',    // displays the setting
		$settings_page,                     // setting page
		$language_section                   // setting section
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
 *
 * @modified 1.4.2 Set default language to English US.
 */
function sfpp_language_select_callback() {

	global $sfpp_options;   // get_option( 'sfpp_settings' );

	$sfpp_options['language'] = isset( $sfpp_options['language'] ) && ! empty( $sfpp_options['language'] ) ? $sfpp_options['language'] : 'en_US';

	?>

	<select id="sfpp_settings[language]" class="chosen-select" name="sfpp_settings[language]" title="<?php esc_attr__( 'Select language', SIMPLE_FACEBOOK_PAGE_I18N ) ?>">
		<option value="af_ZA" <?php selected( $sfpp_options['language'], 'af_ZA' ); ?>>Afrikaans</option>
		<option value="ak_GH" <?php selected( $sfpp_options['language'], 'ak_GH' ); ?>>Akan</option>
		<option value="am_ET" <?php selected( $sfpp_options['language'], 'am_ET' ); ?>>Amharic</option>
		<option value="ar_AR" <?php selected( $sfpp_options['language'], 'ar_AR' ); ?>>Arabic</option>
		<option value="as_IN" <?php selected( $sfpp_options['language'], 'as_IN' ); ?>>Assamese</option>
		<option value="ay_BO" <?php selected( $sfpp_options['language'], 'ay_BO' ); ?>>Aymara</option>
		<option value="az_AZ" <?php selected( $sfpp_options['language'], 'az_AZ' ); ?>>Azerbaijani</option>
		<option value="be_BY" <?php selected( $sfpp_options['language'], 'be_BY' ); ?>>Belarusian</option>
		<option value="bg_BG" <?php selected( $sfpp_options['language'], 'bg_BG' ); ?>>Bulgarian</option>
		<option value="bn_IN" <?php selected( $sfpp_options['language'], 'bn_IN' ); ?>>Bengali</option>
		<option value="br_FR" <?php selected( $sfpp_options['language'], 'br_FR' ); ?>>Breton</option>
		<option value="bs_BA" <?php selected( $sfpp_options['language'], 'bs_BA' ); ?>>Bosnian</option>
		<option value="ca_ES" <?php selected( $sfpp_options['language'], 'ca_ES' ); ?>>Catalan</option>
		<option value="cb_IQ" <?php selected( $sfpp_options['language'], 'cb_IQ' ); ?>>Sorani Kurdish</option>
		<option value="ck_US" <?php selected( $sfpp_options['language'], 'ck_US' ); ?>>Cherokee</option>
		<option value="co_FR" <?php selected( $sfpp_options['language'], 'co_FR' ); ?>>Corsican</option>
		<option value="cs_CZ" <?php selected( $sfpp_options['language'], 'cs_CZ' ); ?>>Czech</option>
		<option value="cx_PH" <?php selected( $sfpp_options['language'], 'cx_PH' ); ?>>Cebuano</option>
		<option value="cy_GB" <?php selected( $sfpp_options['language'], 'cy_GB' ); ?>>Welsh</option>
		<option value="da_DK" <?php selected( $sfpp_options['language'], 'da_DK' ); ?>>Danish</option>
		<option value="de_DE" <?php selected( $sfpp_options['language'], 'de_DE' ); ?>>German</option>
		<option value="el_GR" <?php selected( $sfpp_options['language'], 'el_GR' ); ?>>Greek</option>
		<option value="en_GB" <?php selected( $sfpp_options['language'], 'en_GB' ); ?>>English (UK)</option>
		<option value="en_IN" <?php selected( $sfpp_options['language'], 'en_IN' ); ?>>English (India)</option>
		<option value="en_PI" <?php selected( $sfpp_options['language'], 'en_PI' ); ?>>English (Pirate)</option>
		<option value="en_UD" <?php selected( $sfpp_options['language'], 'en_UD' ); ?>>English (Upside Down)</option>
		<option value="en_US" <?php selected( $sfpp_options['language'], 'en_US' ); ?>>English (US)</option>
		<option value="eo_EO" <?php selected( $sfpp_options['language'], 'eo_EO' ); ?>>Esperanto</option>
		<option value="es_CO" <?php selected( $sfpp_options['language'], 'es_CO' ); ?>>Spanish (Colombia)</option>
		<option value="es_ES" <?php selected( $sfpp_options['language'], 'es_ES' ); ?>>Spanish (Spain)</option>
		<option value="es_LA" <?php selected( $sfpp_options['language'], 'es_LA' ); ?>>Spanish</option>
		<option value="et_EE" <?php selected( $sfpp_options['language'], 'et_EE' ); ?>>Estonian</option>
		<option value="eu_ES" <?php selected( $sfpp_options['language'], 'eu_ES' ); ?>>Basque</option>
		<option value="fa_IR" <?php selected( $sfpp_options['language'], 'fa_IR' ); ?>>Persian</option>
		<option value="fb_LT" <?php selected( $sfpp_options['language'], 'fb_LT' ); ?>>Leet Speak</option>
		<option value="ff_NG" <?php selected( $sfpp_options['language'], 'ff_NG' ); ?>>Fulah</option>
		<option value="fi_FI" <?php selected( $sfpp_options['language'], 'fi_FI' ); ?>>Finnish</option>
		<option value="fo_FO" <?php selected( $sfpp_options['language'], 'fo_FO' ); ?>>Faroese</option>
		<option value="fr_CA" <?php selected( $sfpp_options['language'], 'fr_CA' ); ?>>French (Canada)</option>
		<option value="fr_FR" <?php selected( $sfpp_options['language'], 'fr_FR' ); ?>>French (France)</option>
		<option value="fy_NL" <?php selected( $sfpp_options['language'], 'fy_NL' ); ?>>Frisian</option>
		<option value="ga_IE" <?php selected( $sfpp_options['language'], 'ga_IE' ); ?>>Irish</option>
		<option value="gl_ES" <?php selected( $sfpp_options['language'], 'gl_ES' ); ?>>Galician</option>
		<option value="gn_PY" <?php selected( $sfpp_options['language'], 'gn_PY' ); ?>>Guarani</option>
		<option value="gu_IN" <?php selected( $sfpp_options['language'], 'gu_IN' ); ?>>Gujarati</option>
		<option value="gx_GR" <?php selected( $sfpp_options['language'], 'gx_GR' ); ?>>Classical Greek</option>
		<option value="ha_NG" <?php selected( $sfpp_options['language'], 'ha_NG' ); ?>>Hausa</option>
		<option value="he_IL" <?php selected( $sfpp_options['language'], 'he_IL' ); ?>>Hebrew</option>
		<option value="hi_IN" <?php selected( $sfpp_options['language'], 'hi_IN' ); ?>>Hindi</option>
		<option value="hr_HR" <?php selected( $sfpp_options['language'], 'hr_HR' ); ?>>Croatian</option>
		<option value="hu_HU" <?php selected( $sfpp_options['language'], 'hu_HU' ); ?>>Hungarian</option>
		<option value="hy_AM" <?php selected( $sfpp_options['language'], 'hy_AM' ); ?>>Armenian</option>
		<option value="id_ID" <?php selected( $sfpp_options['language'], 'id_ID' ); ?>>Indonesian</option>
		<option value="ig_NG" <?php selected( $sfpp_options['language'], 'ig_NG' ); ?>>Igbo</option>
		<option value="is_IS" <?php selected( $sfpp_options['language'], 'is_IS' ); ?>>Icelandic</option>
		<option value="it_IT" <?php selected( $sfpp_options['language'], 'it_IT' ); ?>>Italian</option>
		<option value="ja_JP" <?php selected( $sfpp_options['language'], 'ja_JP' ); ?>>Japanese</option>
		<option value="ja_KS" <?php selected( $sfpp_options['language'], 'ja_KS' ); ?>>Japanese (Kansai)</option>
		<option value="jv_ID" <?php selected( $sfpp_options['language'], 'jv_ID' ); ?>>Javanese</option>
		<option value="ka_GE" <?php selected( $sfpp_options['language'], 'ka_GE' ); ?>>Georgian</option>
		<option value="kk_KZ" <?php selected( $sfpp_options['language'], 'kk_KZ' ); ?>>Kazakh</option>
		<option value="km_KH" <?php selected( $sfpp_options['language'], 'km_KH' ); ?>>Khmer</option>
		<option value="kn_IN" <?php selected( $sfpp_options['language'], 'kn_IN' ); ?>>Kannada</option>
		<option value="ko_KR" <?php selected( $sfpp_options['language'], 'ko_KR' ); ?>>Korean</option>
		<option value="ku_TR" <?php selected( $sfpp_options['language'], 'ku_TR' ); ?>>Kurdish (Kurmanji)</option>
		<option value="la_VA" <?php selected( $sfpp_options['language'], 'la_VA' ); ?>>Latin</option>
		<option value="lg_UG" <?php selected( $sfpp_options['language'], 'lg_UG' ); ?>>Ganda</option>
		<option value="li_NL" <?php selected( $sfpp_options['language'], 'li_NL' ); ?>>Limburgish</option>
		<option value="ln_CD" <?php selected( $sfpp_options['language'], 'ln_CD' ); ?>>Lingala</option>
		<option value="lo_LA" <?php selected( $sfpp_options['language'], 'lo_LA' ); ?>>Lao</option>
		<option value="lt_LT" <?php selected( $sfpp_options['language'], 'lt_LT' ); ?>>Lithuanian</option>
		<option value="lv_LV" <?php selected( $sfpp_options['language'], 'lv_LV' ); ?>>Latvian</option>
		<option value="mg_MG" <?php selected( $sfpp_options['language'], 'mg_MG' ); ?>>Malagasy</option>
		<option value="mk_MK" <?php selected( $sfpp_options['language'], 'mk_MK' ); ?>>Macedonian</option>
		<option value="ml_IN" <?php selected( $sfpp_options['language'], 'ml_IN' ); ?>>Malayalam</option>
		<option value="mn_MN" <?php selected( $sfpp_options['language'], 'mn_MN' ); ?>>Mongolian</option>
		<option value="mr_IN" <?php selected( $sfpp_options['language'], 'mr_IN' ); ?>>Marathi</option>
		<option value="ms_MY" <?php selected( $sfpp_options['language'], 'ms_MY' ); ?>>Malay</option>
		<option value="mt_MT" <?php selected( $sfpp_options['language'], 'mt_MT' ); ?>>Maltese</option>
		<option value="my_MM" <?php selected( $sfpp_options['language'], 'my_MM' ); ?>>Burmese</option>
		<option value="nb_NO" <?php selected( $sfpp_options['language'], 'nb_NO' ); ?>>Norwegian (bokmal)</option>
		<option value="nd_ZW" <?php selected( $sfpp_options['language'], 'nd_ZW' ); ?>>Ndebele</option>
		<option value="ne_NP" <?php selected( $sfpp_options['language'], 'ne_NP' ); ?>>Nepali</option>
		<option value="nl_BE" <?php selected( $sfpp_options['language'], 'nl_BE' ); ?>>Dutch (België)</option>
		<option value="nl_NL" <?php selected( $sfpp_options['language'], 'nl_NL' ); ?>>Dutch</option>
		<option value="nn_NO" <?php selected( $sfpp_options['language'], 'nn_NO' ); ?>>Norwegian (nynorsk)</option>
		<option value="ny_MW" <?php selected( $sfpp_options['language'], 'ny_MW' ); ?>>Chewa</option>
		<option value="or_IN" <?php selected( $sfpp_options['language'], 'or_IN' ); ?>>Oriya</option>
		<option value="pa_IN" <?php selected( $sfpp_options['language'], 'pa_IN' ); ?>>Punjabi</option>
		<option value="pl_PL" <?php selected( $sfpp_options['language'], 'pl_PL' ); ?>>Polish</option>
		<option value="ps_AF" <?php selected( $sfpp_options['language'], 'ps_AF' ); ?>>Pashto</option>
		<option value="pt_BR" <?php selected( $sfpp_options['language'], 'pt_BR' ); ?>>Portuguese (Brazil)</option>
		<option value="pt_PT" <?php selected( $sfpp_options['language'], 'pt_PT' ); ?>>Portuguese (Portugal)</option>
		<option value="qu_PE" <?php selected( $sfpp_options['language'], 'qu_PE' ); ?>>Quechua</option>
		<option value="rm_CH" <?php selected( $sfpp_options['language'], 'rm_CH' ); ?>>Romansh</option>
		<option value="ro_RO" <?php selected( $sfpp_options['language'], 'ro_RO' ); ?>>Romanian</option>
		<option value="ru_RU" <?php selected( $sfpp_options['language'], 'ru_RU' ); ?>>Russian</option>
		<option value="rw_RW" <?php selected( $sfpp_options['language'], 'rw_RW' ); ?>>Kinyarwanda</option>
		<option value="sa_IN" <?php selected( $sfpp_options['language'], 'sa_IN' ); ?>>Sanskrit</option>
		<option value="sc_IT" <?php selected( $sfpp_options['language'], 'sc_IT' ); ?>>Sardinian</option>
		<option value="se_NO" <?php selected( $sfpp_options['language'], 'se_NO' ); ?>>Northern Sámi</option>
		<option value="si_LK" <?php selected( $sfpp_options['language'], 'si_LK' ); ?>>Sinhala</option>
		<option value="sk_SK" <?php selected( $sfpp_options['language'], 'sk_SK' ); ?>>Slovak</option>
		<option value="sl_SI" <?php selected( $sfpp_options['language'], 'sl_SI' ); ?>>Slovenian</option>
		<option value="sn_ZW" <?php selected( $sfpp_options['language'], 'sn_ZW' ); ?>>Shona</option>
		<option value="so_SO" <?php selected( $sfpp_options['language'], 'so_SO' ); ?>>Somali</option>
		<option value="sq_AL" <?php selected( $sfpp_options['language'], 'sq_AL' ); ?>>Albanian</option>
		<option value="sr_RS" <?php selected( $sfpp_options['language'], 'sr_RS' ); ?>>Serbian</option>
		<option value="sv_SE" <?php selected( $sfpp_options['language'], 'sv_SE' ); ?>>Swedish</option>
		<option value="sw_KE" <?php selected( $sfpp_options['language'], 'sw_KE' ); ?>>Swahili</option>
		<option value="sy_SY" <?php selected( $sfpp_options['language'], 'sy_SY' ); ?>>Syriac</option>
		<option value="sz_PL" <?php selected( $sfpp_options['language'], 'sz_PL' ); ?>>Silesian</option>
		<option value="ta_IN" <?php selected( $sfpp_options['language'], 'ta_IN' ); ?>>Tamil</option>
		<option value="te_IN" <?php selected( $sfpp_options['language'], 'te_IN' ); ?>>Telugu</option>
		<option value="tg_TJ" <?php selected( $sfpp_options['language'], 'tg_TJ' ); ?>>Tajik</option>
		<option value="th_TH" <?php selected( $sfpp_options['language'], 'th_TH' ); ?>>Thai</option>
		<option value="tk_TM" <?php selected( $sfpp_options['language'], 'tk_TM' ); ?>>Turkmen</option>
		<option value="tl_PH" <?php selected( $sfpp_options['language'], 'tl_PH' ); ?>>Filipino</option>
		<option value="tl_ST" <?php selected( $sfpp_options['language'], 'tl_ST' ); ?>>Klingon</option>
		<option value="tr_TR" <?php selected( $sfpp_options['language'], 'tr_TR' ); ?>>Turkish</option>
		<option value="tt_RU" <?php selected( $sfpp_options['language'], 'tt_RU' ); ?>>Tatar</option>
		<option value="tz_MA" <?php selected( $sfpp_options['language'], 'tz_MA' ); ?>>Tamazight</option>
		<option value="uk_UA" <?php selected( $sfpp_options['language'], 'uk_UA' ); ?>>Ukrainian</option>
		<option value="ur_PK" <?php selected( $sfpp_options['language'], 'ur_PK' ); ?>>Urdu</option>
		<option value="uz_UZ" <?php selected( $sfpp_options['language'], 'uz_UZ' ); ?>>Uzbek</option>
		<option value="vi_VN" <?php selected( $sfpp_options['language'], 'vi_VN' ); ?>>Vietnamese</option>
		<option value="wo_SN" <?php selected( $sfpp_options['language'], 'wo_SN' ); ?>>Wolof</option>
		<option value="xh_ZA" <?php selected( $sfpp_options['language'], 'xh_ZA' ); ?>>Xhosa</option>
		<option value="yi_DE" <?php selected( $sfpp_options['language'], 'yi_DE' ); ?>>Yiddish</option>
		<option value="yo_NG" <?php selected( $sfpp_options['language'], 'yo_NG' ); ?>>Yoruba</option>
		<option value="zh_CN" <?php selected( $sfpp_options['language'], 'zh_CN' ); ?>>Simplified Chinese (China)</option>
		<option value="zh_HK" <?php selected( $sfpp_options['language'], 'zh_HK' ); ?>>Traditional Chinese (Hong Kong)</option>
		<option value="zh_TW" <?php selected( $sfpp_options['language'], 'zh_TW' ); ?>>Traditional Chinese (Taiwan)</option>
		<option value="zu_ZA" <?php selected( $sfpp_options['language'], 'zu_ZA' ); ?>>Zulu</option>
		<option value="zz_TR" <?php selected( $sfpp_options['language'], 'zz_TR' ); ?>>Zazaki</option>
	</select>

<?php
}


/**
 * Displays the settings page
 * https://developer.wordpress.org/plugins/settings/custom-settings-page/#creating-the-page
 *
 * @since 1.4.0
 *
 * @modified 1.4.2 Check if current user can manage_options.
 */
function sfpp_options_page() {

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'You do not have sufficient permissions to access this page.' );
	}

	ob_start();

	?>

	<div class="wrap">

		<h2><?php echo esc_html( get_admin_page_title() ); ?> &mdash; <small>v<?php echo SIMPLE_FACEBOOK_PAGE_VERSION ?></small></h2>

		<form id="main" name="sfpp-form" method="post" action="options.php" enctype="multipart/form-data">

			<h2 class="nav-tab-wrapper hide-if-no-js">
				<a href="#tab_basic" class="nav-tab"><?php _e( 'Basic', SIMPLE_FACEBOOK_PAGE_I18N ); ?></a>
				<!-- <a href="#tab_extras" class="nav-tab"><?php //_e( 'Extras', SIMPLE_FACEBOOK_PAGE_I18N ); ?></a> -->
			</h2>

			<div id="sfpptabs">

				<?php settings_fields( 'sfpp_settings_group' );   // settings group name. This should match the group name used in register_setting(). ?>

				<div class="sfpp-tab" id="tab_basic"><?php do_settings_sections( 'sfpp-settings' ); ?></div>

				<div class="sfpp-tab" id="tab_extras"><?php //do_settings_sections( 'sfpp-extras' ); ?></div>

			</div>

			<?php submit_button(); ?>

		</form>

		<div id="aside">

			<h2><?php _e( 'Sponsors', SIMPLE_FACEBOOK_PAGE_I18N ); ?></h2>

			<div id="banner">
				<a href="http://www.siteground.com" onClick="this.href='http://bit.ly/1FffXrN'" >
					<img src="https://ua.siteground.com/img/banners/application/wordpress/250x250.gif" alt="Web Hosting" width="250" height="250" border="0">
				</a>
			</div>

			<div id="banner">
				<a href="http://www.csshero.org" onClick="this.href='http://bit.ly/1Flr6sW'">
					<img src="http://www.csshero.org/banners/250x250_01.png" alt="WordPress Theme Editor" width="250" height="250" border="0">
				</a>
			</div>

		</div>

	</div>

	<?php
	echo ob_get_clean();
}


/**
 * Insert plugin activation date into the site options.
 *
 * @since 1.4.2
 */
register_activation_hook( __FILE__, 'sfpp_insert_install_date' );
function sfpp_insert_install_date() {

	$datetime_now = new DateTime();     // get the current date
	$date_string  = $datetime_now->format( 'Y-m-d' );   // make it pretty

	add_site_option( SIMPLE_FACEBOOK_PAGE_INSTALL_DATE, $date_string, '', 'no' );   // add the install date into the site options

	return $date_string;    // insert install date on plugin activation
}


/**
 * Retrieve plugin activation date from site options.
 *
 * @since 1.4.2
 */
function sfpp_get_install_date() {

	$date_string = get_site_option( SIMPLE_FACEBOOK_PAGE_INSTALL_DATE, '' );    // retrieve activation date

	if ( $date_string == '' ) {

		$date_string = sfpp_insert_install_date();  // there is no install date, plugin was installed before version 1.2.0. add it now.

	}

	return new DateTime( $date_string );    // return plugin activation date
}


/**
 * Check current user for admin & notice hide catch.
 *
 * @see sfpp_display_admin_notice()
 *
 * @since 1.4.2
 */
add_action( 'plugins_loaded', 'sfpp_admin_notices' );
function sfpp_admin_notices() {

	/**
	 * Check if current user is an admin & abort if they are not.
	 */
	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}

	add_action( 'admin_init', 'sfpp_catch_hide_notice' );  // admin notice hide catch

	/**
	 * Check if admin notice has already been hidden.
	 */
	$current_user = wp_get_current_user();
	$hide_notice  = get_user_meta( $current_user->ID, SIMPLE_FACEBOOK_PAGE_NOTICE_KEY, true );

	if ( current_user_can( 'install_plugins' ) && $hide_notice == '' ) {

		$datetime_install = sfpp_get_install_date();    // get installation date
		$datetime_past    = new DateTime( '-10 days' ); // set 10 day difference

		if ( $datetime_past >= $datetime_install ) {

			/**
			 * Display admin notice 10 days after activation.
			 */
			add_action( 'admin_notices', 'sfpp_display_admin_notice' );

		} // end install date check

	} // end admin check & hidden notice check

}


/**
 * Find out if the admin notice has been hidden.
 *
 * @since 1.4.2
 */
function sfpp_catch_hide_notice() {

	if ( isset( $_GET[SIMPLE_FACEBOOK_PAGE_NOTICE_KEY] ) && current_user_can( 'install_plugins' ) ) {

		//* Add user meta
		global $current_user;

		add_user_meta( $current_user->ID, SIMPLE_FACEBOOK_PAGE_NOTICE_KEY, '1', true );

		//* Build redirect URL
		$query_params = sfpp_get_admin_querystring_array();

		unset( $query_params[SIMPLE_FACEBOOK_PAGE_NOTICE_KEY] );

		$query_string = http_build_query( $query_params );

		if ( $query_string != '' ) {
			$query_string = '?' . $query_string;
		}

		$redirect_url = 'http';
		if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) {
			$redirect_url .= 's';
		}

		$redirect_url .= '://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . $query_string;

		//* Redirect
		wp_redirect( $redirect_url );
		exit;
	}
}


/**
 * Admin query string helper function.
 *
 * @since 1.4.2
 *
 * @return mixed
 */
function sfpp_get_admin_querystring_array() {

	parse_str( $_SERVER['QUERY_STRING'], $params );

	return $params;
}


/**
 * Displays admin notice.
 *
 * This admin notice is only displayed once 10 days after plugin activation.
 *
 * @since 1.4.2
 */
function sfpp_display_admin_notice() {

	$query_params = sfpp_get_admin_querystring_array();
	$query_string = '?' . http_build_query( array_merge( $query_params, array( SIMPLE_FACEBOOK_PAGE_NOTICE_KEY => '1' ) ) );

	echo '<div class="updated"><p>';
	printf( __( "You've been using <b>Simple Facebook Page Plugin & Shortcode</b> for some time now, could you please give it a review at wordpress.org? <br /><br /> <a href='%s' target='_blank'>Yes, take me there!</a> - <a href='%s'>I've already done this!</a>" ), 'https://wordpress.org/support/view/plugin-reviews/simple-facebook-twitter-widget', $query_string );
	echo "</p></div>";
}


/**
 * Class Simple_Facebook_Page_Feed_Widget
 *
 * @since 1.0.0
 *
 * @modified 1.2.0 Wrapped shortcode in comment for debug/tracking.
 * @modified 1.3.0 Added alignment parameter.
 * @modified 1.4.2 Added version to debug comment.
 */
class Simple_Facebook_Page_Feed_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'Simple_Facebook_Page_Feed_Widget',
			__( 'Simple Facebook Page Widget', SIMPLE_FACEBOOK_PAGE_I18N ),
			array( 'description' => __( 'Easily display your Facebook Page feed.', SIMPLE_FACEBOOK_PAGE_I18N ), )
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {


		//* Apply any styles before the widget.
		if ( array_key_exists( 'before_widget', $args ) ) {
			echo $args['before_widget'];
		}

		//* Apply any styles before & after widget title.
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		$output = '';

		//* Comment for tracking/debugging
		$output .= '<!-- This Facebook Page Feed was generated with Simple Facebook Page Widget & Shortcode plugin v' . SIMPLE_FACEBOOK_PAGE_VERSION . ' - https://wordpress.org/plugins/simple-facebook-twitter-widget/ -->';

		//* Wrapper for alignment
		$output .= '<div id="simple-facebook-widget" style="text-align:' . esc_attr( $instance['align'] ) . ';">';

		//* Main Facebook Feed
		$output .= '<div class="fb-page" ';
		$output .= 'data-href="' . esc_attr( $instance['href'] ) . '" ';
		$output .= 'data-width="' . esc_attr( $instance['width'] ) . '" ';
		$output .= 'data-height="' . esc_attr( $instance['height'] ) . '" ';
		$output .= 'data-hide-cover="' . esc_attr( $instance['show_cover'] ) . '" ';
		$output .= 'data-show-facepile="' . esc_attr( $instance['show_facepile'] ) . '" ';
		$output .= 'data-show-posts="' . esc_attr( $instance['show_posts'] ) . '">';
		$output .= '</div>';

		// end wrapper
		$output .= '</div>';

		// end comment
		$output .= '<!-- End Simple Facebook Page Plugin (Widget) -->';

		echo $output;

		if ( array_key_exists( 'after_widget', $args ) ) {
			echo $args['after_widget'];
		}

	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 *
	 * @return string|void
	 */
	public function form( $instance ) {

		/**
		 * Set up the default form values.
		 *
		 * @var $defaults
		 */
		$defaults = $this->sfpp_defaults();

		/**
		 * Merge the user-selected arguments with the defaults.
		 *
		 * @var $instance
		 */
		$instance = wp_parse_args( (array) $instance, $defaults );

		/**
		 * The Widget Title (optional)
		 *
		 * @var string $title
		 */
		$title = strip_tags( $instance['title'] );

		/**
		 * The URL of the Facebook Page (required)
		 *
		 * @var $href string This is the only required value.
		 */
		$href = strip_tags( $instance['href'] );

		/**
		 * The pixel width of the plugin.
		 * Min. is 280
		 * Max. is 500
		 *
		 * @var $width array Defaults to 340.
		 */
		$width = range( 280, 500, 20 );

		/**
		 * The maximum pixel height of the plugin.
		 * Min. is 130
		 *
		 * @var $height array Defaults to 500.
		 */
		$height = range( 125, 800, 25 );

		/**
		 * Show cover photo in the header
		 */
		$show_cover = array( 'true' => 'Yes', 'false' => 'No' );

		/**
		 * Show profile photos when friends like this
		 */
		$show_facepile = array( 'true' => 'Yes', 'false' => 'No' );

		/**
		 * Show posts from the Page's timeline.
		 */
		$show_posts = array( 'true' => 'Yes', 'false' => 'No' );

		/**
		 * Alignment of the widget.
		 *
		 * @var $align array Allows initial, left, center, and right text-align.
		 */
		$align = array( 'initial' => 'None', 'left' => 'Left', 'center' => 'Center', 'right' => 'Right' );

		/**
		 * Facebook wants to be difficult and use the term "Hide Cover" instead of show cover.
		 */
		$reverse_boolean = array ( 0 => 'Yes', 1 => 'No' );

		$boolean = array( 1 => 'Yes', 0 => 'No' );

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', SIMPLE_FACEBOOK_PAGE_I18N ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'href' ); ?>"><?php _e( 'Facebook Page URL:', SIMPLE_FACEBOOK_PAGE_I18N ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'href' ); ?>" name="<?php echo $this->get_field_name( 'href' ); ?>" value="<?php echo esc_attr( $instance['href'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Width:', SIMPLE_FACEBOOK_PAGE_I18N ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>">
				<?php foreach ( $width as $val ): ?>
					<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $instance['width'], $val ); ?>><?php echo esc_html( $val ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Height:', SIMPLE_FACEBOOK_PAGE_I18N ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>">
				<?php foreach ( $height as $val ): ?>
					<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $instance['height'], $val ); ?>><?php echo esc_html( $val ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_cover' ); ?>"><?php _e( 'Show Cover Photo?', SIMPLE_FACEBOOK_PAGE_I18N ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'show_cover' ); ?>" name="<?php echo $this->get_field_name( 'show_cover' ); ?>">
				<?php foreach ( $reverse_boolean as $key => $val ): ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $instance['show_cover'], $key ); ?>><?php echo esc_html( $val ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_facepile' ); ?>"><?php _e( 'Show Facepile?', SIMPLE_FACEBOOK_PAGE_I18N ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'show_facepile' ); ?>" name="<?php echo $this->get_field_name( 'show_facepile' ); ?>">
				<?php foreach ( $boolean as $key => $val ): ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $instance['show_facepile'], $key ); ?>><?php echo esc_html( $val ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_posts' ); ?>"><?php _e( 'Show Posts?', SIMPLE_FACEBOOK_PAGE_I18N ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'show_posts' ); ?>" name="<?php echo $this->get_field_name( 'show_posts' ); ?>">
				<?php foreach ( $boolean as $key => $val ): ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $instance['show_posts'], $key ); ?>><?php echo esc_html( $val ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'align' ); ?>"><?php _e( 'Alignment:', SIMPLE_FACEBOOK_PAGE_I18N ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'align' ); ?>" name="<?php echo $this->get_field_name( 'align' ); ?>">
				<?php foreach ( $align as $key => $val ): ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $instance['align'], $key ); ?>><?php echo esc_html( $val ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
	<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {

		/**
		 * Default arguments.
		 *
		 * @var array $defaults
		 */
		$defaults = $this->sfpp_defaults();

		/**
		 * Update logic.
		 *
		 * @var array $instance
		 */
		$instance = $old_instance;
		foreach ( $defaults as $key => $val ) {
			$instance[ $key ] = strip_tags( $new_instance[ $key ] );
		}

		return $instance;
	}

	/**
	 * Set up defaults form values in an array.
	 *
	 * @return array
	 */
	function sfpp_defaults() {

		$defaults = array(
			'title'         => esc_attr__( 'Facebook Page Widget', SIMPLE_FACEBOOK_PAGE_I18N ),
			'href'          => 'https://www.facebook.com/facebook',
			'width'         => '340',
			'height'        => '500',
			'show_cover'    => '0',
			'show_facepile' => '0',
			'show_posts'    => '1',
			'align'         => 'initial',
		);

		return $defaults;
	}

}