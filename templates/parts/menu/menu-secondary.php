<?php
/**
 * Secondary menu template
 *
 * This is a simple, one-level secondary site navigation in header.
 *
 * @package    Reykjavik
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.0.0
 */





// Requirements check

	if ( ! has_nav_menu( 'secondary' ) ) {
		return;
	}


?>

<nav id="secondary-navigation" class="secondary-navigation" role="navigation" aria-labelledby="secondary-navigation-label">

	<h2 class="screen-reader-text" id="secondary-navigation-label"><?php esc_html_e( 'Secondary Menu', 'reykjavik' ); ?></h2>

	<?php

	wp_nav_menu( array(
			'theme_location' => 'secondary',
			'container'      => 'false',
			'depth'          => 1,
			'fallback_cb'    => false,
		) );

	?>

</nav>
