<?php
/**
 * Customizer: цвета бренда, шапка/подвал, контакты, соцсети, оплата.
 *
 * @package InterCarz
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bool-санитайзер для чекбоксов.
 *
 * @param mixed $value
 * @return bool
 */
function intercarz_sanitize_checkbox( $value ) {
	return (bool) $value;
}

add_action( 'customize_register', 'intercarz_customize_register' );
/**
 * Register theme options.
 *
 * @param WP_Customize_Manager $wp_customize
 */
function intercarz_customize_register( $wp_customize ) {

	/* ------------------------------------------------------------------ *
	 *  Цвета бренда
	 * ------------------------------------------------------------------ */
	$wp_customize->add_section(
		'intercarz_colors',
		array(
			'title'    => __( 'Цвета бренда', 'intercarz' ),
			'priority' => 25,
		)
	);

	$colors = array(
		'intercarz_color_accent'       => array( 'label' => __( 'Акцент (кнопки, ссылки)', 'intercarz' ), 'default' => '#F76707' ),
		'intercarz_color_accent_hover' => array( 'label' => __( 'Акцент — наведение', 'intercarz' ), 'default' => '#E8590C' ),
		'intercarz_color_accent_light' => array( 'label' => __( 'Акцент — светлый фон', 'intercarz' ), 'default' => '#FFF0E6' ),
		'intercarz_color_topbar'       => array( 'label' => __( 'Фон верхней полосы', 'intercarz' ), 'default' => '#1A1D21' ),
	);
	foreach ( $colors as $id => $args ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $args['default'],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$id,
				array(
					'label'   => $args['label'],
					'section' => 'intercarz_colors',
				)
			)
		);
	}

	/* ------------------------------------------------------------------ *
	 *  Шапка / подвал — блоки
	 * ------------------------------------------------------------------ */
	$wp_customize->add_section(
		'intercarz_header',
		array(
			'title'    => __( 'Шапка и блоки', 'intercarz' ),
			'priority' => 26,
		)
	);

	$toggles = array(
		'intercarz_show_search' => __( 'Показывать поиск в шапке', 'intercarz' ),
		'intercarz_show_nav'    => __( 'Показывать меню под шапкой', 'intercarz' ),
	);
	foreach ( $toggles as $id => $label ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => true,
				'sanitize_callback' => 'intercarz_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $label,
				'section' => 'intercarz_header',
				'type'    => 'checkbox',
			)
		);
	}

	// Поиск по артикулу (модуль CPMod).
	$search_fields = array(
		'intercarz_module_base'       => array( 'label' => __( 'Базовый путь модуля каталога', 'intercarz' ), 'default' => '/carparts' ),
		'intercarz_search_placeholder' => array( 'label' => __( 'Плейсхолдер поиска', 'intercarz' ), 'default' => __( 'Номер, артикул, OE…', 'intercarz' ) ),
	);
	foreach ( $search_fields as $id => $args ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $args['default'],
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $args['label'],
				'section' => 'intercarz_header',
				'type'    => 'text',
			)
		);
	}

	$usp = array(
		'intercarz_usp_1' => array( 'label' => __( 'УТП №1', 'intercarz' ), 'default' => __( 'Доставка по всей стране', 'intercarz' ) ),
		'intercarz_usp_2' => array( 'label' => __( 'УТП №2', 'intercarz' ), 'default' => __( 'Оригинал и проверенные аналоги', 'intercarz' ) ),
	);
	foreach ( $usp as $id => $args ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $args['default'],
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $args['label'],
				'section' => 'intercarz_header',
				'type'    => 'text',
			)
		);
	}

	/* ------------------------------------------------------------------ *
	 *  Контакты и подвал
	 * ------------------------------------------------------------------ */
	$wp_customize->add_section(
		'intercarz_contacts',
		array(
			'title'    => __( 'Контакты и подвал', 'intercarz' ),
			'priority' => 30,
		)
	);

	$fields = array(
		'intercarz_phone'        => array( 'label' => __( 'Телефон (верхняя полоса)', 'intercarz' ), 'default' => '', 'type' => 'text' ),
		'intercarz_phone_label'  => array( 'label' => __( 'Подпись под телефоном', 'intercarz' ), 'default' => __( 'Звонок бесплатный', 'intercarz' ), 'type' => 'text' ),
		'intercarz_hours'        => array( 'label' => __( 'Часы работы', 'intercarz' ), 'default' => '', 'type' => 'text' ),
		'intercarz_email'        => array( 'label' => __( 'E-mail', 'intercarz' ), 'default' => '', 'type' => 'text' ),
		'intercarz_address'      => array( 'label' => __( 'Адрес', 'intercarz' ), 'default' => '', 'type' => 'textarea' ),
		'intercarz_footer_about' => array( 'label' => __( 'О компании (подвал)', 'intercarz' ), 'default' => '', 'type' => 'textarea' ),
		'intercarz_copyright'    => array( 'label' => __( 'Копирайт', 'intercarz' ), 'default' => '© ' . gmdate( 'Y' ) . ' InterCarz', 'type' => 'text' ),
	);
	foreach ( $fields as $id => $args ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $args['default'],
				'sanitize_callback' => 'textarea' === $args['type'] ? 'sanitize_textarea_field' : 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $args['label'],
				'section' => 'intercarz_contacts',
				'type'    => $args['type'],
			)
		);
	}

	/* ------------------------------------------------------------------ *
	 *  Соцсети
	 * ------------------------------------------------------------------ */
	$wp_customize->add_section(
		'intercarz_social',
		array(
			'title'    => __( 'Соцсети', 'intercarz' ),
			'priority' => 31,
		)
	);

	foreach ( intercarz_social_networks() as $id => $net ) {
		$setting = 'intercarz_social_' . $id;
		$wp_customize->add_setting(
			$setting,
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			$setting,
			array(
				/* translators: %s: social network name. */
				'label'       => sprintf( __( 'Ссылка: %s', 'intercarz' ), $net ),
				'section'     => 'intercarz_social',
				'type'        => 'url',
			)
		);
	}

	/* ------------------------------------------------------------------ *
	 *  Иконки оплаты
	 * ------------------------------------------------------------------ */
	$wp_customize->add_section(
		'intercarz_payments',
		array(
			'title'    => __( 'Иконки оплаты (подвал)', 'intercarz' ),
			'priority' => 32,
		)
	);

	foreach ( intercarz_payment_methods() as $id => $label ) {
		$setting = 'intercarz_pay_' . $id;
		$wp_customize->add_setting(
			$setting,
			array(
				'default'           => false,
				'sanitize_callback' => 'intercarz_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			$setting,
			array(
				'label'   => $label,
				'section' => 'intercarz_payments',
				'type'    => 'checkbox',
			)
		);
	}
}
