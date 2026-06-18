<?php
/**
 * Single page template.
 *
 * @package InterCarz
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--page' ); ?>>
		<header class="page-plate">
			<?php the_title( '<h1 class="page-plate__title">', '</h1>' ); ?>
		</header>
		<div class="entry__content">
			<?php
			the_content();
			wp_link_pages(
				array(
					'before' => '<nav class="page-links">' . esc_html__( 'Страницы:', 'intercarz' ),
					'after'  => '</nav>',
				)
			);
			?>
		</div>
	</article>
	<?php
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}
endwhile;

get_footer();
