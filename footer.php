<?php
/**
 * Footer — закрывает обёртку контента и выводит подвал.
 *
 * @package InterCarz
 */

defined( 'ABSPATH' ) || exit;

$intercarz_about = get_theme_mod( 'intercarz_footer_about' );
if ( ! $intercarz_about ) {
	$intercarz_about = __( 'Auto parts catalog: genuine and trusted aftermarket parts for any car. Search by make, model and engine.', 'intercarz' );
}
$intercarz_phone   = get_theme_mod( 'intercarz_phone' );
$intercarz_email   = get_theme_mod( 'intercarz_email' );
$intercarz_address = get_theme_mod( 'intercarz_address' );
$intercarz_has_contacts = $intercarz_phone || $intercarz_email || $intercarz_address;
?>
</div><!-- .site-content -->

<footer class="site-footer" role="contentinfo">
	<div class="container">

		<div class="site-footer__cols">

			<div class="site-footer__col site-footer__col--brand">
				<a class="site-footer__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">Inter<b>Carz</b></a>
				<p class="site-footer__about"><?php echo esc_html( $intercarz_about ); ?></p>
				<?php intercarz_social_links(); ?>
			</div>

			<div class="site-footer__col">
				<h3><?php esc_html_e( 'Navigation', 'intercarz' ); ?></h3>
				<?php
				if ( has_nav_menu( 'footer' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'menu_class'     => 'site-footer__menu',
							'depth'          => 1,
							'fallback_cb'    => false,
						)
					);
				} else {
					wp_page_menu(
						array(
							'show_home'  => __( 'Home', 'intercarz' ),
							'menu_class' => 'site-footer__menu',
							'depth'      => 1,
							'container'  => '',
						)
					);
				}
				?>
			</div>

			<?php if ( $intercarz_has_contacts ) : ?>
				<div class="site-footer__col">
					<h3><?php esc_html_e( 'Contacts', 'intercarz' ); ?></h3>
					<ul class="site-footer__contacts">
						<?php if ( $intercarz_phone ) : ?>
							<li><?php intercarz_icon( 'phone' ); ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $intercarz_phone ) ); ?>"><?php echo esc_html( $intercarz_phone ); ?></a></li>
						<?php endif; ?>
						<?php if ( $intercarz_email ) : ?>
							<li><?php intercarz_icon( 'mail' ); ?><a href="mailto:<?php echo esc_attr( $intercarz_email ); ?>"><?php echo esc_html( $intercarz_email ); ?></a></li>
						<?php endif; ?>
						<?php if ( $intercarz_address ) : ?>
							<li><?php intercarz_icon( 'pin' ); ?><span><?php echo nl2br( esc_html( $intercarz_address ) ); ?></span></li>
						<?php endif; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php for ( $i = 1; $i <= 3; $i++ ) : ?>
				<?php if ( is_active_sidebar( 'footer-' . $i ) ) : ?>
					<?php dynamic_sidebar( 'footer-' . $i ); ?>
				<?php endif; ?>
			<?php endfor; ?>

		</div>

		<div class="site-footer__bottom">
			<span><?php echo esc_html( get_theme_mod( 'intercarz_copyright', '© ' . gmdate( 'Y' ) . ' InterCarz' ) ); ?></span>
			<?php intercarz_payment_icons(); ?>
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
