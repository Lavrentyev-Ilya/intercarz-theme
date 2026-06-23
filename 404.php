<?php
/**
 * 404 template.
 *
 * @package InterCarz
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="error-404">
	<header class="page-plate"><h1 class="page-plate__title"><?php esc_html_e( 'Page not found', 'intercarz' ); ?></h1></header>
	<p><?php esc_html_e( 'It looks like nothing is here. Try a search or go back to the homepage.', 'intercarz' ); ?></p>
	<?php get_search_form(); ?>
	<p><a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'intercarz' ); ?></a></p>
</section>
<?php
get_footer();
