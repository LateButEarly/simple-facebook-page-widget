<?php
/**
 * Plugin Name:    Simple Facebook Page Plugin
 * Plugin URI:     https://wordpress.org/plugins/simple-facebook-twitter-widget/
 * Description:    Shows the Facebook Page feed in a sidebar widget and/or via shortcode.
 * Version:        1.5.0
 * Author:         Dylan Ryan
 * Author URI:     https://profiles.wordpress.org/irkanu
 * Domain Path:    /lang
 * Text Domain:    sfpp-lang
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
 * @subpackage  Page_Plugin
 * @author      Dylan Ryan
 * @version     1.5.0
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


class Simple_Facebook_Page_Plugin extends Simple_Facebook {

	protected $plugin_name = 'Simple Facebook Page Plugin';

	protected $plugin_slug = 'simple-facebook-page-plugin';

	protected $previous_version = '1.4.1';

	protected $plugin_key = 'sfpp-version';

	protected $install_key = 'sfpp-install-date';

	protected $notice_key = 'sfpp-hide-notice';

	protected $settings = 'sfpp_settings';

	protected $settings_group = 'sfpp_settings_group';

	protected $settings_page = 'sfpp_dashboard';

	protected $settings_basic_section = 'sfpp_basic_section';

	protected $settings_adv_section = 'sfpp_adv_section';

	protected $i18n = 'sfpp-lang';

	public static $instance;

	public static function init() {

		if ( is_null( self::$instance ) )
			self::$instance = new Simple_Facebook_Page_Plugin();
		return self::$instance;

	}

	public function __construct() {

		add_action( 'widgets_init', $this->register_widget() );

		add_action( 'admin_init', array( $this, 'register_settings' ) );

		add_action( 'admin_init', array( $this, 'maybe_hide_notice' ) );

		add_action( 'admin_menu', array( $this, 'page_plugin_settings_menu') );

		add_action( 'init', array ( $this, 'load_textdomain') );

		add_action( 'plugins_loaded', array( $this, 'admin_notice' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'localize_scripts' ) );

		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'quick_settings_link' ) );

		add_shortcode( 'facebook-page', array( $this, 'add_shortcode' ) );

		register_activation_hook( __FILE__, array( $this, 'set_activation_key' ) );

		register_activation_hook( __FILE__, array( $this, 'set_install_date' ) );

		register_uninstall_hook( __FILE__, array( $this, 'remove_activation_key' ) );

		register_uninstall_hook( __FILE__, array( $this, 'remove_install_date' ) );
	}


	/**
	 * Load the translation PO files.
	 * http://codex.wordpress.org/I18n_for_WordPress_Developers
	 *
	 * @since 1.1.0
	 *
	 * @modified 1.5.0 Wrapped in function and hooked into init.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( $this->i18n, false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
	}


	/**
	 * Enqueue Facebook script required for the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @modified 1.4.0 Localized the script for language option.
	 */
	public function localize_scripts() {

		$options = $this->get_options();

		//* Prepare the javascript for manipulation.
		wp_enqueue_script( $this->plugin_slug, Simple_Facebook::get_plugin_directory() . 'js/simple-facebook-page-root.js', array( 'jquery' ) );

		//* Pass the language option from the database to javascript.
		wp_localize_script( $this->plugin_slug, 'sfpp_script_vars', array(
				'language' => ( $options['language'] )
			)
		);

	}


	/**
	 * Registers the Simple_Facebook_Page_Feed_Widget widget class.
	 *
	 * @since 1.0.0
	 *
	 * @modified 1.2.1 Added compatibility for PHP 5.2 with create_function
	 * https://wordpress.org/support/topic/plugin-activation-error-9
	 */
	private function register_widget() {
		require_once('inc/class-simple-facebook-page-plugin-widget.php');
		return create_function( '', 'return register_widget("Simple_Facebook_Page_Feed_Widget");' );
	}


	/**
	 * Create the [facebook-page] shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @modified 1.2.0 Wrapped shortcode in comment for debug/tracking.
	 * @modified 1.3.0 Added alignment parameter.
	 * @modified 1.5.0 Added version to debug comment.
	 *
	 * @param   $atts   array   href, width, height, hide_cover, show_facepile, show_posts, align
	 *
	 * @return  string  Outputs the Facebook Page feed via shortcode.
	 */
	public function add_shortcode( $atts ) {

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

		$output .= '<!-- This Facebook Page Feed was generated with Simple Facebook Page Widget & Shortcode plugin v' . $this->current_version . ' - https://wordpress.org/plugins/simple-facebook-twitter-widget/ -->';

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

	private function get_options() {
		return get_option( $this->settings );
	}


	/**
	 * Sets the current version into the options table.
	 * http://www.smashingmagazine.com/2011/03/08/ten-things-every-wordpress-plugin-developer-should-know/
	 *
	 * @since 1.5.0
	 */
	public function set_activation_key() {
		add_option( $this->plugin_key, $this->current_version );
	}


	/**
	 * Removes the current version from the options table.
	 *
	 * @since 1.5.0
	 */
	public function remove_activation_key() {
		delete_option( $this->plugin_key ); // remove footprint
	}


	/**
	 * Helper function used to return the plugin installation date.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function set_install_date() {
		$datetime_now = new DateTime();                     // Get the current date.
		$date_string  = $datetime_now->format( 'Y-m-d' );   // Make it pretty.

		add_site_option( $this->install_key, $date_string, '', 'no' );   // add the install date into the site options

		return $date_string;
	}


	/**
	 * Retrieve plugin activation date from site options.
	 *
	 * @since 1.5.0
	 */
	public function get_install_date() {
		$date_string = get_site_option( $this->install_key, '' );    // retrieve activation date

		if ( $date_string == '' ) {

			$date_string = $this->set_install_date();  // there is no install date, plugin was installed before version 1.2.0. add it now.

		}

		return new DateTime( $date_string );    // return plugin activation date
	}


	/**
	 * Removes plugin's install date from the options table.
	 *
	 * @since 1.5.0
	 */
	public function remove_install_date() {
		delete_site_option( $this->install_key ); // remove install date
	}


	/**
	 * Registers the settings, sections, and fields.
	 * https://developer.wordpress.org/plugins/settings/creating-and-using-options/
	 *
	 * @since 1.4.0
	 */
	public function register_settings() {

		register_setting(
			$this->settings_group,          // settings section (group) - used on the admin page itself to setup fields
			$this->settings                 // setting name - get_option() to retrieve from database - retrieve it and store it in global variable
		);

		add_settings_section(
			$this->settings_basic_section,  // setup language section
			'Basic Settings',               // title of section
			array( $this, 'basic_setting_section_callback' ),  // display after the title & before the settings
			$this->settings_page            // setting page
		);

		add_settings_section(
			$this->settings_adv_section,  // setup language section
			'Advanced Settings',               // title of section
			array( $this, 'advanced_setting_section_callback' ),  // display after the title & before the settings
			$this->settings_page            // setting page
		);

		add_settings_field(
			$this->settings,                         // setting name
			__( 'Select a language:', $this->i18n ), // text before the display
			array( $this, 'language_setting_callback' ),             // displays the setting
			$this->settings_page,                    // setting page
			$this->settings_basic_section            // setting section
		);

	}


	/**
	 * Function that echos out any content at the top of the section (between heading and fields).
	 *
	 * @since 1.4.0
	 */
	public function basic_setting_section_callback() {

	}


	/**
	 * Function that echos out any content at the top of the section (between heading and fields).
	 *
	 * @since 1.4.0
	 */
	public function advanced_setting_section_callback() {

	}


	/**
	 * Function that fills the field with the desired form inputs. The function should echo its output.
	 *
	 * @since 1.4.0
	 *
	 * @modified 1.5.0 Set default language to English US.
	 */
	public function language_setting_callback() {

		$options = $this->get_options();   // get_option( 'sfpp_settings' );

		$options['language'] = isset( $options['language'] ) && ! empty( $options['language'] ) ? $options['language'] : 'en_US';

		?>

		<select id="sfpp_settings[language]" class="chosen-select" name="sfpp_settings[language]" title="<?php esc_attr__( 'Select language', $this->i18n ) ?>">
			<option value="af_ZA" <?php selected( $options['language'], 'af_ZA' ); ?>>Afrikaans</option>
			<option value="ak_GH" <?php selected( $options['language'], 'ak_GH' ); ?>>Akan</option>
			<option value="am_ET" <?php selected( $options['language'], 'am_ET' ); ?>>Amharic</option>
			<option value="ar_AR" <?php selected( $options['language'], 'ar_AR' ); ?>>Arabic</option>
			<option value="as_IN" <?php selected( $options['language'], 'as_IN' ); ?>>Assamese</option>
			<option value="ay_BO" <?php selected( $options['language'], 'ay_BO' ); ?>>Aymara</option>
			<option value="az_AZ" <?php selected( $options['language'], 'az_AZ' ); ?>>Azerbaijani</option>
			<option value="be_BY" <?php selected( $options['language'], 'be_BY' ); ?>>Belarusian</option>
			<option value="bg_BG" <?php selected( $options['language'], 'bg_BG' ); ?>>Bulgarian</option>
			<option value="bn_IN" <?php selected( $options['language'], 'bn_IN' ); ?>>Bengali</option>
			<option value="br_FR" <?php selected( $options['language'], 'br_FR' ); ?>>Breton</option>
			<option value="bs_BA" <?php selected( $options['language'], 'bs_BA' ); ?>>Bosnian</option>
			<option value="ca_ES" <?php selected( $options['language'], 'ca_ES' ); ?>>Catalan</option>
			<option value="cb_IQ" <?php selected( $options['language'], 'cb_IQ' ); ?>>Sorani Kurdish</option>
			<option value="ck_US" <?php selected( $options['language'], 'ck_US' ); ?>>Cherokee</option>
			<option value="co_FR" <?php selected( $options['language'], 'co_FR' ); ?>>Corsican</option>
			<option value="cs_CZ" <?php selected( $options['language'], 'cs_CZ' ); ?>>Czech</option>
			<option value="cx_PH" <?php selected( $options['language'], 'cx_PH' ); ?>>Cebuano</option>
			<option value="cy_GB" <?php selected( $options['language'], 'cy_GB' ); ?>>Welsh</option>
			<option value="da_DK" <?php selected( $options['language'], 'da_DK' ); ?>>Danish</option>
			<option value="de_DE" <?php selected( $options['language'], 'de_DE' ); ?>>German</option>
			<option value="el_GR" <?php selected( $options['language'], 'el_GR' ); ?>>Greek</option>
			<option value="en_GB" <?php selected( $options['language'], 'en_GB' ); ?>>English (UK)</option>
			<option value="en_IN" <?php selected( $options['language'], 'en_IN' ); ?>>English (India)</option>
			<option value="en_PI" <?php selected( $options['language'], 'en_PI' ); ?>>English (Pirate)</option>
			<option value="en_UD" <?php selected( $options['language'], 'en_UD' ); ?>>English (Upside Down)</option>
			<option value="en_US" <?php selected( $options['language'], 'en_US' ); ?>>English (US)</option>
			<option value="eo_EO" <?php selected( $options['language'], 'eo_EO' ); ?>>Esperanto</option>
			<option value="es_CO" <?php selected( $options['language'], 'es_CO' ); ?>>Spanish (Colombia)</option>
			<option value="es_ES" <?php selected( $options['language'], 'es_ES' ); ?>>Spanish (Spain)</option>
			<option value="es_LA" <?php selected( $options['language'], 'es_LA' ); ?>>Spanish</option>
			<option value="et_EE" <?php selected( $options['language'], 'et_EE' ); ?>>Estonian</option>
			<option value="eu_ES" <?php selected( $options['language'], 'eu_ES' ); ?>>Basque</option>
			<option value="fa_IR" <?php selected( $options['language'], 'fa_IR' ); ?>>Persian</option>
			<option value="fb_LT" <?php selected( $options['language'], 'fb_LT' ); ?>>Leet Speak</option>
			<option value="ff_NG" <?php selected( $options['language'], 'ff_NG' ); ?>>Fulah</option>
			<option value="fi_FI" <?php selected( $options['language'], 'fi_FI' ); ?>>Finnish</option>
			<option value="fo_FO" <?php selected( $options['language'], 'fo_FO' ); ?>>Faroese</option>
			<option value="fr_CA" <?php selected( $options['language'], 'fr_CA' ); ?>>French (Canada)</option>
			<option value="fr_FR" <?php selected( $options['language'], 'fr_FR' ); ?>>French (France)</option>
			<option value="fy_NL" <?php selected( $options['language'], 'fy_NL' ); ?>>Frisian</option>
			<option value="ga_IE" <?php selected( $options['language'], 'ga_IE' ); ?>>Irish</option>
			<option value="gl_ES" <?php selected( $options['language'], 'gl_ES' ); ?>>Galician</option>
			<option value="gn_PY" <?php selected( $options['language'], 'gn_PY' ); ?>>Guarani</option>
			<option value="gu_IN" <?php selected( $options['language'], 'gu_IN' ); ?>>Gujarati</option>
			<option value="gx_GR" <?php selected( $options['language'], 'gx_GR' ); ?>>Classical Greek</option>
			<option value="ha_NG" <?php selected( $options['language'], 'ha_NG' ); ?>>Hausa</option>
			<option value="he_IL" <?php selected( $options['language'], 'he_IL' ); ?>>Hebrew</option>
			<option value="hi_IN" <?php selected( $options['language'], 'hi_IN' ); ?>>Hindi</option>
			<option value="hr_HR" <?php selected( $options['language'], 'hr_HR' ); ?>>Croatian</option>
			<option value="hu_HU" <?php selected( $options['language'], 'hu_HU' ); ?>>Hungarian</option>
			<option value="hy_AM" <?php selected( $options['language'], 'hy_AM' ); ?>>Armenian</option>
			<option value="id_ID" <?php selected( $options['language'], 'id_ID' ); ?>>Indonesian</option>
			<option value="ig_NG" <?php selected( $options['language'], 'ig_NG' ); ?>>Igbo</option>
			<option value="is_IS" <?php selected( $options['language'], 'is_IS' ); ?>>Icelandic</option>
			<option value="it_IT" <?php selected( $options['language'], 'it_IT' ); ?>>Italian</option>
			<option value="ja_JP" <?php selected( $options['language'], 'ja_JP' ); ?>>Japanese</option>
			<option value="ja_KS" <?php selected( $options['language'], 'ja_KS' ); ?>>Japanese (Kansai)</option>
			<option value="jv_ID" <?php selected( $options['language'], 'jv_ID' ); ?>>Javanese</option>
			<option value="ka_GE" <?php selected( $options['language'], 'ka_GE' ); ?>>Georgian</option>
			<option value="kk_KZ" <?php selected( $options['language'], 'kk_KZ' ); ?>>Kazakh</option>
			<option value="km_KH" <?php selected( $options['language'], 'km_KH' ); ?>>Khmer</option>
			<option value="kn_IN" <?php selected( $options['language'], 'kn_IN' ); ?>>Kannada</option>
			<option value="ko_KR" <?php selected( $options['language'], 'ko_KR' ); ?>>Korean</option>
			<option value="ku_TR" <?php selected( $options['language'], 'ku_TR' ); ?>>Kurdish (Kurmanji)</option>
			<option value="la_VA" <?php selected( $options['language'], 'la_VA' ); ?>>Latin</option>
			<option value="lg_UG" <?php selected( $options['language'], 'lg_UG' ); ?>>Ganda</option>
			<option value="li_NL" <?php selected( $options['language'], 'li_NL' ); ?>>Limburgish</option>
			<option value="ln_CD" <?php selected( $options['language'], 'ln_CD' ); ?>>Lingala</option>
			<option value="lo_LA" <?php selected( $options['language'], 'lo_LA' ); ?>>Lao</option>
			<option value="lt_LT" <?php selected( $options['language'], 'lt_LT' ); ?>>Lithuanian</option>
			<option value="lv_LV" <?php selected( $options['language'], 'lv_LV' ); ?>>Latvian</option>
			<option value="mg_MG" <?php selected( $options['language'], 'mg_MG' ); ?>>Malagasy</option>
			<option value="mk_MK" <?php selected( $options['language'], 'mk_MK' ); ?>>Macedonian</option>
			<option value="ml_IN" <?php selected( $options['language'], 'ml_IN' ); ?>>Malayalam</option>
			<option value="mn_MN" <?php selected( $options['language'], 'mn_MN' ); ?>>Mongolian</option>
			<option value="mr_IN" <?php selected( $options['language'], 'mr_IN' ); ?>>Marathi</option>
			<option value="ms_MY" <?php selected( $options['language'], 'ms_MY' ); ?>>Malay</option>
			<option value="mt_MT" <?php selected( $options['language'], 'mt_MT' ); ?>>Maltese</option>
			<option value="my_MM" <?php selected( $options['language'], 'my_MM' ); ?>>Burmese</option>
			<option value="nb_NO" <?php selected( $options['language'], 'nb_NO' ); ?>>Norwegian (bokmal)</option>
			<option value="nd_ZW" <?php selected( $options['language'], 'nd_ZW' ); ?>>Ndebele</option>
			<option value="ne_NP" <?php selected( $options['language'], 'ne_NP' ); ?>>Nepali</option>
			<option value="nl_BE" <?php selected( $options['language'], 'nl_BE' ); ?>>Dutch (België)</option>
			<option value="nl_NL" <?php selected( $options['language'], 'nl_NL' ); ?>>Dutch</option>
			<option value="nn_NO" <?php selected( $options['language'], 'nn_NO' ); ?>>Norwegian (nynorsk)</option>
			<option value="ny_MW" <?php selected( $options['language'], 'ny_MW' ); ?>>Chewa</option>
			<option value="or_IN" <?php selected( $options['language'], 'or_IN' ); ?>>Oriya</option>
			<option value="pa_IN" <?php selected( $options['language'], 'pa_IN' ); ?>>Punjabi</option>
			<option value="pl_PL" <?php selected( $options['language'], 'pl_PL' ); ?>>Polish</option>
			<option value="ps_AF" <?php selected( $options['language'], 'ps_AF' ); ?>>Pashto</option>
			<option value="pt_BR" <?php selected( $options['language'], 'pt_BR' ); ?>>Portuguese (Brazil)</option>
			<option value="pt_PT" <?php selected( $options['language'], 'pt_PT' ); ?>>Portuguese (Portugal)</option>
			<option value="qu_PE" <?php selected( $options['language'], 'qu_PE' ); ?>>Quechua</option>
			<option value="rm_CH" <?php selected( $options['language'], 'rm_CH' ); ?>>Romansh</option>
			<option value="ro_RO" <?php selected( $options['language'], 'ro_RO' ); ?>>Romanian</option>
			<option value="ru_RU" <?php selected( $options['language'], 'ru_RU' ); ?>>Russian</option>
			<option value="rw_RW" <?php selected( $options['language'], 'rw_RW' ); ?>>Kinyarwanda</option>
			<option value="sa_IN" <?php selected( $options['language'], 'sa_IN' ); ?>>Sanskrit</option>
			<option value="sc_IT" <?php selected( $options['language'], 'sc_IT' ); ?>>Sardinian</option>
			<option value="se_NO" <?php selected( $options['language'], 'se_NO' ); ?>>Northern Sámi</option>
			<option value="si_LK" <?php selected( $options['language'], 'si_LK' ); ?>>Sinhala</option>
			<option value="sk_SK" <?php selected( $options['language'], 'sk_SK' ); ?>>Slovak</option>
			<option value="sl_SI" <?php selected( $options['language'], 'sl_SI' ); ?>>Slovenian</option>
			<option value="sn_ZW" <?php selected( $options['language'], 'sn_ZW' ); ?>>Shona</option>
			<option value="so_SO" <?php selected( $options['language'], 'so_SO' ); ?>>Somali</option>
			<option value="sq_AL" <?php selected( $options['language'], 'sq_AL' ); ?>>Albanian</option>
			<option value="sr_RS" <?php selected( $options['language'], 'sr_RS' ); ?>>Serbian</option>
			<option value="sv_SE" <?php selected( $options['language'], 'sv_SE' ); ?>>Swedish</option>
			<option value="sw_KE" <?php selected( $options['language'], 'sw_KE' ); ?>>Swahili</option>
			<option value="sy_SY" <?php selected( $options['language'], 'sy_SY' ); ?>>Syriac</option>
			<option value="sz_PL" <?php selected( $options['language'], 'sz_PL' ); ?>>Silesian</option>
			<option value="ta_IN" <?php selected( $options['language'], 'ta_IN' ); ?>>Tamil</option>
			<option value="te_IN" <?php selected( $options['language'], 'te_IN' ); ?>>Telugu</option>
			<option value="tg_TJ" <?php selected( $options['language'], 'tg_TJ' ); ?>>Tajik</option>
			<option value="th_TH" <?php selected( $options['language'], 'th_TH' ); ?>>Thai</option>
			<option value="tk_TM" <?php selected( $options['language'], 'tk_TM' ); ?>>Turkmen</option>
			<option value="tl_PH" <?php selected( $options['language'], 'tl_PH' ); ?>>Filipino</option>
			<option value="tl_ST" <?php selected( $options['language'], 'tl_ST' ); ?>>Klingon</option>
			<option value="tr_TR" <?php selected( $options['language'], 'tr_TR' ); ?>>Turkish</option>
			<option value="tt_RU" <?php selected( $options['language'], 'tt_RU' ); ?>>Tatar</option>
			<option value="tz_MA" <?php selected( $options['language'], 'tz_MA' ); ?>>Tamazight</option>
			<option value="uk_UA" <?php selected( $options['language'], 'uk_UA' ); ?>>Ukrainian</option>
			<option value="ur_PK" <?php selected( $options['language'], 'ur_PK' ); ?>>Urdu</option>
			<option value="uz_UZ" <?php selected( $options['language'], 'uz_UZ' ); ?>>Uzbek</option>
			<option value="vi_VN" <?php selected( $options['language'], 'vi_VN' ); ?>>Vietnamese</option>
			<option value="wo_SN" <?php selected( $options['language'], 'wo_SN' ); ?>>Wolof</option>
			<option value="xh_ZA" <?php selected( $options['language'], 'xh_ZA' ); ?>>Xhosa</option>
			<option value="yi_DE" <?php selected( $options['language'], 'yi_DE' ); ?>>Yiddish</option>
			<option value="yo_NG" <?php selected( $options['language'], 'yo_NG' ); ?>>Yoruba</option>
			<option value="zh_CN" <?php selected( $options['language'], 'zh_CN' ); ?>>Simplified Chinese (China)</option>
			<option value="zh_HK" <?php selected( $options['language'], 'zh_HK' ); ?>>Traditional Chinese (Hong Kong)</option>
			<option value="zh_TW" <?php selected( $options['language'], 'zh_TW' ); ?>>Traditional Chinese (Taiwan)</option>
			<option value="zu_ZA" <?php selected( $options['language'], 'zu_ZA' ); ?>>Zulu</option>
			<option value="zz_TR" <?php selected( $options['language'], 'zz_TR' ); ?>>Zazaki</option>
		</select>

	<?php
	}


	/**
	 * Creates a quick link to the settings page.
	 *
	 * @since    1.5.0
	 *
	 * @param   $actions
	 *
	 * @return      string  Outputs a settings link to the settings page.
	 * @internal    param   $plugin_file
	 */
	public function quick_settings_link( $actions ) {

		array_unshift( $actions, sprintf( '<a href="%s">%s</a>', admin_url( 'options-general.php?page=sfpp_dashboard' ), __( 'Settings' ) ) );

		return $actions;
	}


	/**
	 * Registers the admin settings menu.
	 * https://developer.wordpress.org/plugins/settings/custom-settings-page/#creating-the-menu-item
	 *
	 * Only loads libraries required on the settings page.
	 * http://codex.wordpress.org/Function_Reference/wp_enqueue_script#Load_scripts_only_on_plugin_pages
	 *
	 * @since 1.4.0
	 */
	public function page_plugin_settings_menu() {

		$page_title = 'Simple Facebook Settings';
		$menu_title = 'Simple Facebook';
		$capability = 'manage_options';
		$callback   = array( $this, 'display_options_page' );
		$page_plugin_callback = array( $this, 'display_page_plugin_options_page' );
		$comments_callback = array ( $this, 'display_comments_options_page' );
		$embedded_callback = array ( $this, 'display_embedded_options_page' );
		$like_share_send_callback = array( $this, 'display_like_share_send_options_page' );
		$follow_callback = array( $this, 'display_follow_options_page' );
		$icon       = 'dashicons-facebook';
		$position   = '95.1337';

		//$admin_settings_page = add_menu_page( $page_title, $menu_title, $capability, $this->settings_page, $callback, $icon, $position );

		//add_submenu_page( $this->settings_page, $page_title, __( 'General', $this->i18n ), $capability, $this->settings_page, $callback );

		add_submenu_page( $this->settings_page, '', __( 'Page Plugin', $this->i18n ), $capability, 'sfpp_page_plugin', $page_plugin_callback );

		add_submenu_page( $this->settings_page, '', __( 'Comments', $this->i18n ), $capability, 'sfpp_comments', $comments_callback );

		add_submenu_page( $this->settings_page, '', __( 'Embedded', $this->i18n ), $capability, 'sfpp_embedded', $embedded_callback );

		add_submenu_page( $this->settings_page, '', __( 'Follow', $this->i18n ), $capability, 'sfpp_follow', $follow_callback );

		add_submenu_page( $this->settings_page, '', __( 'Like, Share, Send', $this->i18n ), $capability, 'sfpp_like_share_send', $like_share_send_callback );

	}





	/**
	 * @todo
	 */
	public function display_page_plugin_options_page() {

	}


	/**
	 * @todo
	 */
	public function display_comments_options_page() {

	}

	/**
	 * @todo
	 */
	public function display_embedded_options_page() {

	}


	/**
	 * @todo
	 */
	public function display_like_share_send_options_page() {

	}


	/**
	 * @todo
	 */
	public function display_follow_options_page() {

	}


	/**
	 * Check current user for admin & maybe hide notice.
	 *
	 * @see display_admin_notice()
	 *
	 * @since 1.5.0
	 */
	public function admin_notice() {

		/**
		 * Check if current user is an admin & abort if they are not.
		 */
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		/**
		 * Check if admin notice has already been hidden.
		 */
		$current_user = wp_get_current_user();
		$hide_notice  = get_user_meta( $current_user->ID, $this->notice_key, true );

		if ( current_user_can( 'install_plugins' ) && $hide_notice == '' ) {

			$datetime_install = $this->get_install_date();    // get installation date
			$datetime_past    = new DateTime( '-10 days' ); // set 10 day difference

			if ( $datetime_past >= $datetime_install ) {

				/**
				 * Display admin notice 10 days after activation.
				 */
				add_action( 'admin_notices', array( $this, 'display_admin_notice' ) );

			} // end install date check

		} // end admin check & hidden notice check

	}


	/**
	 * Find out if the admin notice has been hidden.
	 *
	 * @since 1.5.0
	 */
	function maybe_hide_notice() {

		if ( isset( $_GET[$this->notice_key] ) && current_user_can( 'install_plugins' ) ) {

			//* Add user meta
			global $current_user;

			add_user_meta( $current_user->ID, $this->notice_key, '1', true );

			//* Build redirect URL
			$query_params = $this->get_admin_querystring_array();

			unset( $query_params[$this->notice_key] );

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
	 * @since 1.5.0
	 *
	 * @return mixed
	 */
	function get_admin_querystring_array() {

		parse_str( $_SERVER['QUERY_STRING'], $params );

		return $params;
	}


	/**
	 * Displays admin notice.
	 *
	 * This admin notice is only displayed once 10 days after plugin activation.
	 *
	 * @since 1.5.0
	 */
	function display_admin_notice() {

		$query_params = $this->get_admin_querystring_array();
		$query_string = '?' . http_build_query( array_merge( $query_params, array( $this->notice_key => '1' ) ) );

		echo '<div class="updated"><p>';
		printf( __( "You've been using <b>Simple Facebook Page Plugin & Shortcode</b> for some time now, could you please give it a review at wordpress.org? <br /><br /> <a href='%s' target='_blank'>Yes, take me there!</a> - <a href='%s'>I've already done this!</a>" ), 'https://wordpress.org/support/view/plugin-reviews/simple-facebook-twitter-widget', $query_string );
		echo "</p></div>";
	}

}

class Simple_Facebook {

	protected $settings_page = 'sfpp_dashboard';

	protected $current_version = '1.5.0';

	public static $instance;

	public static function init() {

		if ( is_null( self::$instance ) )
			self::$instance = new Simple_Facebook();
		return self::$instance;

	}

	public function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_settings_menu') );

	}

	public function get_plugin_directory() {
		return plugin_dir_url( __FILE__ );
	}

	public function get_lib_directory() {
		return plugin_dir_url( __FILE__ ) . 'lib/';
	}

	public function admin_settings_menu() {

		$page_title = 'Simple Facebook Settings';
		$menu_title = 'Simple Facebook';
		$capability = 'manage_options';
		$callback   = array( $this, 'display_options_page' );
		$page_plugin_callback = array( $this, 'display_page_plugin_options_page' );
		$comments_callback = array ( $this, 'display_comments_options_page' );
		$embedded_callback = array ( $this, 'display_embedded_options_page' );
		$like_share_send_callback = array( $this, 'display_like_share_send_options_page' );
		$follow_callback = array( $this, 'display_follow_options_page' );
		$icon       = 'dashicons-facebook';
		$position   = '95.1337';

		$admin_settings_page = add_menu_page( $page_title, $menu_title, $capability, $this->settings_page, $callback, $icon, $position );

		add_submenu_page( $this->settings_page, $page_title, __( 'General', Simple_Facebook_Language::translate() ), $capability, $this->settings_page, $callback );

		/**
		 * Only loads libraries required on the settings page.
		 * http://codex.wordpress.org/Function_Reference/wp_enqueue_script#Load_scripts_only_on_plugin_pages
		 *
		 * @since 1.5.0
		 */
		add_action( 'admin_print_scripts-' . $admin_settings_page, array( $this, 'admin_enqueue_scripts' ) );

	}

	/**
	 * Enqueue admin only scripts and styles on our settings page.
	 * https://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	 *
	 * @uses http://harvesthq.github.io/chosen/
	 *
	 * @since 1.4.0
	 */
	public function admin_enqueue_scripts() {
		//* Chosen script
		wp_enqueue_script( 'chosen-js',     $this->get_lib_directory() .  'chosen/chosen.jquery.min.js', array( 'jquery' ) );

		//* Chosen stylesheet
		wp_enqueue_style( 'chosen-style',   $this->get_lib_directory() .  'chosen/chosen.min.css' );

		//* Custom admin javascript
		wp_enqueue_script( 'admin-js',      $this->get_plugin_directory() .  'js/admin.js', array( 'jquery' ) );

		//* Custom admin stylesheet
		wp_enqueue_style( 'admin-style',    $this->get_plugin_directory() .  'css/admin.css' );
	}

	/**
	 * Displays the settings page.
	 * https://developer.wordpress.org/plugins/settings/custom-settings-page/#creating-the-page
	 *
	 * @since 1.4.0
	 *
	 * @modified 1.5.0 Check if current user can manage_options.
	 */
	public function display_options_page() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'You do not have sufficient permissions to access this page.' );
		}

		ob_start();

		?>

		<div class="wrap">

			<h2><?php echo esc_html( get_admin_page_title() ); ?> &mdash; <small>v<?php echo $this->current_version; ?></small></h2>

			<form name="sfpp-form" method="post" action="options.php" enctype="multipart/form-data">

				<h2 class="nav-tab-wrapper hide-if-no-js">
					<a href="#tab_basic" class="nav-tab"><?php _e( 'Basic', Simple_Facebook_Language::translate() ); ?></a>
					<!-- <a href="#tab_extras" class="nav-tab"><?php //_e( 'Extras', SIMPLE_FACEBOOK_PAGE_I18N ); ?></a> -->
				</h2>

				<div id="sfpptabs">

					<?php settings_fields( $this->settings_group );   // settings group name. This should match the group name used in register_setting(). ?>

					<div class="sfpp-tab" id="tab_basic"><?php do_settings_sections( $this->settings_page ); ?></div>

					<div class="sfpp-tab" id="tab_extras"><?php //do_settings_sections( 'sfpp-extras' ); ?></div>

				</div>

				<?php submit_button(); ?>

			</form>

		</div>

		<?php

		echo ob_get_clean();
	}

}

class Simple_Facebook_Language {

	public static $instance;

	public static function init() {

		if ( is_null( self::$instance ) )
			self::$instance = new Simple_Facebook();
		return self::$instance;
	}

	public static function translate() {
		return 'sfpp-lang';
	}

}

Simple_Facebook::init();
Simple_Facebook_Page_Plugin::init();