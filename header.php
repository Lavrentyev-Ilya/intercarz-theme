<?php
/**
 * Header — открывает документ, шапку и обёртку контента.
 *
 * Вызывается как обычными WP-шаблонами, так и приложением CPMod
 * (через get_header()). Обёртка .site-content закрывается в footer.php.
 *
 * @package InterCarz
 */

defined( 'ABSPATH' ) || exit;

$intercarz_is_app = function_exists( 'intercarz_is_app_context' ) && intercarz_is_app_context();
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class( $intercarz_is_app ? 'cp-app-context' : 'cp-cms-context' ); ?>>
<?php wp_body_open(); ?>

<a class="screen-reader-text skip-link" href="#content"><?php esc_html_e( 'Перейти к содержимому', 'intercarz' ); ?></a>

<header class="site-header" role="banner">

	<?php if ( get_theme_mod( 'intercarz_show_topbar', true ) ) : ?>
	<div class="site-header__top">
		<div class="container site-header__top-inner">
			<?php intercarz_topbar_left(); ?>
			<div class="topbar__right">
				<?php intercarz_currency_switcher(); ?>
				<?php intercarz_language_switcher(); ?>
				<?php intercarz_account_link(); ?>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<div class="site-header__main">
		<div class="container site-header__bar">

			<button class="nav-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="<?php esc_attr_e( 'Меню', 'intercarz' ); ?>">
				<?php intercarz_icon( 'menu' ); ?>
			</button>

			<?php intercarz_site_branding(); ?>

			<?php intercarz_primary_menu(); ?>

			<div class="header-actions">
				<?php if ( get_theme_mod( 'intercarz_show_search', true ) ) { intercarz_header_search(); } ?>
				<?php if ( function_exists( 'intercarz_cart_link' ) ) { intercarz_cart_link(); } ?>
			</div>

		</div>
	</div>
</header>

<div class="overlay" data-overlay></div>

<div id="content" class="site-content <?php echo $intercarz_is_app ? 'is-app' : 'is-cms'; ?>">
