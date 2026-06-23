<?php
/**
 * Front page — витрина главной (секции конфигурируются в Customizer → «Главная страница»).
 *
 * Используется WordPress для главной страницы сайта (не для страниц приложения
 * CPMod, которое рендерится своим маршрутом).
 *
 * @package InterCarz
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( intercarz_home_on( 'hero' ) ) {
	intercarz_home_hero();
}
if ( intercarz_home_on( 'categories' ) ) {
	intercarz_home_categories();
}
if ( intercarz_home_on( 'banners_a' ) ) {
	intercarz_home_banners( 'a' );
}
if ( intercarz_home_on( 'bestseller' ) ) {
	intercarz_home_products( 'bestseller', 'best_selling_products', __( 'Best sellers', 'intercarz' ) );
}
if ( intercarz_home_on( 'banners_b' ) ) {
	intercarz_home_banners( 'b' );
}
if ( intercarz_home_on( 'offers' ) ) {
	intercarz_home_products( 'offers', 'sale_products', __( 'Special offers', 'intercarz' ) );
}
if ( intercarz_home_on( 'testimonials' ) ) {
	intercarz_home_testimonials();
}
if ( intercarz_home_on( 'features' ) ) {
	intercarz_home_features();
}
if ( intercarz_home_on( 'brands' ) ) {
	intercarz_home_brands();
}
if ( intercarz_home_on( 'blog' ) ) {
	intercarz_home_blog();
}
if ( intercarz_home_on( 'newsletter' ) ) {
	intercarz_home_newsletter();
}

get_footer();
