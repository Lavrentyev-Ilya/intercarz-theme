<?php
/**
 * Главная страница (front-page): настройки Customizer + рендер секций.
 *
 * Контент берётся из WooCommerce (категории, хиты, sale), CPT (слайды,
 * отзывы, бренды) и Customizer (баннеры, заголовки, тумблеры).
 *
 * @package InterCarz
 */

defined( 'ABSPATH' ) || exit;

/* ========================================================================= *
 *  Customizer
 * ========================================================================= */

add_action( 'customize_register', 'intercarz_home_customizer' );
/**
 * @param WP_Customize_Manager $wp_customize
 */
function intercarz_home_customizer( $wp_customize ) {

	$wp_customize->add_panel(
		'intercarz_home',
		array(
			'title'    => __( 'Главная страница', 'intercarz' ),
			'priority' => 24,
		)
	);

	// Конфиг секций: slug => [заголовок секции в админке, дефолтный title, счётчик].
	$sections = array(
		'hero'         => array( __( 'Герой-слайдер', 'intercarz' ), '', null ),
		'categories'   => array( __( 'Категории', 'intercarz' ), __( 'Категории товаров', 'intercarz' ), 8 ),
		'banners_a'    => array( __( 'Промо-баннеры (2)', 'intercarz' ), '', null ),
		'bestseller'   => array( __( 'Хиты продаж', 'intercarz' ), __( 'Хиты продаж', 'intercarz' ), 8 ),
		'banners_b'    => array( __( 'Промо-баннеры (3)', 'intercarz' ), '', null ),
		'offers'       => array( __( 'Специальные предложения', 'intercarz' ), __( 'Специальные предложения', 'intercarz' ), 8 ),
		'testimonials' => array( __( 'Отзывы', 'intercarz' ), __( 'Отзывы клиентов', 'intercarz' ), 9 ),
		'features'     => array( __( 'Преимущества', 'intercarz' ), '', null ),
		'brands'       => array( __( 'Бренды', 'intercarz' ), __( 'Бренды', 'intercarz' ), 12 ),
		'blog'         => array( __( 'Блог', 'intercarz' ), __( 'Из блога', 'intercarz' ), 3 ),
		'newsletter'   => array( __( 'Подписка', 'intercarz' ), __( 'Скидка 10% на первый заказ', 'intercarz' ), null ),
	);

	foreach ( $sections as $slug => $cfg ) {
		list( $label, $title_default, $count_default ) = $cfg;

		$wp_customize->add_section(
			'intercarz_home_' . $slug,
			array( 'title' => $label, 'panel' => 'intercarz_home' )
		);

		// Тумблер показа.
		intercarz_add_setting( $wp_customize, 'intercarz_home_' . $slug . '_on', true, 'intercarz_sanitize_checkbox' );
		$wp_customize->add_control(
			'intercarz_home_' . $slug . '_on',
			array( 'label' => __( 'Показывать секцию', 'intercarz' ), 'section' => 'intercarz_home_' . $slug, 'type' => 'checkbox' )
		);

		// Заголовок секции.
		if ( '' !== $title_default ) {
			intercarz_add_setting( $wp_customize, 'intercarz_home_' . $slug . '_title', $title_default, 'sanitize_text_field' );
			$wp_customize->add_control(
				'intercarz_home_' . $slug . '_title',
				array( 'label' => __( 'Заголовок', 'intercarz' ), 'section' => 'intercarz_home_' . $slug, 'type' => 'text' )
			);
		}

		// Счётчик элементов.
		if ( null !== $count_default ) {
			intercarz_add_setting( $wp_customize, 'intercarz_home_' . $slug . '_count', $count_default, 'absint' );
			$wp_customize->add_control(
				'intercarz_home_' . $slug . '_count',
				array( 'label' => __( 'Сколько элементов', 'intercarz' ), 'section' => 'intercarz_home_' . $slug, 'type' => 'number' )
			);
		}
	}

	/* --- Баннеры (2 и 3) --- */
	$banners = array(
		'a1' => 'intercarz_home_banners_a', 'a2' => 'intercarz_home_banners_a',
		'b1' => 'intercarz_home_banners_b', 'b2' => 'intercarz_home_banners_b', 'b3' => 'intercarz_home_banners_b',
	);
	foreach ( $banners as $id => $section ) {
		intercarz_add_setting( $wp_customize, 'intercarz_banner_' . $id . '_img', '', 'esc_url_raw' );
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'intercarz_banner_' . $id . '_img',
				array( 'label' => sprintf( __( 'Баннер %s — изображение', 'intercarz' ), strtoupper( $id ) ), 'section' => $section )
			)
		);
		intercarz_add_setting( $wp_customize, 'intercarz_banner_' . $id . '_title', '', 'sanitize_text_field' );
		$wp_customize->add_control( 'intercarz_banner_' . $id . '_title', array( 'label' => sprintf( __( 'Баннер %s — заголовок', 'intercarz' ), strtoupper( $id ) ), 'section' => $section, 'type' => 'text' ) );
		intercarz_add_setting( $wp_customize, 'intercarz_banner_' . $id . '_sub', '', 'sanitize_text_field' );
		$wp_customize->add_control( 'intercarz_banner_' . $id . '_sub', array( 'label' => sprintf( __( 'Баннер %s — подзаголовок', 'intercarz' ), strtoupper( $id ) ), 'section' => $section, 'type' => 'text' ) );
		intercarz_add_setting( $wp_customize, 'intercarz_banner_' . $id . '_btn', '', 'sanitize_text_field' );
		$wp_customize->add_control( 'intercarz_banner_' . $id . '_btn', array( 'label' => sprintf( __( 'Баннер %s — кнопка', 'intercarz' ), strtoupper( $id ) ), 'section' => $section, 'type' => 'text' ) );
		intercarz_add_setting( $wp_customize, 'intercarz_banner_' . $id . '_url', '', 'esc_url_raw' );
		$wp_customize->add_control( 'intercarz_banner_' . $id . '_url', array( 'label' => sprintf( __( 'Баннер %s — ссылка', 'intercarz' ), strtoupper( $id ) ), 'section' => $section, 'type' => 'url' ) );
	}

	/* --- Преимущества (3 пункта) --- */
	$feat_defaults = array(
		1 => array( __( 'Бесплатная доставка', 'intercarz' ), __( 'При заказе от указанной суммы', 'intercarz' ) ),
		2 => array( __( 'Возврат 30 дней', 'intercarz' ), __( 'Если деталь не подошла', 'intercarz' ) ),
		3 => array( __( 'Поддержка 24/7', 'intercarz' ), __( 'Поможем с подбором запчасти', 'intercarz' ) ),
	);
	foreach ( $feat_defaults as $i => $d ) {
		intercarz_add_setting( $wp_customize, 'intercarz_feature_' . $i . '_title', $d[0], 'sanitize_text_field' );
		$wp_customize->add_control( 'intercarz_feature_' . $i . '_title', array( 'label' => sprintf( __( 'Преимущество %d — заголовок', 'intercarz' ), $i ), 'section' => 'intercarz_home_features', 'type' => 'text' ) );
		intercarz_add_setting( $wp_customize, 'intercarz_feature_' . $i . '_text', $d[1], 'sanitize_text_field' );
		$wp_customize->add_control( 'intercarz_feature_' . $i . '_text', array( 'label' => sprintf( __( 'Преимущество %d — описание', 'intercarz' ), $i ), 'section' => 'intercarz_home_features', 'type' => 'text' ) );
	}

	/* --- Подписка --- */
	intercarz_add_setting( $wp_customize, 'intercarz_home_newsletter_sub', __( 'Подпишитесь на рассылку и получите скидку 10% на первый заказ.', 'intercarz' ), 'sanitize_text_field' );
	$wp_customize->add_control( 'intercarz_home_newsletter_sub', array( 'label' => __( 'Подзаголовок', 'intercarz' ), 'section' => 'intercarz_home_newsletter', 'type' => 'text' ) );
	intercarz_add_setting( $wp_customize, 'intercarz_home_newsletter_shortcode', '', 'wp_kses_post' );
	$wp_customize->add_control( 'intercarz_home_newsletter_shortcode', array( 'label' => __( 'Шорткод формы (напр. MC4WP)', 'intercarz' ), 'description' => __( 'Если пусто — показывается простая форма-заглушка.', 'intercarz' ), 'section' => 'intercarz_home_newsletter', 'type' => 'text' ) );
}

/**
 * Шорткат регистрации настройки Customizer.
 */
function intercarz_add_setting( $wp_customize, $id, $default, $sanitize ) {
	$wp_customize->add_setting( $id, array( 'default' => $default, 'sanitize_callback' => $sanitize, 'transport' => 'refresh' ) );
}

/* ========================================================================= *
 *  Хелперы
 * ========================================================================= */

/**
 * Секция включена?
 *
 * @param string $slug
 * @return bool
 */
function intercarz_home_on( $slug ) {
	return (bool) get_theme_mod( 'intercarz_home_' . $slug . '_on', true );
}

/**
 * Заголовок секции.
 *
 * @param string $slug
 * @param string $default
 */
function intercarz_section_heading( $slug, $default = '' ) {
	$title = get_theme_mod( 'intercarz_home_' . $slug . '_title', $default );
	if ( $title ) {
		echo '<h2 class="section-title">' . esc_html( $title ) . '</h2>';
	}
}

/* ========================================================================= *
 *  Рендер секций
 * ========================================================================= */

/**
 * Герой-слайдер (CPT ic_slide). Фолбэк — статичный баннер.
 */
function intercarz_home_hero() {
	$slides = get_posts(
		array(
			'post_type'      => 'ic_slide',
			'posts_per_page' => 10,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		)
	);
	?>
	<section class="home-hero">
		<div class="container">
			<?php if ( $slides ) : ?>
				<div class="hero-slider" data-slider>
					<div class="hero-slider__track">
						<?php foreach ( $slides as $slide ) :
							$img = get_the_post_thumbnail_url( $slide, 'full' );
							$sub = get_post_meta( $slide->ID, '_ic_subtitle', true );
							$btn = get_post_meta( $slide->ID, '_ic_btn_text', true );
							$url = get_post_meta( $slide->ID, '_ic_btn_url', true );
							?>
							<div class="hero-slide" <?php echo $img ? 'style="background-image:url(' . esc_url( $img ) . ')"' : ''; ?>>
								<div class="hero-slide__inner">
									<h2 class="hero-slide__title"><?php echo esc_html( get_the_title( $slide ) ); ?></h2>
									<?php if ( $sub ) : ?><p class="hero-slide__sub"><?php echo esc_html( $sub ); ?></p><?php endif; ?>
									<?php if ( $btn && $url ) : ?><a class="btn hero-slide__btn" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $btn ); ?></a><?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
					<?php if ( count( $slides ) > 1 ) : ?>
						<button class="slider-arrow slider-arrow--prev" data-slider-prev aria-label="<?php esc_attr_e( 'Назад', 'intercarz' ); ?>">‹</button>
						<button class="slider-arrow slider-arrow--next" data-slider-next aria-label="<?php esc_attr_e( 'Вперёд', 'intercarz' ); ?>">›</button>
						<div class="slider-dots" data-slider-dots></div>
					<?php endif; ?>
				</div>
			<?php else : ?>
				<div class="hero-slide hero-slide--placeholder">
					<div class="hero-slide__inner">
						<h2 class="hero-slide__title"><?php esc_html_e( 'Автозапчасти для любого автомобиля', 'intercarz' ); ?></h2>
						<p class="hero-slide__sub"><?php esc_html_e( 'Оригинальные и проверенные аналоговые детали. Подбор по марке, модели и двигателю.', 'intercarz' ); ?></p>
						<?php if ( function_exists( 'wc_get_page_id' ) ) : ?>
							<a class="btn hero-slide__btn" href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>"><?php esc_html_e( 'В каталог', 'intercarz' ); ?></a>
						<?php endif; ?>
						<p class="hero-slide__hint"><?php esc_html_e( 'Добавьте слайды в админке: «Слайды».', 'intercarz' ); ?></p>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
}

/**
 * Сетка категорий WooCommerce (верхний уровень).
 */
function intercarz_home_categories() {
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return;
	}
	$count = (int) get_theme_mod( 'intercarz_home_categories_count', 8 );
	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'parent'     => 0,
			'hide_empty' => false,
			'number'     => $count,
		)
	);
	if ( is_wp_error( $terms ) || ! $terms ) {
		return;
	}
	?>
	<section class="home-section home-categories">
		<div class="container">
			<?php intercarz_section_heading( 'categories', __( 'Категории товаров', 'intercarz' ) ); ?>
			<div class="cat-grid">
				<?php foreach ( $terms as $term ) :
					$thumb_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
					$img = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'thumbnail' ) : '';
					?>
					<a class="cat-card" href="<?php echo esc_url( get_term_link( $term ) ); ?>">
						<span class="cat-card__icon">
							<?php if ( $img ) : ?>
								<img src="<?php echo esc_url( $img ); ?>" alt="" loading="lazy">
							<?php else : ?>
								<?php intercarz_icon( 'truck' ); ?>
							<?php endif; ?>
						</span>
						<span class="cat-card__name"><?php echo esc_html( $term->name ); ?></span>
						<span class="cat-card__count"><?php echo esc_html( $term->count ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Ряд промо-баннеров: $set = 'a' (2 шт.) или 'b' (3 шт.).
 *
 * @param string $set
 */
function intercarz_home_banners( $set ) {
	$ids = 'a' === $set ? array( 'a1', 'a2' ) : array( 'b1', 'b2', 'b3' );
	$cards = '';
	foreach ( $ids as $id ) {
		$img   = get_theme_mod( 'intercarz_banner_' . $id . '_img' );
		$title = get_theme_mod( 'intercarz_banner_' . $id . '_title' );
		if ( ! $img && ! $title ) {
			continue;
		}
		$sub = get_theme_mod( 'intercarz_banner_' . $id . '_sub' );
		$btn = get_theme_mod( 'intercarz_banner_' . $id . '_btn' );
		$url = get_theme_mod( 'intercarz_banner_' . $id . '_url' );
		$style = $img ? ' style="background-image:url(' . esc_url( $img ) . ')"' : '';
		$cards .= '<a class="promo-banner" href="' . esc_url( $url ? $url : '#' ) . '"' . $style . '>';
		$cards .= '<div class="promo-banner__body">';
		if ( $title ) { $cards .= '<h3 class="promo-banner__title">' . esc_html( $title ) . '</h3>'; }
		if ( $sub ) { $cards .= '<p class="promo-banner__sub">' . esc_html( $sub ) . '</p>'; }
		if ( $btn ) { $cards .= '<span class="btn btn--sm">' . esc_html( $btn ) . '</span>'; }
		$cards .= '</div></a>';
	}
	if ( ! $cards ) {
		return;
	}
	echo '<section class="home-section home-banners home-banners--' . esc_attr( $set ) . '"><div class="container"><div class="promo-grid">' . $cards . '</div></div></section>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Грид товаров через WooCommerce-шорткод.
 *
 * @param string $slug      Slug секции (для заголовка/счётчика).
 * @param string $shortcode best_selling_products|sale_products|featured_products
 * @param string $default_title
 */
function intercarz_home_products( $slug, $shortcode, $default_title ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}
	$count = (int) get_theme_mod( 'intercarz_home_' . $slug . '_count', 8 );
	?>
	<section class="home-section home-products home-<?php echo esc_attr( $slug ); ?>">
		<div class="container">
			<?php intercarz_section_heading( $slug, $default_title ); ?>
			<?php echo do_shortcode( '[' . $shortcode . ' limit="' . $count . '" columns="4"]' ); ?>
		</div>
	</section>
	<?php
}

/**
 * Отзывы (CPT ic_testimonial).
 */
function intercarz_home_testimonials() {
	$count = (int) get_theme_mod( 'intercarz_home_testimonials_count', 9 );
	$items = get_posts(
		array(
			'post_type'      => 'ic_testimonial',
			'posts_per_page' => $count,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		)
	);
	if ( ! $items ) {
		return;
	}
	?>
	<section class="home-section home-testimonials">
		<div class="container">
			<?php intercarz_section_heading( 'testimonials', __( 'Отзывы клиентов', 'intercarz' ) ); ?>
			<div class="t-slider" data-slider data-per="3">
				<div class="t-slider__track hero-slider__track">
					<?php foreach ( $items as $t ) :
						$role   = get_post_meta( $t->ID, '_ic_role', true );
						$rating = (int) get_post_meta( $t->ID, '_ic_rating', true );
						$photo  = get_the_post_thumbnail_url( $t, 'thumbnail' );
						?>
						<div class="t-card">
							<?php if ( $rating ) : ?><div class="t-card__stars"><?php echo str_repeat( '★', max( 0, min( 5, $rating ) ) ); ?></div><?php endif; ?>
							<h3 class="t-card__title"><?php echo esc_html( get_the_title( $t ) ); ?></h3>
							<div class="t-card__text"><?php echo wp_kses_post( wpautop( $t->post_content ) ); ?></div>
							<div class="t-card__author">
								<?php if ( $photo ) : ?><img class="t-card__photo" src="<?php echo esc_url( $photo ); ?>" alt="" loading="lazy"><?php endif; ?>
								<span class="t-card__name"><?php echo esc_html( $t->post_title ); ?></span>
								<?php if ( $role ) : ?><span class="t-card__role"><?php echo esc_html( $role ); ?></span><?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<?php if ( count( $items ) > 3 ) : ?>
					<button class="slider-arrow slider-arrow--prev" data-slider-prev aria-label="‹">‹</button>
					<button class="slider-arrow slider-arrow--next" data-slider-next aria-label="›">›</button>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Преимущества (3 пункта).
 */
function intercarz_home_features() {
	$icons = array( 1 => 'truck', 2 => 'shield', 3 => 'clock' );
	?>
	<section class="home-section home-features">
		<div class="container">
			<div class="features-grid">
				<?php for ( $i = 1; $i <= 3; $i++ ) :
					$title = get_theme_mod( 'intercarz_feature_' . $i . '_title' );
					$text  = get_theme_mod( 'intercarz_feature_' . $i . '_text' );
					if ( ! $title ) { continue; }
					?>
					<div class="feature">
						<span class="feature__icon"><?php intercarz_icon( $icons[ $i ] ); ?></span>
						<div>
							<h3 class="feature__title"><?php echo esc_html( $title ); ?></h3>
							<?php if ( $text ) : ?><p class="feature__text"><?php echo esc_html( $text ); ?></p><?php endif; ?>
						</div>
					</div>
				<?php endfor; ?>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Бренды (CPT ic_brand) — карусель логотипов.
 */
function intercarz_home_brands() {
	$count = (int) get_theme_mod( 'intercarz_home_brands_count', 12 );
	$items = get_posts(
		array(
			'post_type'      => 'ic_brand',
			'posts_per_page' => $count,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		)
	);
	if ( ! $items ) {
		return;
	}
	?>
	<section class="home-section home-brands">
		<div class="container">
			<?php intercarz_section_heading( 'brands', __( 'Бренды', 'intercarz' ) ); ?>
			<div class="brands-row">
				<?php foreach ( $items as $b ) :
					$logo = get_the_post_thumbnail_url( $b, 'medium' );
					$url  = get_post_meta( $b->ID, '_ic_url', true );
					$tag  = $url ? 'a' : 'span';
					?>
					<<?php echo $tag; ?> class="brand-item"<?php echo $url ? ' href="' . esc_url( $url ) . '"' : ''; ?>>
						<?php if ( $logo ) : ?>
							<img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( $b->post_title ); ?>" loading="lazy">
						<?php else : ?>
							<span class="brand-item__name"><?php echo esc_html( $b->post_title ); ?></span>
						<?php endif; ?>
					</<?php echo $tag; ?>>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Последние записи блога.
 */
function intercarz_home_blog() {
	$count = (int) get_theme_mod( 'intercarz_home_blog_count', 3 );
	$posts = get_posts( array( 'posts_per_page' => $count, 'post_status' => 'publish' ) );
	if ( ! $posts ) {
		return;
	}
	?>
	<section class="home-section home-blog">
		<div class="container">
			<?php intercarz_section_heading( 'blog', __( 'Из блога', 'intercarz' ) ); ?>
			<div class="blog-grid">
				<?php foreach ( $posts as $p ) : ?>
					<article class="blog-card">
						<a class="blog-card__thumb" href="<?php echo esc_url( get_permalink( $p ) ); ?>">
							<?php echo get_the_post_thumbnail( $p, 'medium_large' ); ?>
						</a>
						<div class="blog-card__body">
							<span class="blog-card__date"><?php echo esc_html( get_the_date( '', $p ) ); ?></span>
							<h3 class="blog-card__title"><a href="<?php echo esc_url( get_permalink( $p ) ); ?>"><?php echo esc_html( get_the_title( $p ) ); ?></a></h3>
							<p class="blog-card__excerpt"><?php echo esc_html( wp_trim_words( $p->post_content, 18 ) ); ?></p>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Подписка.
 */
function intercarz_home_newsletter() {
	$sub       = get_theme_mod( 'intercarz_home_newsletter_sub' );
	$shortcode = get_theme_mod( 'intercarz_home_newsletter_shortcode' );
	?>
	<section class="home-section home-newsletter">
		<div class="container newsletter-inner">
			<div class="newsletter-text">
				<?php intercarz_section_heading( 'newsletter', __( 'Скидка 10% на первый заказ', 'intercarz' ) ); ?>
				<?php if ( $sub ) : ?><p class="newsletter-sub"><?php echo esc_html( $sub ); ?></p><?php endif; ?>
			</div>
			<div class="newsletter-form">
				<?php if ( $shortcode ) : ?>
					<?php echo do_shortcode( $shortcode ); ?>
				<?php else : ?>
					<form class="subscribe" method="post" action="#" onsubmit="return false;">
						<input type="email" class="subscribe__input" placeholder="<?php esc_attr_e( 'Ваш e-mail', 'intercarz' ); ?>" required>
						<button type="submit" class="btn subscribe__btn"><?php esc_html_e( 'Подписаться', 'intercarz' ); ?></button>
					</form>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
}
