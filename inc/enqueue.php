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

	// Переопределение токенов из Customizer (цвета бренда).
	$inline = intercarz_dynamic_tokens_css();
	if ( $inline ) {
		wp_add_inline_style( 'intercarz-tokens', $inline );
	}

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

	// Витрина главной — только на front-page.
	if ( is_front_page() ) {
		wp_enqueue_style(
			'intercarz-home',
			INTERCARZ_URI . '/assets/css/home.css',
			array( 'intercarz-theme' ),
			INTERCARZ_VERSION
		);
		wp_enqueue_script(
			'intercarz-home',
			INTERCARZ_URI . '/assets/js/home.js',
			array(),
			INTERCARZ_VERSION,
			true
		);
	}

	// Поиск в шапке = поиск по артикулу модуля CPMod.
	if ( get_theme_mod( 'intercarz_show_search', true ) ) {
		$base = function_exists( 'intercarz_module_base' ) ? intercarz_module_base() : '/carparts';

		// Стили выпадающего списка и строк результата берём из самого модуля
		// (чтобы вид совпадал с его собственным поиском).
		wp_enqueue_style( 'intercarz-cp-search', $base . '/add/search/default/styles.css', array( 'intercarz-tokens' ), null );
		wp_enqueue_style( 'intercarz-cp-search-rows', $base . '/templates/default/artnum_search/style.css', array( 'intercarz-cp-search' ), null );

		wp_enqueue_script(
			'intercarz-header-search',
			INTERCARZ_URI . '/assets/js/header-search.js',
			array(),
			INTERCARZ_VERSION,
			true
		);
		wp_localize_script(
			'intercarz-header-search',
			'intercarzSearch',
			array(
				'base'     => $base,
				'minLen'   => 2,
				'notFound' => __( 'Ничего не найдено', 'intercarz' ),
			)
		);
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

/**
 * CSS-переопределение дизайн-токенов значениями из Customizer.
 * Возвращает пустую строку, если всё на дефолтах.
 *
 * @return string
 */
function intercarz_dynamic_tokens_css() {
	$map = array(
		'--accent-500'  => array( 'intercarz_color_accent', '#F76707' ),
		'--accent-600'  => array( 'intercarz_color_accent_hover', '#E8590C' ),
		'--accent-100'  => array( 'intercarz_color_accent_light', '#FFF0E6' ),
		'--topbar-bg'   => array( 'intercarz_color_topbar', '#1A1D21' ),
	);

	$rules = '';
	foreach ( $map as $var => $cfg ) {
		list( $mod, $default ) = $cfg;
		$value = get_theme_mod( $mod, $default );
		$value = sanitize_hex_color( $value );
		if ( $value && strtolower( $value ) !== strtolower( $default ) ) {
			$rules .= $var . ':' . $value . ';';
		}
	}

	return $rules ? ':root{' . $rules . '}' : '';
}
