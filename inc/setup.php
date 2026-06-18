<?php
/**
 * Theme setup: supports, menus, image sizes.
 *
 * @package InterCarz
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', 'intercarz_setup' );
/**
 * Register theme supports and navigation menus.
 */
function intercarz_setup() {
	load_theme_textdomain( 'intercarz', INTERCARZ_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'responsive-embeds' );

	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 36,
			'width'       => 180,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	// WooCommerce — тема-оболочка предоставляет корзину и checkout приложению.
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus(
		array(
			'primary'      => __( 'Главное меню (header)', 'intercarz' ),
			'footer'       => __( 'Меню в подвале', 'intercarz' ),
			'footer_legal' => __( 'Правовые ссылки (низ подвала)', 'intercarz' ),
		)
	);
}

add_action( 'widgets_init', 'intercarz_widgets_init' );
/**
 * Footer widget areas.
 */
function intercarz_widgets_init() {
	for ( $i = 1; $i <= 3; $i++ ) {
		register_sidebar(
			array(
				/* translators: %d: footer column number. */
				'name'          => sprintf( __( 'Подвал — колонка %d', 'intercarz' ), $i ),
				'id'            => 'footer-' . $i,
				'before_widget' => '<div class="site-footer__col %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3>',
				'after_title'   => '</h3>',
			)
		);
	}
}

/**
 * Контекст приложения CPMod.
 *
 * CPMod подключает WordPress через wp-load.php и оставляет в области видимости
 * глобал $CPMod, после чего сам вызывает get_header()/get_footer(). По этому
 * признаку отличаем «страницу приложения» (полная ширина, grid-область
 * breadcrumbs) от обычной WP/WooCommerce страницы (контейнер 1280px).
 *
 * @return bool
 */
function intercarz_is_app_context() {
	$is_app = isset( $GLOBALS['CPMod'] )
		|| defined( 'CM_INDEX_INCLUDED' )
		|| defined( 'CM_ADD_TO_CART' )
		|| defined( 'CM_DIR' );

	/**
	 * Позволяет переопределить детект контекста приложения.
	 *
	 * @param bool $is_app
	 */
	return (bool) apply_filters( 'intercarz_is_app_context', $is_app );
}
