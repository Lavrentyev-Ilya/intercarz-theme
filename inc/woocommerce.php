<?php
/**
 * WooCommerce integration: header cart, AJAX fragments, CPMod bridge.
 *
 * Загружается только когда WooCommerce активен (см. functions.php).
 *
 * @package InterCarz
 */

defined( 'ABSPATH' ) || exit;

/**
 * Кол-во товаров в корзине (безопасно).
 *
 * @return int
 */
function intercarz_cart_count() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return 0;
	}
	return (int) WC()->cart->get_cart_contents_count();
}

/**
 * Подытог корзины строкой (с валютой, учитывает CURCY).
 *
 * @return string
 */
function intercarz_cart_subtotal() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return '';
	}
	return WC()->cart->get_cart_subtotal();
}

/**
 * Иконка корзины + счётчик + выпадающая мини-корзина (для header).
 */
function intercarz_cart_link() {
	if ( ! function_exists( 'wc_get_cart_url' ) ) {
		return;
	}
	// #cp-cart — контракт с CPMod: в настройке модуля CMS_CART_HTML_DOM_ID нужно
	// указать «cp-cart». После add-to-cart CPMod возвращает innerHTML этого
	// элемента, а его JS подменяет содержимое корзины в шапке. Поэтому ВСЁ, что
	// должно обновляться (счётчик, сумма, позиции), лежит ВНУТРИ #cp-cart.
	echo '<div class="cart-link-wrap" id="cp-cart" data-mini-cart>';
	intercarz_cart_inner();
	echo '</div>';
}

/**
 * Внутренность элемента #cp-cart: ссылка со счётчиком/суммой + панель мини-корзины.
 * Именно этот HTML возвращает CPMod при AJAX add-to-cart.
 */
function intercarz_cart_inner() {
	$count = intercarz_cart_count();
	?>
	<a class="cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-haspopup="true" aria-expanded="false">
		<?php intercarz_icon( 'cart' ); ?>
		<span class="cart-link__count" data-count="<?php echo esc_attr( $count ); ?>"><?php echo esc_html( $count ); ?></span>
	</a>
	<div class="mini-cart" id="cp-mini-cart">
		<?php intercarz_mini_cart_inner(); ?>
	</div>
	<?php
}

/**
 * Внутренность мини-корзины (стандартный woocommerce_mini_cart()).
 */
function intercarz_mini_cart_inner() {
	if ( ! function_exists( 'woocommerce_mini_cart' ) ) {
		return;
	}
	if ( intercarz_cart_count() < 1 ) {
		echo '<p class="mini-cart__empty">' . esc_html__( 'Корзина пуста', 'intercarz' ) . '</p>';
		return;
	}
	woocommerce_mini_cart();
}

/**
 * Обновление шапки после add-to-cart через стандартные WooCommerce-фрагменты.
 *
 * @param array $fragments
 * @return array
 */
add_filter( 'woocommerce_add_to_cart_fragments', 'intercarz_cart_fragments' );
function intercarz_cart_fragments( $fragments ) {
	$count = intercarz_cart_count();

	$fragments['span.cart-link__count'] =
		'<span class="cart-link__count" data-count="' . esc_attr( $count ) . '">' . esc_html( $count ) . '</span>';

	ob_start();
	echo '<div class="mini-cart" id="cp-mini-cart">';
	intercarz_mini_cart_inner();
	echo '</div>';
	$fragments['#cp-mini-cart'] = ob_get_clean();

	return $fragments;
}

/*
 * Контракт корзины с CPMod.
 *
 * Функцию AxajAddCartDOM() определяет САМО ядро CPMod (core/funcs.php) —
 * тема её НЕ объявляет. Механизм (см. core/funcs.php ~1709):
 *   1. До get_header() при add-to-cart CPMod вызывает ob_start() (буферизация
 *      всего рендера страницы).
 *   2. get_header() + контент + get_footer() пишутся в буфер.
 *   3. После get_footer() CPMod парсит буфер через DOMDocument, находит элемент
 *      с id = CmsCartID (настройка модуля CMS_CART_HTML_DOM_ID) и возвращает
 *      ТОЛЬКО его innerHTML; JS приложения подменяет им корзину в шапке.
 *
 * Поэтому от темы требуется лишь одно: элемент с этим id, содержащий корзину.
 * Здесь это #cp-cart (см. intercarz_cart_link()). В админке CPMod установите
 * CMS_CART_HTML_DOM_ID = cp-cart (если поле пустое — CPMod делает редирект на
 * страницу корзины вместо AJAX-обновления).
 */

/**
 * Не оборачивать WooCommerce-контент в дефолтный .woocommerce wrapper темы —
 * обёртку контента даёт header.php/footer.php (.site-content).
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

add_action( 'woocommerce_before_main_content', 'intercarz_wc_wrapper_start', 10 );
add_action( 'woocommerce_after_main_content', 'intercarz_wc_wrapper_end', 10 );

/**
 * Open WooCommerce content wrapper.
 */
function intercarz_wc_wrapper_start() {
	echo '<div class="woocommerce-content">';
}

/**
 * Close WooCommerce content wrapper.
 */
function intercarz_wc_wrapper_end() {
	echo '</div>';
}

// Товаров в ряд в дефолтных woo-каталогах (если используются вне приложения).
add_filter( 'loop_shop_columns', function () { return 3; } );
