<?php
/**
 * Class Simple_Facebook_Page_Plugin_Widget
 *
 * @since 1.0.0
 */
class Simple_Facebook_Page_Plugin_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'Simple_Facebook_Page_Plugin_Widget',
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
		$output .= '<!-- Begin Facebook Page Widget - https://wordpress.org/plugins/simple-facebook-twitter-widget/ -->';

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

		//end wrapper
		$output .= '</div>';

		//end comment
		$output .= '<!-- End Facebook Page Widget -->';

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
				<?php foreach ( $align as $val ): ?>
					<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $instance['align'], $val ); ?>><?php echo esc_html( $val ); ?></option>
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