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
			'title'    => __( 'Brand colours', 'intercarz' ),
			'priority' => 25,
		)
	);

	$colors = array(
		'intercarz_color_accent'       => array( 'label' => __( 'Accent (buttons, links)', 'intercarz' ), 'default' => '#F76707' ),
		'intercarz_color_accent_hover' => array( 'label' => __( 'Accent — hover', 'intercarz' ), 'default' => '#E8590C' ),
		'intercarz_color_accent_light' => array( 'label' => __( 'Accent — light background', 'intercarz' ), 'default' => '#FFF0E6' ),
		'intercarz_color_topbar'       => array( 'label' => __( 'Top bar background', 'intercarz' ), 'default' => '#1A1D21' ),
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
			'title'    => __( 'Header & blocks', 'intercarz' ),
			'priority' => 26,
		)
	);

	$toggles = array(
		'intercarz_show_search' => __( 'Show search in header', 'intercarz' ),
		'intercarz_show_nav'    => __( 'Show navigation below header', 'intercarz' ),
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
		'intercarz_module_base'       => array( 'label' => __( 'Catalog module base path', 'intercarz' ), 'default' => '/carparts' ),
		'intercarz_search_placeholder' => array( 'label' => __( 'Search placeholder', 'intercarz' ), 'default' => __( 'Part number, OE…', 'intercarz' ) ),
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
		'intercarz_usp_1' => array( 'label' => __( 'USP #1', 'intercarz' ), 'default' => __( 'Nationwide delivery', 'intercarz' ) ),
		'intercarz_usp_2' => array( 'label' => __( 'USP #2', 'intercarz' ), 'default' => __( 'Genuine & trusted parts', 'intercarz' ) ),
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
			'title'    => __( 'Contacts & footer', 'intercarz' ),
			'priority' => 30,
		)
	);

	$fields = array(
		'intercarz_phone'        => array( 'label' => __( 'Phone (top bar)', 'intercarz' ), 'default' => '', 'type' => 'text' ),
		'intercarz_phone_label'  => array( 'label' => __( 'Phone label', 'intercarz' ), 'default' => __( 'Toll-free', 'intercarz' ), 'type' => 'text' ),
		'intercarz_hours'        => array( 'label' => __( 'Working hours', 'intercarz' ), 'default' => '', 'type' => 'text' ),
		'intercarz_email'        => array( 'label' => __( 'E-mail', 'intercarz' ), 'default' => '', 'type' => 'text' ),
		'intercarz_address'      => array( 'label' => __( 'Address', 'intercarz' ), 'default' => '', 'type' => 'textarea' ),
		'intercarz_footer_about' => array( 'label' => __( 'About (footer)', 'intercarz' ), 'default' => '', 'type' => 'textarea' ),
		'intercarz_copyright'    => array( 'label' => __( 'Copyright', 'intercarz' ), 'default' => '© ' . gmdate( 'Y' ) . ' InterCarz', 'type' => 'text' ),
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
			'title'    => __( 'Social networks', 'intercarz' ),
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
				'label'       => sprintf( __( 'Link: %s', 'intercarz' ), $net ),
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
			'title'    => __( 'Payment icons (footer)', 'intercarz' ),
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
