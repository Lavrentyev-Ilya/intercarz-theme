<?php
/**
 * Кастомные типы записей для витрины главной: слайды, отзывы, бренды.
 * Редактируются в админке (добавить/изменить/удалить, порядок — поле «Order»).
 *
 * @package InterCarz
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'intercarz_register_cpts' );
/**
 * Register showcase post types.
 */
function intercarz_register_cpts() {

	register_post_type(
		'ic_slide',
		array(
			'labels'        => array(
				'name'          => __( 'Слайды', 'intercarz' ),
				'singular_name' => __( 'Слайд', 'intercarz' ),
				'add_new_item'  => __( 'Добавить слайд', 'intercarz' ),
				'edit_item'     => __( 'Редактировать слайд', 'intercarz' ),
			),
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => true,
			'menu_icon'     => 'dashicons-images-alt2',
			'menu_position' => 26,
			'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
			'has_archive'   => false,
			'rewrite'       => false,
		)
	);

	register_post_type(
		'ic_testimonial',
		array(
			'labels'        => array(
				'name'          => __( 'Отзывы', 'intercarz' ),
				'singular_name' => __( 'Отзыв', 'intercarz' ),
				'add_new_item'  => __( 'Добавить отзыв', 'intercarz' ),
				'edit_item'     => __( 'Редактировать отзыв', 'intercarz' ),
			),
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => true,
			'menu_icon'     => 'dashicons-format-quote',
			'menu_position' => 27,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			'has_archive'   => false,
			'rewrite'       => false,
		)
	);

	register_post_type(
		'ic_brand',
		array(
			'labels'        => array(
				'name'          => __( 'Бренды', 'intercarz' ),
				'singular_name' => __( 'Бренд', 'intercarz' ),
				'add_new_item'  => __( 'Добавить бренд', 'intercarz' ),
				'edit_item'     => __( 'Редактировать бренд', 'intercarz' ),
			),
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => true,
			'menu_icon'     => 'dashicons-awards',
			'menu_position' => 28,
			'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
			'has_archive'   => false,
			'rewrite'       => false,
		)
	);
}

/* ------------------------------------------------------------------------- *
 *  Метабоксы
 * ------------------------------------------------------------------------- */

add_action( 'add_meta_boxes', 'intercarz_register_metaboxes' );
/**
 * Register meta boxes for showcase CPTs.
 */
function intercarz_register_metaboxes() {
	add_meta_box( 'ic_slide_fields', __( 'Параметры слайда', 'intercarz' ), 'intercarz_slide_metabox', 'ic_slide', 'normal', 'high' );
	add_meta_box( 'ic_testimonial_fields', __( 'Параметры отзыва', 'intercarz' ), 'intercarz_testimonial_metabox', 'ic_testimonial', 'side', 'high' );
	add_meta_box( 'ic_brand_fields', __( 'Параметры бренда', 'intercarz' ), 'intercarz_brand_metabox', 'ic_brand', 'side', 'high' );
}

/**
 * Helper: текстовое поле метабокса.
 *
 * @param int    $post_id
 * @param string $key
 * @param string $label
 * @param string $type  text|url|number|textarea
 */
function intercarz_meta_field( $post_id, $key, $label, $type = 'text' ) {
	$value = get_post_meta( $post_id, $key, true );
	echo '<p><label for="' . esc_attr( $key ) . '"><strong>' . esc_html( $label ) . '</strong></label><br>';
	if ( 'textarea' === $type ) {
		echo '<textarea id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" rows="3" style="width:100%;">' . esc_textarea( $value ) . '</textarea>';
	} else {
		echo '<input type="' . esc_attr( $type ) . '" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" style="width:100%;">';
	}
	echo '</p>';
}

/**
 * Slide meta box.
 *
 * @param WP_Post $post
 */
function intercarz_slide_metabox( $post ) {
	wp_nonce_field( 'intercarz_save_meta', 'intercarz_meta_nonce' );
	echo '<p style="color:#666;">' . esc_html__( 'Фон слайда — изображение записи (Featured image) справа.', 'intercarz' ) . '</p>';
	intercarz_meta_field( $post->ID, '_ic_subtitle', __( 'Подзаголовок / описание', 'intercarz' ), 'textarea' );
	intercarz_meta_field( $post->ID, '_ic_btn_text', __( 'Текст кнопки', 'intercarz' ) );
	intercarz_meta_field( $post->ID, '_ic_btn_url', __( 'Ссылка кнопки', 'intercarz' ), 'url' );
}

/**
 * Testimonial meta box.
 *
 * @param WP_Post $post
 */
function intercarz_testimonial_metabox( $post ) {
	wp_nonce_field( 'intercarz_save_meta', 'intercarz_meta_nonce' );
	intercarz_meta_field( $post->ID, '_ic_role', __( 'Должность / роль', 'intercarz' ) );
	intercarz_meta_field( $post->ID, '_ic_rating', __( 'Оценка (1–5)', 'intercarz' ), 'number' );
	echo '<p style="color:#666;">' . esc_html__( 'Текст отзыва — в основном редакторе. Фото — изображение записи.', 'intercarz' ) . '</p>';
}

/**
 * Brand meta box.
 *
 * @param WP_Post $post
 */
function intercarz_brand_metabox( $post ) {
	wp_nonce_field( 'intercarz_save_meta', 'intercarz_meta_nonce' );
	echo '<p style="color:#666;">' . esc_html__( 'Логотип — изображение записи (Featured image).', 'intercarz' ) . '</p>';
	intercarz_meta_field( $post->ID, '_ic_url', __( 'Ссылка', 'intercarz' ), 'url' );
}

add_action( 'save_post', 'intercarz_save_meta' );
/**
 * Save showcase meta fields.
 *
 * @param int $post_id
 */
function intercarz_save_meta( $post_id ) {
	if ( ! isset( $_POST['intercarz_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['intercarz_meta_nonce'] ) ), 'intercarz_save_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = array(
		'_ic_subtitle' => 'sanitize_textarea_field',
		'_ic_btn_text' => 'sanitize_text_field',
		'_ic_btn_url'  => 'esc_url_raw',
		'_ic_role'     => 'sanitize_text_field',
		'_ic_rating'   => 'absint',
		'_ic_url'      => 'esc_url_raw',
	);

	foreach ( $fields as $key => $sanitizer ) {
		if ( isset( $_POST[ $key ] ) ) {
			$raw = wp_unslash( $_POST[ $key ] );
			update_post_meta( $post_id, $key, call_user_func( $sanitizer, $raw ) );
		}
	}
}
