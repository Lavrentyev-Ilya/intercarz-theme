<?php
/**
 * Customizer settings: контакты, подвал.
 *
 * @package InterCarz
 */

defined( 'ABSPATH' ) || exit;

add_action( 'customize_register', 'intercarz_customize_register' );
/**
 * Register theme options.
 *
 * @param WP_Customize_Manager $wp_customize
 */
function intercarz_customize_register( $wp_customize ) {

	$wp_customize->add_section(
		'intercarz_contacts',
		array(
			'title'    => __( 'Контакты и подвал', 'intercarz' ),
			'priority' => 30,
		)
	);

	$fields = array(
		'intercarz_phone'        => array(
			'label'   => __( 'Телефон (header)', 'intercarz' ),
			'default' => '',
			'type'    => 'text',
		),
		'intercarz_phone_label'  => array(
			'label'   => __( 'Подпись под телефоном', 'intercarz' ),
			'default' => __( 'Звонок бесплатный', 'intercarz' ),
			'type'    => 'text',
		),
		'intercarz_email'        => array(
			'label'   => __( 'E-mail', 'intercarz' ),
			'default' => '',
			'type'    => 'text',
		),
		'intercarz_address'      => array(
			'label'   => __( 'Адрес', 'intercarz' ),
			'default' => '',
			'type'    => 'textarea',
		),
		'intercarz_footer_about' => array(
			'label'   => __( 'О компании (подвал)', 'intercarz' ),
			'default' => '',
			'type'    => 'textarea',
		),
		'intercarz_copyright'    => array(
			'label'   => __( 'Копирайт', 'intercarz' ),
			'default' => '© ' . gmdate( 'Y' ) . ' InterCarz',
			'type'    => 'text',
		),
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
}
