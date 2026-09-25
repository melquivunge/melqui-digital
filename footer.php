<?php
/**
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

$md_email = md_option( 'contact_email' );
$md_logo  = function_exists( 'get_field' ) ? get_field( 'brand_logo', 'option' ) : 0;

$md_footer_services = new WP_Query(
	array(
		'post_type'      => 'md_service',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	)
);
?>
</main>

<footer class="site-footer">
	<div class="container">
		<div class="site-footer__top">
			<div class="site-footer__brand">
				<div class="brand">
					<?php
					if ( $md_logo ) {
						echo wp_get_attachment_image( (int) $md_logo, 'thumbnail', false, array( 'class' => 'brand__mark', 'alt' => '' ) );
					}
					?>

					<span class="brand__text">
						<span class="brand__name"><?php bloginfo( 'name' ); ?></span>
						<span class="brand__tagline"><?php echo esc_html( md_option( 'brand_tagline' ) ); ?></span>
					</span>
				</div>

				<?php if ( md_option( 'footer_text' ) ) : ?>
					<p class="site-footer__text"><?php echo esc_html( md_option( 'footer_text' ) ); ?></p>
				<?php endif; ?>

				<?php if ( md_option( 'footer_meta' ) ) : ?>
					<p class="eyebrow"><?php echo esc_html( md_option( 'footer_meta' ) ); ?></p>
				<?php endif; ?>

				<?php if ( $md_email ) : ?>
					<a class="site-footer__email" href="<?php echo esc_url( 'mailto:' . $md_email ); ?>"><?php echo esc_html( $md_email ); ?></a>
				<?php endif; ?>
			</div>

			<nav class="site-footer__col" aria-label="<?php esc_attr_e( 'Navegação do rodapé', 'melqui-digital' ); ?>">
				<h2 class="eyebrow"><?php esc_html_e( 'Navegação', 'melqui-digital' ); ?></h2>

				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>

			<?php if ( $md_footer_services->have_posts() ) : ?>
				<nav class="site-footer__col" aria-label="<?php esc_attr_e( 'Serviços', 'melqui-digital' ); ?>">
					<h2 class="eyebrow"><?php esc_html_e( 'Serviços', 'melqui-digital' ); ?></h2>

					<ul>
						<?php
						while ( $md_footer_services->have_posts() ) :
							$md_footer_services->the_post();
							?>
							<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
							<?php
						endwhile;
						wp_reset_postdata();
						?>
					</ul>
				</nav>
			<?php endif; ?>
		</div>

		<div class="site-footer__bottom">
			<p class="eyebrow">
				<?php
				printf(
					/* translators: 1: year, 2: site name, 3: signature. */
					esc_html__( '© %1$s %2$s · %3$s', 'melqui-digital' ),
					esc_html( (string) gmdate( 'Y' ) ),
					esc_html( get_bloginfo( 'name' ) ),
					esc_html( md_option( 'footer_note' ) )
				);
				?>
			</p>

			<?php if ( md_option( 'footer_stack' ) ) : ?>
				<p class="eyebrow"><?php echo esc_html( md_option( 'footer_stack' ) ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
