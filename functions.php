<?php
/**
 * InterCarz theme bootstrap.
 *
 * Тема-оболочка для встраиваемого каталога CPMod. Подключает WooCommerce,
 * header/footer, мини-корзину и свитчеры (валюта CURCY, язык Polylang).
 *
 * @package InterCarz
 */

defined( 'ABSPATH' ) || exit;

define( 'INTERCARZ_VERSION', '0.1.0' );
define( 'INTERCARZ_DIR', get_template_directory() );
define( 'INTERCARZ_URI', get_template_directory_uri() );

require INTERCARZ_DIR . '/inc/setup.php';
require INTERCARZ_DIR . '/inc/enqueue.php';
require INTERCARZ_DIR . '/inc/template-tags.php';
require INTERCARZ_DIR . '/inc/customizer.php';

if ( class_exists( 'WooCommerce' ) ) {
	require INTERCARZ_DIR . '/inc/woocommerce.php';
}
