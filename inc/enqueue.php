<?php
/**
 * Styles and scripts.
 *
 * @package InterCarz
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'intercarz_enqueue_assets' );
/**
 * Enqueue front-end styles and scripts.
 */
function intercarz_enqueue_assets() {
	// Шрифт Inter (§4). При желании заменить на self-hosted в assets/fonts.
	wp_enqueue_style(
		'intercarz-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
		array(),
		null
	);

	// Дизайн-токены — грузим первыми, остальной CSS зависит от них.
	wp_enqueue_style(
		'intercarz-tokens',
		INTERCARZ_URI . '/assets/css/tokens.css',
		array(),
		INTERCARZ_VERSION
	);

	wp_enqueue_style(
		'intercarz-theme',
		INTERCARZ_URI . '/assets/css/theme.css',
		array( 'intercarz-tokens' ),
		INTERCARZ_VERSION
	);

	// style.css (для соответствия стандарту темы; реальных стилей не содержит).
	wp_enqueue_style(
		'intercarz-style',
		get_stylesheet_uri(),
		array( 'intercarz-theme' ),
		INTERCARZ_VERSION
	);

	$deps = array();
	// wc-cart-fragments обновляет счётчик корзины в шапке после add-to-cart.
	if ( class_exists( 'WooCommerce' ) ) {
		$deps[] = 'wc-cart-fragments';
	}

	wp_enqueue_script(
		'intercarz-main',
		INTERCARZ_URI . '/assets/js/main.js',
		$deps,
		INTERCARZ_VERSION,
		true
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
