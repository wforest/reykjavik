<?php
/**
 * Menu Class
 *
 * @package    Reykjavík
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.0.0
 *
 * Contents:
 *
 *  0) Init
 * 10) Register
 * 20) Primary
 * 30) Others
 */
class Reykjavik_Menu {





	/**
	 * 0) Init
	 */

		private static $instance;



		/**
		 * Constructor
		 *
		 * @since    1.0.0
		 * @version  1.0.0
		 */
		private function __construct() {

			// Processing

				// Setup

					self::register();

				// Hooks

					// Actions

						add_action( 'tha_header_top', __CLASS__ . '::primary', 20 );

						add_action( 'tha_header_top', __CLASS__ . '::secondary', 30 );

						add_action( 'tha_header_top', __CLASS__ . '::social', 40 );

						add_action( 'wmhook_reykjavik_site_info_after', __CLASS__ . '::social' );

						add_action( 'wp_update_nav_menu',   __CLASS__ . '::social_cache_flush' );
						add_action( 'customize_save_after', __CLASS__ . '::social_cache_flush' );
						add_action( 'wmhook_reykjavik_library_theme_upgrade', __CLASS__ . '::social_cache_flush' );

					// Filters

						add_filter( 'wmhook_reykjavik_social_links_icons', __CLASS__ . '::social_links_icons' );

						add_filter( 'walker_nav_menu_start_el', __CLASS__ . '::nav_menu_social_icons', 10, 4 );

						add_filter( 'walker_nav_menu_start_el', __CLASS__ . '::primary_item_description', 20, 4 );

						add_filter( 'walker_nav_menu_start_el', __CLASS__ . '::primary_item_expander', 30, 4 );

						add_filter( 'nav_menu_css_class', __CLASS__ . '::primary_item_classes', 10, 4 );

						add_filter( 'widget_nav_menu_args', __CLASS__ . '::social_widget', 10, 3 );

						add_filter( 'wp_nav_menu', __CLASS__ . '::mobile_menu_search', 20, 2 ); // See below for priority info

		} // /__construct



		/**
		 * Initialization (get instance)
		 *
		 * @since    1.0.0
		 * @version  1.0.0
		 */
		public static function init() {

			// Processing

				if ( null === self::$instance ) {
					self::$instance = new self;
				}


			// Output

				return self::$instance;

		} // /init





	/**
	 * 10) Register
	 */

		/**
		 * Register custom menus
		 *
		 * @since    1.0.0
		 * @version  1.0.0
		 */
		public static function register() {

			// Processing

				register_nav_menus( array(
						'primary'   => esc_html_x( 'Primary', 'Navigational menu location', 'reykjavik' ),
						'secondary' => esc_html_x( 'Secondary', 'Navigational menu location', 'reykjavik' ),
						'social'    => esc_html_x( 'Social Links', 'Navigational menu location', 'reykjavik' ),
					) );

		} // /register





	/**
	 * 20) Primary
	 */

		/**
		 * Primary navigation
		 *
		 * @since    1.0.0
		 * @version  1.0.0
		 */
		public static function primary() {

			// Output

				get_template_part( 'templates/parts/menu/menu', 'primary' );

		} // /primary



		/**
		 * Secondary navigation
		 *
		 * @since    1.0.0
		 * @version  1.0.0
		 */
		public static function secondary() {

			// Output

				get_template_part( 'templates/parts/menu/menu', 'secondary' );

		} // /secondary



		/**
		 * Menu item modification: item description
		 *
		 * Primary and Secondary menu only.
		 *
		 * @since    1.0.0
		 * @version  1.0.0
		 *
		 * @param  string $item_output Menu item output HTML (without closing `</li>`).
		 * @param  object $item        The current menu item.
		 * @param  int    $depth       Depth of menu item. Used for padding. Since WordPress 4.1.
		 * @param  array  $args        An array of wp_nav_menu() arguments.
		 */
		public static function primary_item_description( $item_output, $item, $depth, $args ) {

			// Processing

				if (
						in_array( $args->theme_location, array( 'primary', 'secondary' ) )
						&& trim( $item->description )
					) {

					$item_output = str_replace(
							$args->link_after . '</a>',
							'<span class="menu-item-description">' . trim( $item->description ) . '</span>' . $args->link_after . '</a>',
							$item_output
						);

				}


			// Output

				return $item_output;

		} // /primary_item_description



		/**
		 * Menu item modification: submenu expander
		 *
		 * Primary menu only.
		 *
		 * @since    1.0.0
		 * @version  1.0.0
		 *
		 * @param  string $item_output Menu item output HTML (without closing `</li>`).
		 * @param  object $item        The current menu item.
		 * @param  int    $depth       Depth of menu item. Used for padding. Since WordPress 4.1.
		 * @param  array  $args        An array of wp_nav_menu() arguments.
		 */
		public static function primary_item_expander( $item_output, $item, $depth, $args ) {

			// Processing

				if (
						'primary' === $args->theme_location
						&& in_array( 'menu-item-has-children', (array) $item->classes )
					) {

					$item_output = str_replace(
							$args->link_after . '</a>',
							$args->link_after . ' <span class="expander" aria-hidden="true"></span></a>', // Accessibility: on focus, no screen reader text required
							$item_output
						);

				}


			// Output

				return $item_output;

		} // /primary_item_expander



		/**
		 * Navigation item classes
		 *
		 * Applies `has-description` classes on menu items.
		 *
		 * @link  http://a11yproject.com/patterns/
		 *
		 * @since    1.0.0
		 * @version  1.0.0
		 *
		 * @param  array  $classes The CSS classes that are applied to the menu item's `<li>` element.
		 * @param  object $item    The current menu item.
		 * @param  array  $args    An array of wp_nav_menu() arguments.
		 * @param  int    $depth   Depth of menu item. Used for padding. Since WordPress 4.1.
		 */
		public static function primary_item_classes( $classes, $item, $args, $depth = 0 ) {

			// Processing

				// Primary menu

					if ( 'primary' === $args->theme_location ) {

						// Has menu item description?

							if ( trim( $item->post_content ) && $item->menu_item_parent ) {
								$classes[] = 'has-description';
							}

					}


			// Output

				return $classes;

		} // /primary_item_classes





	/**
	 * 30) Others
	 */

		/**
		 * Navigation fallback text
		 *
		 * @since    1.0.0
		 * @version  1.0.0
		 */
		public static function menu_fallback() {

			// Output

				return esc_html__( 'Please, set up the menu in Appearance &raquo; Menus.', 'reykjavik' );

		} // /menu_fallback



		/**
		 * Social
		 */

			/**
			 * Social links
			 *
			 * @since    1.0.0
			 * @version  1.0.0
			 */
			public static function social() {

				// Output

					get_template_part( 'templates/parts/menu/menu', 'social' );

			} // /social



			/**
			 * Social links supported icons
			 *
			 * @since    1.0.0
			 * @version  1.0.0
			 */
			public static function social_links_icons() {

				// Output

					return array(
							'behance.net'       => 'behance',
							'bitbucket.org'     => 'bitbucket',
							'codepen.io'        => 'codepen',
							'deviantart.com'    => 'deviantart',
							'digg.com'          => 'digg',
							'dribbble.com'      => 'dribbble',
							'dropbox.com'       => 'dropbox',
							'facebook.com'      => 'facebook',
							'flickr.com'        => 'flickr',
							'foursquare.com'    => 'foursquare',
							'plus.google.com'   => 'google-plus',
							'github.com'        => 'github',
							'instagram.com'     => 'instagram',
							'linkedin.com'      => 'linkedin',
							'mailto:'           => 'envelope',
							'medium.com'        => 'medium',
							'paypal.com'        => 'paypal',
							'pinterest.com'     => 'pinterest',
							'getpocket.com'     => 'get-pocket',
							'reddit.com'        => 'reddit',
							'skype.com'         => 'skype',
							'skype:'            => 'skype',
							'slack.com'         => 'slack',
							'slideshare.net'    => 'slideshare',
							'snapchat.com'      => 'snapchat',
							'soundcloud.com'    => 'soundcloud',
							'spotify.com'       => 'spotify',
							'stackoverflow.com' => 'stack-overflow',
							'stumbleupon.com'   => 'stumbleupon',
							'trello.com'        => 'trello',
							'tripadvisor.'      => 'tripadvisor',
							'tumblr.com'        => 'tumblr',
							'twitch.tv'         => 'twitch',
							'twitter.com'       => 'twitter',
							'vimeo.com'         => 'vimeo',
							'vine.co'           => 'vine',
							'vk.com'            => 'vk',
							'wordpress.org'     => 'wordpress',
							'wordpress.com'     => 'wordpress',
							'yelp.com'          => 'yelp',
							'youtube.com'       => 'youtube',
						);

			} // /social_links_icons



			/**
			 * Display SVG icons in social links menu
			 *
			 * @uses  `wmhook_reykjavik_social_links_icons` global hook
			 *
			 * @since    1.0.0
			 * @version  1.0.0
			 *
			 * @param  string  $item_output The menu item output.
			 * @param  WP_Post $item        Menu item object.
			 * @param  int     $depth       Depth of the menu.
			 * @param  array   $args        wp_nav_menu() arguments.
			 */
			public static function nav_menu_social_icons( $item_output, $item, $depth, $args ) {

				// Helper variables

					$locations = get_nav_menu_locations();


				// Processing

					if (
							isset( $locations['social'] )
							&& isset( $args->menu->term_id )
							&& absint( $locations['social'] ) === absint( $args->menu->term_id )
						) {

						$social_icons = (array) apply_filters( 'wmhook_reykjavik_social_links_icons', array() );
						$social_icon  = 'chain';

						foreach ( $social_icons as $url => $icon ) {
							if ( false !== strpos( $item_output, $url ) ) {
								$social_icon = $icon;
								break;
							}
						}

						$item_output = str_replace(
								$args->link_after,
								'</span>' . Reykjavik_SVG::get( array(
									'icon' => esc_attr( $social_icon ),
									'base' => 'social-icon',
								) ),
								$item_output
							);

					}


				// Output

					return $item_output;

			} // /nav_menu_social_icons



			/**
			 * Display social links in Menu widget
			 *
			 * @since    1.0.0
			 * @version  1.0.0
			 *
			 * @param  array  $nav_menu_args Array of parameters for `wp_nav_menu()` function.
			 * @param  string $nav_menu      Menu slug assigned in the widget.
			 * @param  array  $args          Widget parameters.
			 */
			public static function social_widget( $nav_menu_args, $nav_menu, $args ) {

				// Helper variables

					$nav_menu_obj = wp_get_nav_menu_object( $nav_menu );
					$locations    = get_nav_menu_locations();


				// Requirements check

					if (
							! isset( $locations['social'] )
							|| ! $locations['social']
							|| absint( $locations['social'] ) !== absint( $nav_menu_obj->term_id )
						) {
						return $nav_menu_args;
					}


				// Processing

					$nav_menu_args['container_class'] = 'social-links';
					$nav_menu_args['menu_class']      = 'social-links-items';
					$nav_menu_args['depth']           = 1;
					$nav_menu_args['link_before']     = '<span class="screen-reader-text">';
					$nav_menu_args['link_after']      = '</span>';
					$nav_menu_args['items_wrap']      = '<ul id="%1$s" class="%2$s">%3$s</ul>';


				// Output

					return $nav_menu_args;

			} // /social_widget



			/**
			 * Flush social menu cache
			 *
			 * @since    1.0.0
			 * @version  1.0.0
			 */
			public static function social_cache_flush() {

				// Processing

					delete_transient( 'reykjavik_social_links' );

			} // /social_cache_flush



		/**
		 * Mobile
		 */

			/**
			 * Mobile menu search form
			 *
			 * Adding mobile menu search form on top of the primary menu.
			 *
			 * Note:
			 * Not sure why, but has to use higher priority than 10 when hooking
			 * this method, as otherwise in some weird cases (wasn't able
			 * to determine the cause) customizer displays the menu twice.
			 *
			 * @since    1.0.0
			 * @version  1.0.0
			 *
			 * @param  string $nav_menu
			 * @param  object $args
			 */
			public static function mobile_menu_search( $nav_menu, $args ) {

				// Requirements check

					if (
							'primary' !== $args->theme_location
							|| get_theme_mod( 'navigation_mobile_disable', false )
						) {
						return $nav_menu;
					}


				// Output

					return '<div class="mobile-search-form">' . get_search_form( false ) . '</div>' . $nav_menu;

			} // /mobile_menu_search





} // /Reykjavik_Menu

add_action( 'after_setup_theme', 'Reykjavik_Menu::init' );