<?php
/**
 * WooCommerce product more link HTML
 *
 * @package    Reykjavík
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.0.0
 */





// Requirements check

	if (
			! is_search()
			|| is_post_type_archive( 'product' )
		) {
		return;
	}


?>

<div class="link-more">
	<a href="<?php echo esc_url( get_permalink( get_the_ID() ) ); ?>" class="button">
		<?php

		printf(
				esc_html_x( 'View product%s&hellip;', '%s: Product name.', 'reykjavik' ),
				the_title( '<span class="screen-reader-text"> &ldquo;', '&rdquo;</span>', false )
			);

		?>
	</a>
</div>