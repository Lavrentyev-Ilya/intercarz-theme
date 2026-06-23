<?php
/**
 * Лёгкая мультиязычность без плагина.
 *
 * Язык хранится в cookie `intercarz_lang` (en/fr). Тема по cookie:
 *  - подменяет локаль WordPress (хедер/футер/строки темы, WooCommerce из их
 *    собственных переводов);
 *  - выводит переключатель в шапке (JS ставит cookie и перезагружает страницу —
 *    остаёмся на той же странице, без «броска на главную»).
 *
 * Модуль CPMod синхронизируется по той же cookie через мост в
 * /carparts/tocms/WordPress.WC.php (см. README).
 *
 * @package InterCarz
 */

defined( 'ABSPATH' ) || exit;

/**
 * Доступные языки: код (2 буквы) => название.
 *
 * @return array
 */
function intercarz_languages() {
	return apply_filters(
		'intercarz_languages',
		array(
			'en' => 'English',
			'fr' => 'Français',
		)
	);
}

/**
 * Соответствие код языка → локаль WordPress.
 *
 * @return array
 */
function intercarz_locale_map() {
	return apply_filters(
		'intercarz_locale_map',
		array(
			'en' => 'en_US',
			'fr' => 'fr_FR',
		)
	);
}

/**
 * Язык по умолчанию (когда cookie ещё нет).
 *
 * @return string
 */
function intercarz_default_lang() {
	$langs   = intercarz_languages();
	$default = apply_filters( 'intercarz_default_lang', 'en' );
	return isset( $langs[ $default ] ) ? $default : (string) key( $langs );
}

/**
 * Текущий язык из cookie (или язык по умолчанию).
 *
 * @return string
 */
function intercarz_current_lang() {
	$langs = intercarz_languages();
	if ( isset( $_COOKIE['intercarz_lang'] ) ) {
		$code = substr( preg_replace( '/[^a-z]/', '', strtolower( (string) $_COOKIE['intercarz_lang'] ) ), 0, 2 );
		if ( isset( $langs[ $code ] ) ) {
			return $code;
		}
	}
	return intercarz_default_lang();
}

add_filter( 'locale', 'intercarz_filter_locale' );
/**
 * Подменяет локаль на фронтенде согласно cookie.
 *
 * @param string $locale
 * @return string
 */
function intercarz_filter_locale( $locale ) {
	// В админке оставляем язык администратора.
	if ( is_admin() && ! ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) ) {
		return $locale;
	}
	$map  = intercarz_locale_map();
	$lang = intercarz_current_lang();
	return isset( $map[ $lang ] ) ? $map[ $lang ] : $locale;
}

/**
 * Переключатель языка в шапке (cookie-based).
 */
function intercarz_language_switcher() {
	$langs = intercarz_languages();
	if ( count( $langs ) < 2 ) {
		return;
	}
	$current = intercarz_current_lang();
	echo '<div class="lang-switcher header-actions__item" data-lang-switcher>';
	foreach ( $langs as $code => $name ) {
		printf(
			'<a class="lang-switcher__item%1$s" href="#" data-set-lang="%2$s" hreflang="%2$s" aria-label="%4$s">%3$s</a>',
			$code === $current ? ' is-active' : '',
			esc_attr( $code ),
			esc_html( strtoupper( $code ) ),
			esc_attr( $name )
		);
	}
	echo '</div>';
}
