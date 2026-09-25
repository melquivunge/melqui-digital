<?php
/**
 * Single service. The FAQ repeater here also feeds the FAQPage schema in
 * inc/seo.php — same source, no duplicated content.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$md_for_who  = get_field( 'for_who' );
	$md_outcomes = get_field( 'outcomes' );
	$md_caps     = get_field( 'capabilities' );
	$md_process  = get_field( 'process' );
	$md_faqs     = get_field( 'faqs' );
	$md_related  = get_field( 'related_project' );
	?>

	<article <?php post_class(); ?>>
		<header class="section svc-hero">
			<div class="container">
				<?php md_breadcrumbs( __( 'Serviços', 'melqui-digital' ), (string) get_post_type_archive_link( 'md_service' ) ); ?>

				<p class="eyebrow">
					<?php
					printf(
						/* translators: 1: service number, 2: service name. */
						esc_html__( 'Serviço %1$s · %2$s', 'melqui-digital' ),
						esc_html( (string) get_field( 'number' ) ),
						esc_html( get_the_title() )
					);
					?>
				</p>

				<h1 class="display-xl"><?php echo esc_html( (string) ( get_field( 'h1' ) ?: get_the_title() ) ); ?></h1>

				<?php if ( get_field( 'summary' ) ) : ?>
					<div class="lede-bar">
						<p><?php echo esc_html( (string) get_field( 'summary' ) ); ?></p>
					</div>
				<?php endif; ?>
			</div>
		</header>

		<?php if ( get_field( 'problem' ) || $md_for_who ) : ?>
			<section class="section" aria-labelledby="problema">
				<div class="container two-col">
					<h2 id="problema"><?php esc_html_e( 'O problema', 'melqui-digital' ); ?></h2>

					<div>
						<p class="two-col__lede"><?php echo esc_html( (string) get_field( 'problem' ) ); ?></p>

						<?php if ( is_array( $md_for_who ) && $md_for_who ) : ?>
							<p class="eyebrow eyebrow--accent"><?php esc_html_e( 'Indicado para', 'melqui-digital' ); ?></p>

							<ul class="dash-list">
								<?php foreach ( $md_for_who as $md_row ) : ?>
									<?php if ( empty( $md_row['item'] ) ) : continue; endif; ?>
									<li><?php echo esc_html( (string) $md_row['item'] ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( $md_outcomes || $md_caps ) : ?>
			<section class="section section--muted" aria-labelledby="entrega">
				<h2 id="entrega" class="screen-reader-text"><?php esc_html_e( 'O que está incluído', 'melqui-digital' ); ?></h2>

				<div class="container grid grid--2">
					<div>
						<h3><?php esc_html_e( 'Resultados esperados', 'melqui-digital' ); ?></h3>
						<?php md_line_list( $md_outcomes ); ?>
					</div>

					<div>
						<h3><?php esc_html_e( 'Capacidades incluídas', 'melqui-digital' ); ?></h3>
						<?php md_line_list( $md_caps ); ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( is_array( $md_process ) && $md_process ) : ?>
			<section class="section" aria-labelledby="processo">
				<div class="container two-col">
					<h2 id="processo"><?php esc_html_e( 'Como o trabalho acontece', 'melqui-digital' ); ?></h2>

					<ol class="rows rows--compact">
						<?php foreach ( $md_process as $md_index => $md_step ) : ?>
							<li class="rows__item">
								<span class="rows__n"><?php echo esc_html( sprintf( '%02d', $md_index + 1 ) ); ?></span>
								<h3 class="rows__title"><?php echo esc_html( (string) $md_step['title'] ); ?></h3>
								<p class="rows__text"><?php echo esc_html( (string) $md_step['text'] ); ?></p>
							</li>
						<?php endforeach; ?>
					</ol>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( is_array( $md_faqs ) && $md_faqs ) : ?>
			<section class="section" aria-labelledby="faq">
				<div class="container two-col">
					<h2 id="faq"><?php esc_html_e( 'Perguntas frequentes', 'melqui-digital' ); ?></h2>

					<div>
						<?php foreach ( $md_faqs as $md_faq ) : ?>
							<details class="faq">
								<summary class="faq__q">
									<span><?php echo esc_html( (string) $md_faq['q'] ); ?></span>
									<span class="faq__chevron" aria-hidden="true"></span>
								</summary>
								<div class="faq__a"><p><?php echo esc_html( (string) $md_faq['a'] ); ?></p></div>
							</details>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( $md_related ) : ?>
			<section class="section section--muted" aria-labelledby="relacionado">
				<div class="container">
					<h2 id="relacionado"><?php esc_html_e( 'Projeto relacionado', 'melqui-digital' ); ?></h2>

					<?php
					global $post;
					$md_keep = $post;
					$post    = get_post( (int) $md_related ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					setup_postdata( $post );

					get_template_part( 'template-parts/card', 'project-wide' );

					$post = $md_keep; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					wp_reset_postdata();
					?>
				</div>
			</section>
		<?php endif; ?>

		<?php md_other_services( get_the_ID() ); ?>

		<?php get_template_part( 'template-parts/cta-next' ); ?>
	</article>

	<?php
endwhile;

get_footer();
