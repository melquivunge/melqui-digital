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

	$md_facts    = get_field( 'facts' );
	$md_sections = get_field( 'sections' );
	$md_stack    = get_the_terms( get_the_ID(), 'md_stack' );
	?>

	<article <?php post_class(); ?>>
		<header class="pj-hero">
			<div class="pj-hero__grid" aria-hidden="true"></div>

			<div class="container pj-hero__inner">
				<div>
					<?php md_breadcrumbs( __( 'Projetos', 'melqui-digital' ), (string) get_post_type_archive_link( 'md_project' ) ); ?>

					<p class="eyebrow eyebrow--accent"><?php echo esc_html( md_project_label( get_the_ID() ) ); ?></p>

					<h1 class="display-xl"><?php the_title(); ?></h1>

					<p class="pj-hero__lede"><?php echo esc_html( (string) get_field( 'impact' ) ); ?></p>

					<?php if ( get_field( 'external_url' ) ) : ?>
						<a class="mono-link" href="<?php echo esc_url( (string) get_field( 'external_url' ) ); ?>" rel="noopener nofollow">
							<?php echo esc_html( (string) ( get_field( 'external_label' ) ?: __( 'Ver projeto', 'melqui-digital' ) ) ); ?>
							<span aria-hidden="true">&#8599;</span>
						</a>
					<?php endif; ?>
				</div>

				<dl class="pj-hero__meta">
					<?php if ( get_field( 'role' ) ) : ?>
						<div>
							<dt><?php esc_html_e( 'Papel', 'melqui-digital' ); ?></dt>
							<dd><?php echo esc_html( (string) get_field( 'role' ) ); ?></dd>
						</div>
					<?php endif; ?>

					<?php if ( get_field( 'context' ) ) : ?>
						<div>
							<dt><?php esc_html_e( 'Contexto', 'melqui-digital' ); ?></dt>
							<dd><?php echo esc_html( (string) get_field( 'context' ) ); ?></dd>
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
				<?php the_post_thumbnail( 'full', array( 'fetchpriority' => 'high', 'loading' => 'eager' ) ); ?>

				<?php if ( get_field( 'image_note' ) ) : ?>
					<figcaption class="container"><?php echo esc_html( (string) get_field( 'image_note' ) ); ?></figcaption>
				<?php endif; ?>
			</figure>
		<?php endif; ?>

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
			<div class="container pj-body">
				<?php if ( is_array( $md_sections ) && count( $md_sections ) > 1 ) : ?>
					<nav class="pj-toc" aria-labelledby="nesta-pagina">
						<p class="eyebrow" id="nesta-pagina"><?php esc_html_e( 'Nesta página', 'melqui-digital' ); ?></p>

						<ul>
							<?php foreach ( $md_sections as $md_section ) : ?>
								<li>
									<a href="#<?php echo esc_attr( sanitize_title( (string) $md_section['title'] ) ); ?>">
										<?php echo esc_html( (string) $md_section['title'] ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</nav>
				<?php endif; ?>

				<div class="pj-sections">
					<?php if ( is_array( $md_sections ) && $md_sections ) : ?>
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
