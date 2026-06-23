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
 * Базовый путь модуля каталога CPMod (для поиска по артикулу).
 *
 * @return string Без завершающего слэша, напр. "/carparts".
 */
function intercarz_module_base() {
	$base = get_theme_mod( 'intercarz_module_base', '/carparts' );
	$base = '/' . trim( (string) $base, '/' );
	return apply_filters( 'intercarz_module_base', $base );
}

/**
 * Поиск в шапке = поиск по артикулу модуля CPMod.
 *
 * На Enter/клик JS (header-search.js) делает AJAX-запрос на {база}/search/{q}/
 * и показывает выпадающий список. Без JS — обычная отправка формы на страницу
 * результатов (модуль ловит параметр ArtSearch).
 */
function intercarz_header_search() {
	$base        = intercarz_module_base();
	$placeholder = get_theme_mod( 'intercarz_search_placeholder', __( 'Номер, артикул, OE…', 'intercarz' ) );
	?>
	<form role="search" method="get" class="header-search" action="<?php echo esc_url( $base . '/search/' ); ?>" data-cp-search>
		<input type="search" class="header-search__input" id="cp-art-search" name="ArtSearch"
			autocomplete="off" maxlength="40"
			placeholder="<?php echo esc_attr( $placeholder ); ?>"
			aria-label="<?php esc_attr_e( 'Поиск по артикулу', 'intercarz' ); ?>">
		<button type="submit" class="header-search__btn" aria-label="<?php esc_attr_e( 'Найти', 'intercarz' ); ?>">
			<?php intercarz_icon( 'search' ); ?>
		</button>
		<div id="CmSearchResult" class="cp-search-result"></div>
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
	if ( ! get_theme_mod( 'intercarz_show_usp', true ) ) {
		return;
	}
	$usps = array(
		array( 'truck', get_theme_mod( 'intercarz_usp_1', __( 'Доставка по всей стране', 'intercarz' ) ) ),
		array( 'shield', get_theme_mod( 'intercarz_usp_2', __( 'Оригинал и проверенные аналоги', 'intercarz' ) ) ),
	);
	echo '<div class="topbar__usp">';
	foreach ( $usps as $usp ) {
		if ( ! $usp[1] ) {
			continue;
		}
		printf(
			'<span class="topbar__item">%1$s<span>%2$s</span></span>',
			intercarz_get_icon( $usp[0] ),
			esc_html( $usp[1] )
		);
	}
	echo '</div>';
}

/**
 * Справочник поддерживаемых соцсетей: id => название (и id = имя иконки).
 *
 * @return array
 */
function intercarz_social_networks() {
	return array(
		'facebook'  => 'Facebook',
		'instagram' => 'Instagram',
		'youtube'   => 'YouTube',
		'telegram'  => 'Telegram',
		'whatsapp'  => 'WhatsApp',
	);
}

/**
 * Ссылки на соцсети (из Customizer). Выводит только заполненные.
 */
function intercarz_social_links() {
	$out = '';
	foreach ( intercarz_social_networks() as $id => $name ) {
		$url = get_theme_mod( 'intercarz_social_' . $id );
		if ( ! $url ) {
			continue;
		}
		$out .= sprintf(
			'<a class="social-links__item" href="%1$s" target="_blank" rel="noopener noreferrer" aria-label="%2$s" title="%2$s">%3$s</a>',
			esc_url( $url ),
			esc_attr( $name ),
			intercarz_get_icon( $id )
		);
	}
	if ( $out ) {
		echo '<div class="social-links">' . $out . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- экранировано выше.
	}
}

/**
 * Справочник способов оплаты: id => подпись.
 *
 * @return array
 */
function intercarz_payment_methods() {
	return array(
		'visa'       => 'Visa',
		'mastercard' => 'Mastercard',
		'maestro'    => 'Maestro',
		'paypal'     => 'PayPal',
		'applepay'   => 'Apple Pay',
		'googlepay'  => 'Google Pay',
	);
}

/**
 * Иконки оплаты (чипы) — выводит включённые в Customizer.
 */
function intercarz_payment_icons() {
	$out = '';
	foreach ( intercarz_payment_methods() as $id => $label ) {
		if ( ! get_theme_mod( 'intercarz_pay_' . $id, false ) ) {
			continue;
		}
		$out .= '<span class="pay-chip pay-chip--' . esc_attr( $id ) . '">' . esc_html( $label ) . '</span>';
	}
	if ( $out ) {
		echo '<div class="pay-icons">' . $out . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- экранировано выше.
	}
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
		'facebook' => '<path d="M15 3h-3a4 4 0 0 0-4 4v3H5v4h3v7h4v-7h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>',
		'instagram'=> '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.5" fill="currentColor"/>',
		'youtube'  => '<rect x="2" y="5" width="20" height="14" rx="4"/><polygon points="10 9 16 12 10 15" fill="currentColor" stroke="none"/>',
		'telegram' => '<path d="M22 4 2 11l6 2 2 6 3-4 5 4z"/><path d="M8 13l9-6-6 8"/>',
		'whatsapp' => '<path d="M21 12a9 9 0 0 1-13.5 7.8L3 21l1.3-4.4A9 9 0 1 1 21 12z"/><path d="M8.5 8.5c-.3 1 .2 2.4 1.4 3.6s2.6 1.7 3.6 1.4c.5-.2.8-.9.6-1.4l-1-1-1.2.6-1.6-1.6.6-1.2-1-1c-.5-.2-1.2.1-1.4.6z"/>',
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
 * Корзина в шапке: реальная (WooCommerce) либо статичный фолбэк-значок,
 * чтобы иконка корзины присутствовала даже без активного WooCommerce.
 */
function intercarz_header_cart() {
	if ( function_exists( 'intercarz_cart_link' ) ) {
		intercarz_cart_link();
		return;
	}
	$url = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '#';
	?>
	<div class="cart-link-wrap" id="cp-cart">
		<a class="cart-link" href="<?php echo esc_url( $url ); ?>">
			<?php intercarz_icon( 'cart' ); ?>
			<span class="cart-link__count" data-count="0">0</span>
		</a>
	</div>
	<?php
}

/**
 * «Нужна помощь: телефон» в строке меню (если задан телефон).
 */
function intercarz_header_help() {
	$phone = get_theme_mod( 'intercarz_phone' );
	if ( ! $phone ) {
		return;
	}
	printf(
		'<a class="header-help" href="tel:%1$s"><span class="header-help__label">%2$s</span>%3$s<span class="header-help__phone">%4$s</span></a>',
		esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ),
		esc_html__( 'Нужна помощь:', 'intercarz' ),
		intercarz_get_icon( 'phone' ),
		esc_html( $phone )
	);
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
	if ( is_user_logged_in() ) {
		$current = wp_get_current_user();
		$top = $current->display_name ? $current->display_name : __( 'Кабинет', 'intercarz' );
		$sub = __( 'Личный кабинет', 'intercarz' );
	} else {
		$top = __( 'Войти', 'intercarz' );
		$sub = __( 'или регистрация', 'intercarz' );
	}
	printf(
		'<a class="account-link" href="%1$s">%2$s<span class="account-link__lines"><span class="account-link__top">%3$s</span><span class="account-link__sub">%4$s</span></span></a>',
		esc_url( $account_url ),
		intercarz_get_icon( 'user' ),
		esc_html( $top ),
		esc_html( $sub )
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

