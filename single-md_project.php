<?php
/**
 * Single project / case study.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$md_post_id       = (int) get_the_ID();
	$md_facts         = get_field( 'facts' );
	$md_sections      = get_field( 'sections' );
	$md_stack         = get_the_terms( $md_post_id, 'md_stack' );
	$md_section_count = is_array( $md_sections ) ? count( $md_sections ) : 0;
	$md_impact        = (string) get_field( 'impact' );
	$md_label         = md_project_label( $md_post_id );
	$md_external      = (string) get_field( 'external_url' );
	$md_year          = (string) get_field( 'year' );
	$md_status        = (string) get_field( 'status' );
	$md_engagement    = (string) get_field( 'engagement' );
	$md_role          = (string) get_field( 'role' );
	$md_context       = (string) get_field( 'context' );
	?>

	<article <?php post_class(); ?>>
		<header class="pj-hero">
			<div class="pj-hero__grid" aria-hidden="true"></div>

			<div class="container pj-hero__inner">
				<div>
					<?php md_breadcrumbs( __( 'Projetos', 'melqui-digital' ), (string) get_post_type_archive_link( 'md_project' ) ); ?>

					<?php if ( '' !== $md_label ) : ?>
						<p class="eyebrow eyebrow--accent"><?php echo esc_html( $md_label ); ?></p>
					<?php endif; ?>

					<h1 class="display-xl"><?php the_title(); ?></h1>

					<?php if ( '' !== $md_impact ) : ?>
						<p class="pj-hero__lede"><?php echo esc_html( $md_impact ); ?></p>
					<?php endif; ?>

					<?php if ( '' !== $md_external ) : ?>
						<a class="btn btn--light" href="<?php echo esc_url( $md_external ); ?>" rel="noopener nofollow">
							<?php echo esc_html( (string) ( get_field( 'external_label' ) ?: __( 'Ver projeto', 'melqui-digital' ) ) ); ?>
							<span aria-hidden="true">&#8599;</span>
						</a>
					<?php endif; ?>
				</div>

				<dl class="pj-hero__meta">
					<?php if ( '' !== $md_role ) : ?>
						<div>
							<dt><?php esc_html_e( 'Papel', 'melqui-digital' ); ?></dt>
							<dd><?php echo esc_html( $md_role ); ?></dd>
						</div>
					<?php endif; ?>

					<?php if ( '' !== $md_context ) : ?>
						<div>
							<dt><?php esc_html_e( 'Contexto', 'melqui-digital' ); ?></dt>
							<dd><?php echo esc_html( $md_context ); ?></dd>
						</div>
					<?php endif; ?>

					<?php if ( '' !== $md_year ) : ?>
						<div>
							<dt><?php esc_html_e( 'Período', 'melqui-digital' ); ?></dt>
							<dd><?php echo esc_html( $md_year ); ?></dd>
						</div>
					<?php endif; ?>

					<?php if ( '' !== $md_status ) : ?>
						<div>
							<dt><?php esc_html_e( 'Status', 'melqui-digital' ); ?></dt>
							<dd><?php echo esc_html( $md_status ); ?></dd>
						</div>
					<?php endif; ?>

					<?php if ( '' !== $md_engagement ) : ?>
						<div>
							<dt><?php esc_html_e( 'Engajamento', 'melqui-digital' ); ?></dt>
							<dd><?php echo esc_html( $md_engagement ); ?></dd>
						</div>
					<?php endif; ?>

					<?php if ( $md_stack && ! is_wp_error( $md_stack ) ) : ?>
						<div>
							<dt><?php esc_html_e( 'Stack', 'melqui-digital' ); ?></dt>
							<dd><?php echo esc_html( implode( ' · ', wp_list_pluck( $md_stack, 'name' ) ) ); ?></dd>
						</div>
					<?php endif; ?>
				</dl>
			</div>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="pj-figure">
				<div class="container pj-figure__frame">
					<?php the_post_thumbnail( 'full', array( 'fetchpriority' => 'high', 'loading' => 'eager' ) ); ?>
				</div>

				<?php if ( get_field( 'image_note' ) ) : ?>
					<figcaption class="container"><?php echo esc_html( (string) get_field( 'image_note' ) ); ?></figcaption>
				<?php endif; ?>
			</figure>
		<?php endif; ?>

		<?php md_render_project_gallery( $md_post_id ); ?>

		<?php if ( is_array( $md_facts ) && $md_facts ) : ?>
			<section class="pj-facts" aria-label="<?php esc_attr_e( 'Ficha técnica', 'melqui-digital' ); ?>">
				<dl class="container">
					<?php foreach ( $md_facts as $md_fact ) : ?>
						<div>
							<dt><?php echo esc_html( (string) $md_fact['label'] ); ?></dt>
							<dd><?php echo esc_html( (string) $md_fact['value'] ); ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
			</section>
		<?php endif; ?>

		<div class="section">
			<div class="container pj-body<?php echo $md_section_count > 1 ? ' pj-body--split' : ''; ?>">
				<?php if ( $md_section_count > 1 ) : ?>
					<nav class="pj-toc" aria-labelledby="nesta-pagina">
						<p class="eyebrow" id="nesta-pagina"><?php esc_html_e( 'Nesta página', 'melqui-digital' ); ?></p>

						<ol>
							<?php foreach ( $md_sections as $md_section ) : ?>
								<li>
									<a href="#<?php echo esc_attr( sanitize_title( (string) $md_section['title'] ) ); ?>">
										<?php echo esc_html( (string) $md_section['title'] ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ol>
					</nav>
				<?php endif; ?>

				<div class="pj-sections<?php echo $md_section_count > 1 ? ' pj-sections--numbered' : ''; ?>">
					<?php if ( $md_section_count ) : ?>
						<?php foreach ( $md_sections as $md_section ) : ?>
							<section class="pj-section">
								<h2 id="<?php echo esc_attr( sanitize_title( (string) $md_section['title'] ) ); ?>">
									<?php echo esc_html( (string) $md_section['title'] ); ?>
								</h2>

								<?php echo wp_kses_post( (string) $md_section['body'] ); ?>
							</section>
						<?php endforeach; ?>
					<?php else : ?>
						<p><?php esc_html_e( 'Case study em preparação.', 'melqui-digital' ); ?></p>
					<?php endif; ?>

					<?php if ( get_the_content() ) : ?>
						<div class="prose-article"><?php the_content(); ?></div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<?php
		get_template_part(
			'template-parts/cta-next',
			null,
			array(
				'title' => md_option( 'next_project_title' ) ?: null,
				'text'  => md_option( 'next_project_text' ) ?: null,
			)
		);
		?>
	</article>

	<?php
endwhile;

get_footer();
