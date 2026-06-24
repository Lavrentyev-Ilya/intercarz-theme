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
				'name'          => __( 'Slides', 'intercarz' ),
				'singular_name' => __( 'Slide', 'intercarz' ),
				'add_new_item'  => __( 'Add slide', 'intercarz' ),
				'edit_item'     => __( 'Edit slide', 'intercarz' ),
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
				'name'          => __( 'Reviews', 'intercarz' ),
				'singular_name' => __( 'Review', 'intercarz' ),
				'add_new_item'  => __( 'Add review', 'intercarz' ),
				'edit_item'     => __( 'Edit review', 'intercarz' ),
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
				'name'          => __( 'Brands', 'intercarz' ),
				'singular_name' => __( 'Brand', 'intercarz' ),
				'add_new_item'  => __( 'Add brand', 'intercarz' ),
				'edit_item'     => __( 'Edit brand', 'intercarz' ),
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
	add_meta_box( 'ic_slide_fields', __( 'Slide settings', 'intercarz' ), 'intercarz_slide_metabox', 'ic_slide', 'normal', 'high' );
	add_meta_box( 'ic_testimonial_fields', __( 'Review settings', 'intercarz' ), 'intercarz_testimonial_metabox', 'ic_testimonial', 'side', 'high' );
	add_meta_box( 'ic_brand_fields', __( 'Brand settings', 'intercarz' ), 'intercarz_brand_metabox', 'ic_brand', 'side', 'high' );
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
	echo '<p style="color:#666;">' . esc_html__( 'Slide background — the post Featured image.', 'intercarz' ) . '</p>';
	intercarz_meta_field( $post->ID, '_ic_subtitle', __( 'Subtitle / description', 'intercarz' ), 'textarea' );
	intercarz_meta_field( $post->ID, '_ic_btn_text', __( 'Button text', 'intercarz' ) );
	intercarz_meta_field( $post->ID, '_ic_btn_url', __( 'Button link', 'intercarz' ), 'url' );
}

/**
 * Testimonial meta box.
 *
 * @param WP_Post $post
 */
function intercarz_testimonial_metabox( $post ) {
	wp_nonce_field( 'intercarz_save_meta', 'intercarz_meta_nonce' );
	intercarz_meta_field( $post->ID, '_ic_role', __( 'Role / position', 'intercarz' ) );
	intercarz_meta_field( $post->ID, '_ic_rating', __( 'Rating (1–5)', 'intercarz' ), 'number' );
	echo '<p style="color:#666;">' . esc_html__( 'Review text — in the main editor. Photo — the post Featured image.', 'intercarz' ) . '</p>';
}

/**
 * Brand meta box.
 *
 * @param WP_Post $post
 */
function intercarz_brand_metabox( $post ) {
	wp_nonce_field( 'intercarz_save_meta', 'intercarz_meta_nonce' );
	echo '<p style="color:#666;">' . esc_html__( 'Logo — the post Featured image.', 'intercarz' ) . '</p>';
	intercarz_meta_field( $post->ID, '_ic_url', __( 'Link', 'intercarz' ), 'url' );
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
