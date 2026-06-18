<?php
/**
 * Footer — закрывает обёртку контента и выводит подвал.
 *
 * @package InterCarz
 */

defined( 'ABSPATH' ) || exit;
?>
</div><!-- .site-content -->

<footer class="site-footer" role="contentinfo">
	<div class="container">

		<?php if ( is_active_sidebar( 'footer-1' ) || is_active_sidebar( 'footer-2' ) || is_active_sidebar( 'footer-3' ) || has_nav_menu( 'footer' ) || get_theme_mod( 'intercarz_footer_about' ) ) : ?>
		<div class="site-footer__cols">

			<?php if ( get_theme_mod( 'intercarz_footer_about' ) || get_theme_mod( 'intercarz_phone' ) || get_theme_mod( 'intercarz_email' ) ) : ?>
				<div class="site-footer__col">
					<h3><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h3>
					<?php if ( $about = get_theme_mod( 'intercarz_footer_about' ) ) : ?>
						<p><?php echo esc_html( $about ); ?></p>
					<?php endif; ?>
					<ul>
						<?php if ( $phone = get_theme_mod( 'intercarz_phone' ) ) : ?>
							<li><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></li>
						<?php endif; ?>
						<?php if ( $email = get_theme_mod( 'intercarz_email' ) ) : ?>
							<li><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></li>
						<?php endif; ?>
						<?php if ( $address = get_theme_mod( 'intercarz_address' ) ) : ?>
							<li><?php echo nl2br( esc_html( $address ) ); ?></li>
						<?php endif; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if ( has_nav_menu( 'footer' ) ) : ?>
				<div class="site-footer__col">
					<h3><?php esc_html_e( 'Навигация', 'intercarz' ); ?></h3>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'menu_class'     => 'site-footer__menu',
							'depth'          => 1,
							'fallback_cb'    => false,
						)
					);
					?>
				</div>
			<?php endif; ?>

			<?php for ( $i = 1; $i <= 3; $i++ ) : ?>
				<?php if ( is_active_sidebar( 'footer-' . $i ) ) : ?>
					<?php dynamic_sidebar( 'footer-' . $i ); ?>
				<?php endif; ?>
			<?php endfor; ?>

		</div>
		<?php endif; ?>

		<div class="site-footer__bottom">
			<span><?php echo esc_html( get_theme_mod( 'intercarz_copyright', '© ' . gmdate( 'Y' ) . ' InterCarz' ) ); ?></span>
			<?php if ( has_nav_menu( 'footer_legal' ) ) : ?>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer_legal',
						'container'      => false,
						'menu_class'     => 'site-footer__legal',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			<?php endif; ?>
		</div>

	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
