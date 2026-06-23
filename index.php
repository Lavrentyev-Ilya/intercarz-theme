<?php
/**
 * Fallback template — обычные WP-страницы вне приложения CPMod.
 *
 * @package InterCarz
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main class="content-area" role="main">
	<?php if ( have_posts() ) : ?>

		<?php if ( is_home() && ! is_front_page() ) : ?>
			<header class="page-plate"><h1 class="page-plate__title"><?php single_post_title(); ?></h1></header>
		<?php endif; ?>

		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
				<header class="entry__header">
					<?php the_title( '<h2 class="entry__title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' ); ?>
				</header>
				<div class="entry__excerpt"><?php the_excerpt(); ?></div>
			</article>
			<?php
		endwhile;

		the_posts_pagination(
			array(
				'mid_size'  => 1,
				'prev_text' => __( '← Previous', 'intercarz' ),
				'next_text' => __( 'Next →', 'intercarz' ),
			)
		);

	else :
		?>
		<p><?php esc_html_e( 'Nothing found.', 'intercarz' ); ?></p>
		<?php
	endif;
	?>
</main>

<?php
get_footer();
