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
if ( ! defined( 'SIMPLE_FACEBOOK_PAGE_WIDGET_LIB' ) ) {
    define( 'SIMPLE_FACEBOOK_PAGE_WIDGET_LIB', SIMPLE_FACEBOOK_PAGE_WIDGET_DIRECTORY . 'lib/' );
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
 * Enqueue Chosen scripts and styles for easier language selection.
 * https://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
 *
 * http://harvesthq.github.io/chosen/
 *
 * @since 1.4.0
 */
add_action( 'admin_enqueue_scripts', 'sfpp_admin_enqueue_scripts_chosen' );
function sfpp_admin_enqueue_scripts_chosen( $hook ) {

    /*
    //* Check to see if we're on the settings page.
    if ( 'options-general.php?page=sfpp-settings' != $hook ) {
        return;
    }
    */

    //* Enqueue scripts if we are.
    wp_enqueue_script( 'chosen-js', SIMPLE_FACEBOOK_PAGE_WIDGET_LIB . 'chosen/chosen.jquery.js', array( 'jquery' ) );
    wp_enqueue_script( 'chosen-custom', SIMPLE_FACEBOOK_PAGE_WIDGET_LIB . 'chosen/chosen.js', array( 'jquery' ) );

    wp_enqueue_style( 'chosen-css', SIMPLE_FACEBOOK_PAGE_WIDGET_LIB . 'chosen/chosen.css' );

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
	create_function( '', 'return register_widget("Simple_Facebook_Page_Feed_Widget");' )
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

	<select id="sfpp_settings[language]" class="chosen-select" name="sfpp_settings[language]">
        <option value="af_ZA" <?php selected( $sfpp_options['language'], 'Afrikaans' ); ?>>Afrikaans</option>
        <option value="ak_GH" <?php selected( $sfpp_options['language'], 'Akan' ); ?>>Akan</option>
        <option value="am_ET" <?php selected( $sfpp_options['language'], 'Amharic' ); ?>>Amharic</option>
        <option value="ar_AR" <?php selected( $sfpp_options['language'], 'Arabic' ); ?>>Arabic</option>
        <option value="as_IN" <?php selected( $sfpp_options['language'], 'Assamese' ); ?>>Assamese</option>
        <option value="ay_BO" <?php selected( $sfpp_options['language'], 'Aymara' ); ?>>Aymara</option>
        <option value="az_AZ" <?php selected( $sfpp_options['language'], 'Azerbaijani' ); ?>>Azerbaijani</option>
        <option value="be_BY" <?php selected( $sfpp_options['language'], 'Belarusian' ); ?>>Belarusian</option>
        <option value="bg_BG" <?php selected( $sfpp_options['language'], 'Bulgarian' ); ?>>Bulgarian</option>
        <option value="bn_IN" <?php selected( $sfpp_options['language'], 'Bengali' ); ?>>Bengali</option>
        <option value="br_FR" <?php selected( $sfpp_options['language'], 'Breton' ); ?>>Breton</option>
        <option value="bs_BA" <?php selected( $sfpp_options['language'], 'Bosnian' ); ?>>Bosnian</option>
        <option value="ca_ES" <?php selected( $sfpp_options['language'], 'Catalan' ); ?>>Catalan</option>
        <option value="cb_IQ" <?php selected( $sfpp_options['language'], 'Sorani Kurdish' ); ?>>Sorani Kurdish</option>
        <option value="ck_US" <?php selected( $sfpp_options['language'], 'Cherokee' ); ?>>Cherokee</option>
        <option value="co_FR" <?php selected( $sfpp_options['language'], 'Corsican' ); ?>>Corsican</option>
        <option value="cs_CZ" <?php selected( $sfpp_options['language'], 'Czech' ); ?>>Czech</option>
        <option value="cx_PH" <?php selected( $sfpp_options['language'], 'Cebuano' ); ?>>Cebuano</option>
        <option value="cy_GB" <?php selected( $sfpp_options['language'], 'Welsh' ); ?>>Welsh</option>
        <option value="da_DK" <?php selected( $sfpp_options['language'], 'Danish' ); ?>>Danish</option>
        <option value="de_DE" <?php selected( $sfpp_options['language'], 'German' ); ?>>German</option>
        <option value="el_GR" <?php selected( $sfpp_options['language'], 'Greek' ); ?>>Greek</option>
        <option value="en_GB" <?php selected( $sfpp_options['language'], 'English (UK)' ); ?>>English (UK)</option>
        <option value="en_IN" <?php selected( $sfpp_options['language'], 'English (India)' ); ?>>English (India)</option>
        <option value="en_PI" <?php selected( $sfpp_options['language'], 'English (Pirate)' ); ?>>English (Pirate)</option>
        <option value="en_UD" <?php selected( $sfpp_options['language'], 'English (Upside Down)' ); ?>>English (Upside Down)</option>
        <option value="en_US" <?php selected( $sfpp_options['language'], 'English (US)' ); ?>>English (US)</option>
        <option value="eo_EO" <?php selected( $sfpp_options['language'], 'Esperanto' ); ?>>Esperanto</option>
        <option value="es_CO" <?php selected( $sfpp_options['language'], 'Spanish (Colombia)' ); ?>>Spanish (Colombia)</option>
        <option value="es_ES" <?php selected( $sfpp_options['language'], 'Spanish (Spain)' ); ?>>Spanish (Spain)</option>
        <option value="es_LA" <?php selected( $sfpp_options['language'], 'Spanish' ); ?>>Spanish</option>
        <option value="et_EE" <?php selected( $sfpp_options['language'], 'Estonian' ); ?>>Estonian</option>
        <option value="eu_ES" <?php selected( $sfpp_options['language'], 'Basque' ); ?>>Basque</option>
        <option value="fa_IR" <?php selected( $sfpp_options['language'], 'Persian' ); ?>>Persian</option>
        <option value="fb_LT" <?php selected( $sfpp_options['language'], 'Leet Speak' ); ?>>Leet Speak</option>
        <option value="ff_NG" <?php selected( $sfpp_options['language'], 'Fulah' ); ?>>Fulah</option>
        <option value="fi_FI" <?php selected( $sfpp_options['language'], 'Finnish' ); ?>>Finnish</option>
        <option value="fo_FO" <?php selected( $sfpp_options['language'], 'Faroese' ); ?>>Faroese</option>
        <option value="fr_CA" <?php selected( $sfpp_options['language'], 'French (Canada)' ); ?>>French (Canada)</option>
        <option value="fr_FR" <?php selected( $sfpp_options['language'], 'French (France)' ); ?>>French (France)</option>
        <option value="fy_NL" <?php selected( $sfpp_options['language'], 'Frisian' ); ?>>Frisian</option>
        <option value="ga_IE" <?php selected( $sfpp_options['language'], 'Irish' ); ?>>Irish</option>
        <option value="gl_ES" <?php selected( $sfpp_options['language'], 'Galician' ); ?>>Galician</option>
        <option value="gn_PY" <?php selected( $sfpp_options['language'], 'Guarani' ); ?>>Guarani</option>
        <option value="gu_IN" <?php selected( $sfpp_options['language'], 'Gujarati' ); ?>>Gujarati</option>
        <option value="gx_GR" <?php selected( $sfpp_options['language'], 'Classical Greek' ); ?>>Classical Greek</option>
        <option value="ha_NG" <?php selected( $sfpp_options['language'], 'Hausa' ); ?>>Hausa</option>
        <option value="he_IL" <?php selected( $sfpp_options['language'], 'Hebrew' ); ?>>Hebrew</option>
        <option value="hi_IN" <?php selected( $sfpp_options['language'], 'Hindi' ); ?>>Hindi</option>
        <option value="hr_HR" <?php selected( $sfpp_options['language'], 'Croatian' ); ?>>Croatian</option>
        <option value="hu_HU" <?php selected( $sfpp_options['language'], 'Hungarian' ); ?>>Hungarian</option>
        <option value="hy_AM" <?php selected( $sfpp_options['language'], 'Armenian' ); ?>>Armenian</option>
        <option value="id_ID" <?php selected( $sfpp_options['language'], 'Indonesian' ); ?>>Indonesian</option>
        <option value="ig_NG" <?php selected( $sfpp_options['language'], 'Igbo' ); ?>>Igbo</option>
        <option value="is_IS" <?php selected( $sfpp_options['language'], 'Icelandic' ); ?>>Icelandic</option>
        <option value="it_IT" <?php selected( $sfpp_options['language'], 'Italian' ); ?>>Italian</option>
        <option value="ja_JP" <?php selected( $sfpp_options['language'], 'Japanese' ); ?>>Japanese</option>
        <option value="ja_KS" <?php selected( $sfpp_options['language'], 'Japanese (Kansai)' ); ?>>Japanese (Kansai)</option>
        <option value="jv_ID" <?php selected( $sfpp_options['language'], 'Javanese' ); ?>>Javanese</option>
        <option value="ka_GE" <?php selected( $sfpp_options['language'], 'Georgian' ); ?>>Georgian</option>
        <option value="kk_KZ" <?php selected( $sfpp_options['language'], 'Kazakh' ); ?>>Kazakh</option>
        <option value="km_KH" <?php selected( $sfpp_options['language'], 'Khmer' ); ?>>Khmer</option>
        <option value="kn_IN" <?php selected( $sfpp_options['language'], 'Kannada' ); ?>>Kannada</option>
        <option value="ko_KR" <?php selected( $sfpp_options['language'], 'Korean' ); ?>>Korean</option>
        <option value="ku_TR" <?php selected( $sfpp_options['language'], 'Kurdish (Kurmanji)' ); ?>>Kurdish (Kurmanji)</option>
        <option value="la_VA" <?php selected( $sfpp_options['language'], 'Latin' ); ?>>Latin</option>
        <option value="lg_UG" <?php selected( $sfpp_options['language'], 'Ganda' ); ?>>Ganda</option>
        <option value="li_NL" <?php selected( $sfpp_options['language'], 'Limburgish' ); ?>>Limburgish</option>
        <option value="ln_CD" <?php selected( $sfpp_options['language'], 'Lingala' ); ?>>Lingala</option>
        <option value="lo_LA" <?php selected( $sfpp_options['language'], 'Lao' ); ?>>Lao</option>
        <option value="lt_LT" <?php selected( $sfpp_options['language'], 'Lithuanian' ); ?>>Lithuanian</option>
        <option value="lv_LV" <?php selected( $sfpp_options['language'], 'Latvian' ); ?>>Latvian</option>
        <option value="mg_MG" <?php selected( $sfpp_options['language'], 'Malagasy' ); ?>>Malagasy</option>
        <option value="mk_MK" <?php selected( $sfpp_options['language'], 'Macedonian' ); ?>>Macedonian</option>
        <option value="ml_IN" <?php selected( $sfpp_options['language'], 'Malayalam' ); ?>>Malayalam</option>
        <option value="mn_MN" <?php selected( $sfpp_options['language'], 'Mongolian' ); ?>>Mongolian</option>
        <option value="mr_IN" <?php selected( $sfpp_options['language'], 'Marathi' ); ?>>Marathi</option>
        <option value="ms_MY" <?php selected( $sfpp_options['language'], 'Malay' ); ?>>Malay</option>
        <option value="mt_MT" <?php selected( $sfpp_options['language'], 'Maltese' ); ?>>Maltese</option>
        <option value="my_MM" <?php selected( $sfpp_options['language'], 'Burmese' ); ?>>Burmese</option>
        <option value="nb_NO" <?php selected( $sfpp_options['language'], 'Norwegian (bokmal)' ); ?>>Norwegian (bokmal)</option>
        <option value="nd_ZW" <?php selected( $sfpp_options['language'], 'Ndebele' ); ?>>Ndebele</option>
        <option value="ne_NP" <?php selected( $sfpp_options['language'], 'Nepali' ); ?>>Nepali</option>
        <option value="nl_BE" <?php selected( $sfpp_options['language'], 'Dutch (België)' ); ?>>Dutch (België)</option>
        <option value="nl_NL" <?php selected( $sfpp_options['language'], 'Dutch' ); ?>>Dutch</option>
        <option value="nn_NO" <?php selected( $sfpp_options['language'], 'Norwegian (nynorsk)' ); ?>>Norwegian (nynorsk)</option>
        <option value="ny_MW" <?php selected( $sfpp_options['language'], 'Chewa' ); ?>>Chewa</option>
        <option value="or_IN" <?php selected( $sfpp_options['language'], 'Oriya' ); ?>>Oriya</option>
        <option value="pa_IN" <?php selected( $sfpp_options['language'], 'Punjabi' ); ?>>Punjabi</option>
        <option value="pl_PL" <?php selected( $sfpp_options['language'], 'Polish' ); ?>>Polish</option>
        <option value="ps_AF" <?php selected( $sfpp_options['language'], 'Pashto' ); ?>>Pashto</option>
        <option value="pt_BR" <?php selected( $sfpp_options['language'], 'Portuguese (Brazil)' ); ?>>Portuguese (Brazil)</option>
        <option value="pt_PT" <?php selected( $sfpp_options['language'], 'Portuguese (Portugal)' ); ?>>Portuguese (Portugal)</option>
        <option value="qu_PE" <?php selected( $sfpp_options['language'], 'Quechua' ); ?>>Quechua</option>
        <option value="rm_CH" <?php selected( $sfpp_options['language'], 'Romansh' ); ?>>Romansh</option>
        <option value="ro_RO" <?php selected( $sfpp_options['language'], 'Romanian' ); ?>>Romanian</option>
        <option value="ru_RU" <?php selected( $sfpp_options['language'], 'Russian' ); ?>>Russian</option>
        <option value="rw_RW" <?php selected( $sfpp_options['language'], 'Kinyarwanda' ); ?>>Kinyarwanda</option>
        <option value="sa_IN" <?php selected( $sfpp_options['language'], 'Sanskrit' ); ?>>Sanskrit</option>
        <option value="sc_IT" <?php selected( $sfpp_options['language'], 'Sardinian' ); ?>>Sardinian</option>
        <option value="se_NO" <?php selected( $sfpp_options['language'], 'Northern Sámi' ); ?>>Northern Sámi</option>
        <option value="si_LK" <?php selected( $sfpp_options['language'], 'Sinhala' ); ?>>Sinhala</option>
        <option value="sk_SK" <?php selected( $sfpp_options['language'], 'Slovak' ); ?>>Slovak</option>
        <option value="sl_SI" <?php selected( $sfpp_options['language'], 'Slovenian' ); ?>>Slovenian</option>
        <option value="sn_ZW" <?php selected( $sfpp_options['language'], 'Shona' ); ?>>Shona</option>
        <option value="so_SO" <?php selected( $sfpp_options['language'], 'Somali' ); ?>>Somali</option>
        <option value="sq_AL" <?php selected( $sfpp_options['language'], 'Albanian' ); ?>>Albanian</option>
        <option value="sr_RS" <?php selected( $sfpp_options['language'], 'Serbian' ); ?>>Serbian</option>
        <option value="sv_SE" <?php selected( $sfpp_options['language'], 'Swedish' ); ?>>Swedish</option>
        <option value="sw_KE" <?php selected( $sfpp_options['language'], 'Swahili' ); ?>>Swahili</option>
        <option value="sy_SY" <?php selected( $sfpp_options['language'], 'Syriac' ); ?>>Syriac</option>
        <option value="sz_PL" <?php selected( $sfpp_options['language'], 'Silesian' ); ?>>Silesian</option>
        <option value="ta_IN" <?php selected( $sfpp_options['language'], 'Tamil' ); ?>>Tamil</option>
        <option value="te_IN" <?php selected( $sfpp_options['language'], 'Telugu' ); ?>>Telugu</option>
        <option value="tg_TJ" <?php selected( $sfpp_options['language'], 'Tajik' ); ?>>Tajik</option>
        <option value="th_TH" <?php selected( $sfpp_options['language'], 'Thai' ); ?>>Thai</option>
        <option value="tk_TM" <?php selected( $sfpp_options['language'], 'Turkmen' ); ?>>Turkmen</option>
        <option value="tl_PH" <?php selected( $sfpp_options['language'], 'Filipino' ); ?>>Filipino</option>
        <option value="tl_ST" <?php selected( $sfpp_options['language'], 'Klingon' ); ?>>Klingon</option>
        <option value="tr_TR" <?php selected( $sfpp_options['language'], 'Turkish' ); ?>>Turkish</option>
        <option value="tt_RU" <?php selected( $sfpp_options['language'], 'Tatar' ); ?>>Tatar</option>
        <option value="tz_MA" <?php selected( $sfpp_options['language'], 'Tamazight' ); ?>>Tamazight</option>
        <option value="uk_UA" <?php selected( $sfpp_options['language'], 'Ukrainian' ); ?>>Ukrainian</option>
        <option value="ur_PK" <?php selected( $sfpp_options['language'], 'Urdu' ); ?>>Urdu</option>
        <option value="uz_UZ" <?php selected( $sfpp_options['language'], 'Uzbek' ); ?>>Uzbek</option>
        <option value="vi_VN" <?php selected( $sfpp_options['language'], 'Vietnamese' ); ?>>Vietnamese</option>
        <option value="wo_SN" <?php selected( $sfpp_options['language'], 'Wolof' ); ?>>Wolof</option>
        <option value="xh_ZA" <?php selected( $sfpp_options['language'], 'Xhosa' ); ?>>Xhosa</option>
        <option value="yi_DE" <?php selected( $sfpp_options['language'], 'Yiddish' ); ?>>Yiddish</option>
        <option value="yo_NG" <?php selected( $sfpp_options['language'], 'Yoruba' ); ?>>Yoruba</option>
        <option value="zh_CN" <?php selected( $sfpp_options['language'], 'Simplified Chinese (China)' ); ?>>Simplified Chinese (China)</option>
        <option value="zh_HK" <?php selected( $sfpp_options['language'], 'Traditional Chinese (Hong Kong)' ); ?>>Traditional Chinese (Hong Kong)</option>
        <option value="zh_TW" <?php selected( $sfpp_options['language'], 'Traditional Chinese (Taiwan)' ); ?>>Traditional Chinese (Taiwan)</option>
        <option value="zu_ZA" <?php selected( $sfpp_options['language'], 'Zulu' ); ?>>Zulu</option>
        <option value="zz_TR" <?php selected( $sfpp_options['language'], 'Zazaki' ); ?>>Zazaki</option>
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