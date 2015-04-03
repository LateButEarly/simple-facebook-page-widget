<?php
/*
Plugin Name: Simple Facebook Page Widget
Plugin URI: https://wordpress.org/plugins/simple-facebook-page-widget/
Description: Shows the Facebook Page feed in a sidebar widget and/or via shortcode.
Version: 1.1.0
Author: Dylan Ryan
Author URI: https://profiles.wordpress.org/irkanu
Text Domain: simple-facebook-twitter-widget
License: GPL v3

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
add_action( 'widgets_init', function () {
	register_widget( 'SFPP_Widget' );
} );

class SFPP_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'SFPP_Widget', // Base ID
			__( 'Simple Facebook Page Widget', SIMPLE_FACEBOOK_PAGE_I18N ), // Name
			array( 'description' => __( 'Easily display your Facebook Page feed.', SIMPLE_FACEBOOK_PAGE_I18N ), ) // Args
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

		/**
		 * Apply any styles before the widget.
		 */
		if ( array_key_exists( 'before_widget', $args ) ) {
			echo $args['before_widget'];
		}

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		$output = '';

		$output .= '<div class="fb-page" ';
		$output .= 'data-href="' . esc_attr( $instance['href'] ) . '" ';
		$output .= 'data-width="' . esc_attr( $instance['width'] ) . '" ';
		$output .= 'data-height="' . esc_attr( $instance['height'] ) . '" ';
		$output .= 'data-hide-cover="' . esc_attr( $instance['show_cover'] ) . '" ';
		$output .= 'data-show-facepile="' . esc_attr( $instance['show_facepile'] ) . '" ';
		$output .= 'data-show-posts="' . esc_attr( $instance['show_posts'] ) . '">';
		$output .= '</div>';

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
		 **/
		$show_posts = array( 'true' => 'Yes', 'false' => 'No' );

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
			'show_posts'    => '1'
		);

		return $defaults;

	}

}


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