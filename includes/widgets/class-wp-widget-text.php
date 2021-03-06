<?php
/**
 * Widget: WordPress Text
 *
 * Altering native WordPress Text widget.
 *
 * @package    Reykjavik
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.0.0
 *
 * Contents:
 *
 *  1) Requirements check
 * 10) Widget functionality
 */





/**
 * 1) Requirements check
 */

	if (
			! class_exists( 'WP_Widget' )
			|| ! class_exists( 'WP_Widget_Text' )
		) {
		return;
	}





/**
 * 10) Widget functionality
 */

	/**
	 * Widget class
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 *
	 * Contents:
	 *
	 *  0) Init
	 * 10) Output
	 * 20) Options
	 * 30) Admin
	 * 40) Icon fallback
	 */
	class Reykjavik_WP_Widget_Text extends WP_Widget_Text {





		/**
		 * 0) Init
		 */

			/**
			 * Constructor
			 *
			 * @since    1.0.0
			 * @version  1.0.0
			 */
			public function __construct() {

				// Processing

					parent::__construct();

					// Hooks

						// Actions

							add_action( 'admin_print_scripts-widgets.php', array( $this, 'enqueue' ) );

							add_action( 'wp_enqueue_scripts', array( $this, 'assets_beaver_builder' ) );

							add_action( 'wp_head', array( $this, 'style_icon_fallback' ), 5 );

			} // /__construct





		/**
		 * 10) Output
		 */

			/**
			 * Outputs the content for the current widget instance
			 *
			 * @since    1.0.0
			 * @version  1.0.0
			 *
			 * @param  array $args      Display arguments including 'before_title', 'after_title', 'before_widget', and 'after_widget'.
			 * @param  array $instance  Settings for the current Text widget instance.
			 */
			public function widget( $args, $instance ) {

				// Helper variables

					$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

					$widget_text  = ( ! empty( $instance['text'] ) ) ? ( $instance['text'] ) : ( '' );
					$widget_media = array_filter( array(
							'icon'  => ( isset( $instance['icon'] ) ) ? ( trim( $instance['icon'] ) ) : ( '' ),
							'image' => ( isset( $instance['image'] ) ) ? ( $instance['image'] ) : ( 0 ),
						) );

					$is_visual_text_widget = ( ! empty( $instance['visual'] ) && ! empty( $instance['filter'] ) );


				// Processing

					// From WP_Widget_Text

						// In 4.8.0 only, visual Text widgets get filter=content, without visual prop; upgrade instance props just-in-time.
						if ( ! $is_visual_text_widget ) {
							$is_visual_text_widget = ( isset( $instance['filter'] ) && 'content' === $instance['filter'] );
						}
						if ( $is_visual_text_widget ) {
							$instance['filter'] = true;
							$instance['visual'] = true;
						}

						/*
						 * Just-in-time temporarily upgrade Visual Text widget shortcode handling
						 * (with support added by plugin) from the widget_text filter to
						 * widget_text_content:11 to prevent wpautop from corrupting HTML output
						 * added by the shortcode.
						 */
						$widget_text_do_shortcode_priority = has_filter( 'widget_text', 'do_shortcode' );
						$should_upgrade_shortcode_handling = ( $is_visual_text_widget && false !== $widget_text_do_shortcode_priority );
						if ( $should_upgrade_shortcode_handling ) {
							remove_filter( 'widget_text', 'do_shortcode', $widget_text_do_shortcode_priority );
							add_filter( 'widget_text_content', 'do_shortcode', 11 );
						}

						$text = apply_filters( 'widget_text', $widget_text, $instance, $this );



					// Custom widget enhancements output

						if ( ! empty( $widget_media ) ) {

							$widget_text = '';

							// Output image

								if ( isset( $widget_media['image'] ) ) {
									$widget_text .= '<div class="widget-text-media widget-text-media-image">';

									if ( is_numeric( $widget_media['image'] ) ) {
										$widget_text .= wp_get_attachment_image( absint( $instance['image'] ), 'medium' );
									} else {
										$widget_text .= '<img src="' . esc_url( $instance['image'] ) . '" alt="' . esc_attr( $title ) . '" />';
									}

									$widget_text .= '</div>';
								}

							// Output icon

								if ( isset( $widget_media['icon'] ) ) {
									$widget_text .= '<div class="widget-text-media widget-text-media-icon h3">'; // Heading class is to inherit heading color
									$widget_text .= '<span class="widget-symbol ' . esc_attr( $widget_media['icon'] ) . '" aria-hidden="true"></span>';
									$widget_text .= '</div>';
								}

							$widget_text .= '<div class="widget-text-content">';

							if ( ! empty( $title ) ) {
								$widget_text .= $args['before_title'];
								$widget_text .= $title;
								$widget_text .= $args['after_title'];

								$title = '';
							}

							$widget_text .= $text;
							$widget_text .= '</div>';

							$text = $widget_text;

							$instance['filter'] = true;
						}



					// From WP_Widget_Text

						if ( $is_visual_text_widget ) {
							$text = apply_filters( 'widget_text_content', $text, $instance, $this );
						} elseif ( ! empty( $instance['filter'] ) ) {
							$text = wpautop( $text );
						}

						// Undo temporary upgrade of the plugin-supplied shortcode handling.
						if ( $should_upgrade_shortcode_handling ) {
							remove_filter( 'widget_text_content', 'do_shortcode', 11 );
							add_filter( 'widget_text', 'do_shortcode', $widget_text_do_shortcode_priority );
						}


				// Output

					echo $args['before_widget'];

					if ( ! empty( $title ) ) {
						echo $args['before_title'] . $title . $args['after_title'];
					}

					?>

					<div class="textwidget"><?php echo $text; ?></div>

					<?php

					echo $args['after_widget'];

			} // /widget





		/**
		 * 20) Options
		 */

			/**
			 * Outputs the settings form
			 *
			 * @since    1.0.0
			 * @version  1.0.0
			 *
			 * @param  array $instance  Current settings.
			 */
			public function form( $instance ) {

				// Processing

					parent::form( $instance );


				// Output

					$this->field_icon( $instance );
					$this->field_image( $instance );

			} // /form



			/**
			 * Option field: Icon
			 *
			 * @since    1.0.0
			 * @version  1.0.0
			 *
			 * @param  array $instance  Current settings.
			 */
			public function field_icon( $instance = array() ) {

				// Helper variables

					if ( ! isset( $instance['icon'] ) ) {
						$instance['icon'] = '';
					}


				// Output

					?>

					<p class="text-widget-media-icon">
						<label for="<?php echo $this->get_field_id( 'icon' ); ?>">
							<strong><?php esc_html_e( 'Set icon CSS class:', 'reykjavik' ); ?></strong><br>
							<span class="description" style="display: inline-block; padding: 0 0 .38em">
								<em>
									<?php
									printf(
										esc_html__( 'For displaying icons on your website use a plugin, such as %1$s or %2$s.', 'reykjavik' ),
										'<a href="https://wordpress.org/plugins/better-font-awesome/">' . esc_html_x( 'Better Font Awesome', 'Plugin name.', 'reykjavik' ) . '</a>',
										'<a href="https://wordpress.org/plugins/ionicons-official/">' . esc_html_x( 'Ionicons Official', 'Plugin name.', 'reykjavik' ) . '</a>'
									);
									?>
								</em>
							</span>
						</label>
						<input type="text" class="widefat text-widget-media-icon-class" id="<?php echo $this->get_field_id( 'icon' ); ?>" name="<?php echo $this->get_field_name( 'icon' ); ?>" value="<?php echo esc_attr( $instance['icon'] ); ?>" />
					</p>

					<?php

			} // /field_icon



			/**
			 * Option field: Image
			 *
			 * @since    1.0.0
			 * @version  1.0.0
			 *
			 * @param  array $instance  Current settings.
			 */
			public function field_image( $instance = array() ) {

				// Helper variables

					if ( ! isset( $instance['image'] ) ) {
						$instance['image'] = 0;
					}


				// Output

					?>

					<p class="text-widget-media-image">
						<label for="<?php echo $this->get_field_id( 'image' ); ?>">
							<strong><?php esc_html_e( 'Set image:', 'reykjavik' ); ?></strong><br>
							<span class="description" style="display: inline-block; padding: 0 0 .38em">
								<em>
									<?php esc_html_e( 'Choose a featured image for this widget.', 'reykjavik' ); ?>
								</em>
							</span>
						</label>
						<br>
						<button class="button button-hero text-widget-media-image-select"><?php esc_html_e( 'Select image', 'reykjavik' ); ?></button>
						<input type="hidden" id="<?php echo $this->get_field_id( 'image' ); ?>" name="<?php echo $this->get_field_name( 'image' ); ?>" value="<?php echo esc_attr( $instance['image'] ); ?>" />
						<span class="text-widget-media-image-preview"<?php if ( empty( $instance['image'] ) ) { echo ' style="display: none;"'; } ?>>
							<img src="<?php

								if ( is_numeric( $instance['image'] ) ) {
									$image_url = wp_get_attachment_image_src( absint( $instance['image'] ) );
									if ( $image_url ) {
										echo esc_url( $image_url[0] );
									}
								} elseif ( $instance['image'] ) {
									echo esc_url( $instance['image'] );
								}

								?>" alt="" />
							<button class="button text-widget-media-image-remove">
								<span class="screen-reader-text"><?php esc_html_e( 'Remove image', 'reykjavik' ); ?></span>
							</button>
						</span>
					</p>

					<?php

			} // /field_image



			/**
			 * Handles updating settings for the current widget instance
			 *
			 * @since    1.0.0
			 * @version  1.0.0
			 *
			 * @param  array $new_instance  New settings for this instance as input by the user via WP_Widget::form().
			 * @param  array $old_instance  Old settings for this instance.
			 */
			public function update( $new_instance, $old_instance ) {

				// Helper variables

					$instance = parent::update( $new_instance, $old_instance );


				// Processing

					$instance['icon']  = esc_attr( $new_instance['icon'] );
					$instance['image'] = ( is_numeric( $new_instance['image'] ) ) ? ( absint( $new_instance['image'] ) ) : ( esc_url( $new_instance['image'] ) );


				// Output

					return $instance;

			} // /update





		/**
		 * 30) Admin
		 */

			/**
			 * Enqueue assets
			 *
			 * @since    1.0.0
			 * @version  1.0.0
			 */
			public function enqueue() {

				// Processing

					// Styles

						wp_enqueue_style(
								'reykjavik-widget-text',
								get_theme_file_uri( 'assets/css/options-widget-text.css' ),
								array(),
								esc_attr( REYKJAVIK_THEME_VERSION )
							);

					// Scripts

						wp_enqueue_media();

						wp_enqueue_script(
								'reykjavik-widget-text',
								get_theme_file_uri( 'assets/js/scripts-widget-text.js' ),
								array( 'media-upload' ),
								esc_attr( REYKJAVIK_THEME_VERSION ),
								true
							);

			} // /enqueue



			/**
			 * Loading assets in Beaver Builder
			 *
			 * @since    1.0.0
			 * @version  1.0.0
			 */
			public function assets_beaver_builder() {

				// Requirements check

					if ( ! is_callable( 'FLBuilderModel::is_builder_active' ) || ! FLBuilderModel::is_builder_active() ) {
						return;
					}


				// Processing

					$this->enqueue();

			} // /assets_beaver_builder





		/**
		 * 40) Icon fallback
		 */

			/**
			 * Icon fallback styles
			 *
			 * For cases when no icons font is loaded.
			 *
			 * IMPORTANT:
			 * This has to be loaded early enough, before the icons font
			 * stylesheet is enqueued (with any plugin)!
			 *
			 * @since    1.0.0
			 * @version  1.0.0
			 */
			public function style_icon_fallback() {

				// Output

					echo '<style id="reykjavik-text-widget-icon-fallback"> ' .
					     '.widget-symbol::before { content: "?"; font-family: inherit; } ' .
					     '</style>';

			} // /style_icon_fallback





	} // /Reykjavik_WP_Widget_Text



	/**
	 * Widget registration
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	function reykjavik_register_widget_text() {

		// Processing

			unregister_widget( 'WP_Widget_Text' );

			register_widget( 'Reykjavik_WP_Widget_Text' );

	} // /reykjavik_register_widget_text

	add_action( 'widgets_init', 'reykjavik_register_widget_text' );
