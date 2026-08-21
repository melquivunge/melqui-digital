<?php
/**
 * Service archive.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

get_header();
?>

<header class="section page-hero">
	<div class="container">
		<?php md_archive_breadcrumb( __( 'Serviços', 'melqui-digital' ) ); ?>

		<p class="eyebrow"><?php echo esc_html( md_option( 'arch_services_eyebrow' ) ); ?></p>
		<h1 class="display-xl"><?php echo esc_html( md_option( 'arch_services_title' ) ); ?></h1>
		<p class="page-hero__lede"><?php echo esc_html( md_option( 'arch_services_text' ) ); ?></p>
	</div>
</header>

<section class="section section--bordered">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<ol class="svc-list">
				<?php
				while ( have_posts() ) :
					the_post();

					$md_for_who  = get_field( 'for_who' );
					$md_outcomes = get_field( 'outcomes' );
					?>
					<li class="svc-list__item">
						<div>
							<p class="eyebrow eyebrow--accent"><?php echo esc_html( (string) get_field( 'number' ) ); ?></p>

							<h2 class="svc-list__title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>

							<p class="svc-list__tagline"><?php echo esc_html( (string) get_field( 'tagline' ) ); ?></p>
						</div>

						<div>
							<p class="svc-list__summary"><?php echo esc_html( (string) get_field( 'summary' ) ); ?></p>

							<?php if ( is_array( $md_for_who ) && $md_for_who ) : ?>
								<p class="eyebrow"><?php esc_html_e( 'Para quem é', 'melqui-digital' ); ?></p>

								<ul class="dash-list">
									<?php foreach ( $md_for_who as $md_row ) : ?>
										<?php if ( empty( $md_row['item'] ) ) : continue; endif; ?>
										<li><?php echo esc_html( (string) $md_row['item'] ); ?></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</div>

						<div>
							<?php if ( is_array( $md_outcomes ) && $md_outcomes ) : ?>
								<p class="eyebrow"><?php esc_html_e( 'Resultados esperados', 'melqui-digital' ); ?></p>

								<ul class="dash-list">
									<?php foreach ( array_slice( $md_outcomes, 0, 3 ) as $md_row ) : ?>
										<?php if ( empty( $md_row['item'] ) ) : continue; endif; ?>
										<li><?php echo esc_html( (string) $md_row['item'] ); ?></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>

							<a class="mono-link" href="<?php the_permalink(); ?>">
								<?php esc_html_e( 'Ver detalhes', 'melqui-digital' ); ?> <span aria-hidden="true">&#8599;</span>
							</a>
						</div>
					</li>
					<?php
				endwhile;
				?>
			</ol>
		<?php else : ?>
			<p><?php esc_html_e( 'Nenhum serviço publicado ainda.', 'melqui-digital' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_template_part(
	'template-parts/cta-next',
	null,
	array(
		'title' => md_option( 'next_services_title' ) ?: null,
		'text'  => md_option( 'next_services_text' ) ?: null,
	)
);

get_footer();
