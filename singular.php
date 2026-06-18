<?php
/**
 * Single post template.
 *
 * @package InterCarz
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--single' ); ?>>
		<header class="page-plate">
			<?php the_title( '<h1 class="page-plate__title">', '</h1>' ); ?>
		</header>
		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="entry__thumb"><?php the_post_thumbnail( 'large' ); ?></figure>
		<?php endif; ?>
		<div class="entry__content"><?php the_content(); ?></div>
	</article>
	<?php
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}
endwhile;

get_footer();
