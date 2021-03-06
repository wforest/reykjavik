<?php
/**
 * Primary menu template
 *
 * Accessibility markup applied (ARIA).
 *
 * @link  http://a11yproject.com/patterns/
 *
 * @package    Reykjavik
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.0.0
 */





// Helper variables

	$is_mobile_nav_enabled = get_theme_mod( 'navigation_mobile', true );


?>

<nav id="site-navigation" class="main-navigation" role="navigation" aria-labelledby="site-navigation-label">

	<h2 class="screen-reader-text" id="site-navigation-label"><?php esc_html_e( 'Primary Menu', 'reykjavik' ); ?></h2>

	<?php if ( $is_mobile_nav_enabled ) : ?>
	<button role="button" id="menu-toggle" class="menu-toggle" aria-controls="menu-primary" aria-expanded="false"><?php echo esc_html_x( 'Menu', 'Mobile navigation toggle button title.', 'reykjavik' ); ?></button>

	<?php endif; ?>
	<div id="site-navigation-container" class="main-navigation-container">
		<?php wp_nav_menu( Reykjavik_Menu::primary_menu_args( $is_mobile_nav_enabled ) ); ?>
	</div>

</nav>
