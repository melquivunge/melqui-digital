<?php
/**
 * Contact page. Channels come from the ACF options page so they are edited in
 * one place and reused by the footer and the Person schema. The page content
 * holds the availability note shown in the aside.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

get_header();

$md_email  = md_option( 'contact_email' );
$md_social = function_exists( 'get_field' ) ? get_field( 'social_links', 'option' ) : array();

while ( have_posts() ) :
	the_post();
	?>

	<article <?php post_class(); ?>>
		<header class="section page-hero">
			<div class="container">
				<?php md_breadcrumbs( '', '', __( 'Contato', 'melqui-digital' ) ); ?>

				<p class="eyebrow"><?php esc_html_e( 'Contato', 'melqui-digital' ); ?></p>

				<h1 class="display-xl"><?php the_title(); ?></h1>

				<?php if ( has_excerpt() ) : ?>
					<p class="page-hero__lede"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
			</div>
		</header>

		<div class="section section--bordered">
			<div class="container contact-layout">
				<?php get_template_part( 'template-parts/contact-form' ); ?>

				<aside class="channels">
					<h2 class="eyebrow"><?php esc_html_e( 'Canais diretos', 'melqui-digital' ); ?></h2>

					<dl>
						<?php if ( $md_email ) : ?>
							<div>
								<dt><?php esc_html_e( 'E-mail', 'melqui-digital' ); ?></dt>
								<dd><a href="<?php echo esc_url( 'mailto:' . $md_email ); ?>"><?php echo esc_html( $md_email ); ?></a></dd>
							</div>
						<?php endif; ?>

						<?php if ( is_array( $md_social ) ) : ?>
							<?php foreach ( $md_social as $md_link ) : ?>
								<?php if ( empty( $md_link['url'] ) || empty( $md_link['label'] ) ) : continue; endif; ?>
								<div>
									<dt><?php echo esc_html( (string) $md_link['label'] ); ?></dt>
									<dd><a href="<?php echo esc_url( (string) $md_link['url'] ); ?>" rel="me noopener"><?php echo esc_html( (string) $md_link['url'] ); ?></a></dd>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
					</dl>

					<?php if ( get_the_content() ) : ?>
						<h2 class="eyebrow"><?php esc_html_e( 'Disponibilidade', 'melqui-digital' ); ?></h2>
						<div class="channels__note"><?php the_content(); ?></div>
					<?php endif; ?>
				</aside>
			</div>
		</div>
	</article>

	<?php
endwhile;

get_footer();
