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
	<header class="page-plate"><h1 class="page-plate__title"><?php esc_html_e( 'Страница не найдена', 'intercarz' ); ?></h1></header>
	<p><?php esc_html_e( 'Похоже, по этому адресу ничего нет. Воспользуйтесь поиском или вернитесь на главную.', 'intercarz' ); ?></p>
	<?php get_search_form(); ?>
	<p><a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'На главную', 'intercarz' ); ?></a></p>
</section>
<?php
get_footer();
