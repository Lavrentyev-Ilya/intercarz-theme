<?php
/**
 * Template helpers for header / footer chrome.
 *
 * @package InterCarz
 */

defined( 'ABSPATH' ) || exit;

/**
 * Site branding: custom logo, либо текстовый логотип InterCarz.
 */
function intercarz_site_branding() {
	echo '<div class="site-branding">';
	if ( has_custom_logo() ) {
		the_custom_logo();
	} else {
		printf(
			'<a class="site-branding__text" href="%1$s" rel="home">Inter<b>Carz</b></a>',
			esc_url( home_url( '/' ) )
		);
	}
	echo '</div>';
}

/**
 * Главное меню (location: primary). Если меню не назначено — запасной список
 * страниц, чтобы навигация не была пустой.
 */
function intercarz_primary_menu() {
	wp_nav_menu(
		array(
			'theme_location'  => 'primary',
			'container'       => 'nav',
			'container_class' => 'main-nav',
			'container_id'    => 'primary-menu',
			'menu_class'      => 'main-nav__list',
			'depth'           => 2,
			'fallback_cb'     => 'intercarz_primary_menu_fallback',
		)
	);
}

/**
 * Запасное меню: список страниц сайта (+ ссылка на главную).
 */
function intercarz_primary_menu_fallback() {
	wp_page_menu(
		array(
			'show_home'  => __( 'Главная', 'intercarz' ),
			'menu_class' => 'main-nav',
			'menu_id'    => 'primary-menu',
			'container'  => 'nav',
			'depth'      => 1,
		)
	);
}

/**
 * Компактная форма поиска в шапке.
 */
function intercarz_header_search() {
	?>
	<form role="search" method="get" class="header-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<input type="search" class="header-search__input" name="s"
			value="<?php echo esc_attr( get_search_query() ); ?>"
			placeholder="<?php esc_attr_e( 'Поиск по сайту…', 'intercarz' ); ?>"
			aria-label="<?php esc_attr_e( 'Поиск', 'intercarz' ); ?>">
		<button type="submit" class="header-search__btn" aria-label="<?php esc_attr_e( 'Найти', 'intercarz' ); ?>">
			<?php intercarz_icon( 'search' ); ?>
		</button>
	</form>
	<?php
}

/**
 * Левая часть верхней полосы: контакты (если заданы) либо короткие УТП.
 */
function intercarz_topbar_left() {
	$phone = get_theme_mod( 'intercarz_phone' );
	$hours = get_theme_mod( 'intercarz_hours' );

	if ( $phone || $hours ) {
		echo '<div class="topbar__contacts">';
		if ( $phone ) {
			printf(
				'<a class="topbar__item" href="tel:%1$s">%2$s<span>%3$s</span></a>',
				esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ),
				intercarz_get_icon( 'phone' ),
				esc_html( $phone )
			);
		}
		if ( $hours ) {
			printf(
				'<span class="topbar__item">%1$s<span>%2$s</span></span>',
				intercarz_get_icon( 'clock' ),
				esc_html( $hours )
			);
		}
		echo '</div>';
		return;
	}

	// Дефолтные УТП, пока контакты не заданы — чтобы полоса не пустовала.
	$usps = array(
		array( 'truck', __( 'Доставка по всей стране', 'intercarz' ) ),
		array( 'shield', __( 'Оригинал и проверенные аналоги', 'intercarz' ) ),
	);
	echo '<div class="topbar__usp">';
	foreach ( $usps as $usp ) {
		printf(
			'<span class="topbar__item">%1$s<span>%2$s</span></span>',
			intercarz_get_icon( $usp[0] ),
			esc_html( $usp[1] )
		);
	}
	echo '</div>';
}

/**
 * SVG-иконка из набора темы.
 *
 * @param string $name Имя иконки.
 */
function intercarz_icon( $name ) {
	$icons = array(
		'cart'     => '<path d="M2 3h2l2.4 12.3a2 2 0 0 0 2 1.7h7.7a2 2 0 0 0 2-1.6L21 7H6"/><circle cx="9" cy="20" r="1.5"/><circle cx="17" cy="20" r="1.5"/>',
		'user'     => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
		'phone'    => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2 4.2 2 2 0 0 1 4 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/>',
		'menu'     => '<line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>',
		'chevron'  => '<polyline points="6 9 12 15 18 9"/>',
		'search'   => '<circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
		'mail'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><polyline points="3 7 12 13 21 7"/>',
		'pin'      => '<path d="M21 10c0 6-9 12-9 12s-9-6-9-12a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>',
		'clock'    => '<circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15 14"/>',
		'truck'    => '<rect x="1" y="6" width="14" height="11" rx="1"/><path d="M15 9h4l3 3v5h-7z"/><circle cx="6" cy="18" r="2"/><circle cx="18" cy="18" r="2"/>',
		'shield'   => '<path d="M12 2l8 3v6c0 5-3.4 8.5-8 11-4.6-2.5-8-6-8-11V5z"/><polyline points="9 12 11 14 15 10"/>',
	);
	if ( empty( $icons[ $name ] ) ) {
		return;
	}
	printf(
		'<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">%s</svg>',
		$icons[ $name ] // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- статичный SVG.
	);
}

/**
 * Свитчер языка (Polylang). Тихо ничего не выводит без плагина.
 */
function intercarz_language_switcher() {
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return;
	}
	$langs = pll_the_languages( array( 'raw' => 1, 'hide_if_empty' => 1 ) );
	if ( empty( $langs ) || ! is_array( $langs ) ) {
		return;
	}
	echo '<div class="lang-switcher header-actions__item">';
	foreach ( $langs as $lang ) {
		printf(
			'<a class="lang-switcher__item%1$s" href="%2$s" lang="%3$s">%4$s</a>',
			! empty( $lang['current_lang'] ) ? ' is-active' : '',
			esc_url( $lang['url'] ),
			esc_attr( $lang['locale'] ),
			esc_html( strtoupper( $lang['slug'] ) )
		);
	}
	echo '</div>';
}

/**
 * Свитчер валюты (CURCY / WooCommerce Multi Currency, cookie wmc_current_currency).
 * Использует шорткод плагина; без плагина ничего не выводит.
 */
function intercarz_currency_switcher() {
	if ( ! shortcode_exists( 'woo_multi_currency' ) ) {
		return;
	}
	echo '<div class="wmc-currency-wrapper header-actions__item">';
	echo do_shortcode( '[woo_multi_currency]' );
	echo '</div>';
}

/**
 * Ссылка на аккаунт / вход (WooCommerce My Account, иначе wp-login).
 */
function intercarz_account_link() {
	if ( function_exists( 'wc_get_page_id' ) ) {
		$account_url = get_permalink( wc_get_page_id( 'myaccount' ) );
	} else {
		$account_url = wp_login_url();
	}
	if ( ! $account_url ) {
		return;
	}
	$label = is_user_logged_in() ? __( 'Кабинет', 'intercarz' ) : __( 'Вход', 'intercarz' );
	printf(
		'<a class="header-actions__item" href="%1$s">%2$s<span class="header-actions__item--label">%3$s</span></a>',
		esc_url( $account_url ),
		intercarz_get_icon( 'user' ),
		esc_html( $label )
	);
}

/**
 * Возвращает SVG-иконку строкой (для использования внутри printf).
 *
 * @param string $name Имя иконки.
 * @return string
 */
function intercarz_get_icon( $name ) {
	ob_start();
	intercarz_icon( $name );
	return ob_get_clean();
}

