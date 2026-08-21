<?php
/**
 * About page. The narrative is edited in the block editor; the timeline, the
 * principles and the stack come from the content model so they never drift
 * from the rest of the site.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

get_header();

$md_portrait   = function_exists( 'get_field' ) ? get_field( 'hero_portrait', 'option' ) : 0;
$md_principles = function_exists( 'get_field' ) ? get_field( 'principles', 'option' ) : array();
$md_groups     = function_exists( 'get_field' ) ? get_field( 'capability_groups', 'option' ) : array();
$md_education  = function_exists( 'get_field' ) ? get_field( 'education', 'option' ) : array();

while ( have_posts() ) :
	the_post();
	?>

	<article <?php post_class(); ?>>
		<header class="pj-hero about-hero">
			<div class="pj-hero__grid" aria-hidden="true"></div>

			<div class="container pj-hero__inner">
				<div>
					<?php md_breadcrumbs( '', '', __( 'Sobre', 'melqui-digital' ) ); ?>

					<p class="eyebrow eyebrow--accent"><?php echo esc_html( md_option( 'about_eyebrow' ) ); ?></p>

					<h1 class="display-xl"><?php the_title(); ?></h1>

					<?php foreach ( array_filter( preg_split( '/\R{2,}/', md_option( 'about_text' ) ) ) as $md_paragraph ) : ?>
						<p class="about-hero__text"><?php echo esc_html( trim( $md_paragraph ) ); ?></p>
					<?php endforeach; ?>
				</div>

				<?php if ( $md_portrait ) : ?>
					<figure class="about-hero__portrait">
						<?php
						echo wp_get_attachment_image(
							(int) $md_portrait,
							'large',
							false,
							array( 'loading' => 'eager', 'fetchpriority' => 'high' )
						);
						?>
						<figcaption class="eyebrow"><?php echo esc_html( md_option( 'hero_meta' ) ); ?></figcaption>
					</figure>
				<?php endif; ?>
			</div>
		</header>

		<section class="section" aria-labelledby="progressao">
			<div class="container two-col">
				<div>
					<p class="eyebrow"><?php echo esc_html( md_option( 'progress_eyebrow' ) ); ?></p>
					<h2 id="progressao" class="about-h2"><?php echo esc_html( md_option( 'progress_title' ) ); ?></h2>
				</div>

				<div class="about-prose"><?php the_content(); ?></div>
			</div>
		</section>
	</article>

	<?php
endwhile;

$md_career = new WP_Query(
	array(
		'post_type'      => 'md_experience',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	)
);

if ( $md_career->have_posts() ) :
	?>
	<section class="section section--muted" aria-labelledby="linha-do-tempo">
		<div class="container">
			<p class="eyebrow"><?php echo esc_html( md_option( 'xp_eyebrow' ) ); ?></p>
			<h2 id="linha-do-tempo" class="about-h2"><?php echo esc_html( md_option( 'xp_title' ) ); ?></h2>

			<ol class="timeline">
				<?php
				while ( $md_career->have_posts() ) :
					$md_career->the_post();
					get_template_part( 'template-parts/entry', 'experience' );
				endwhile;
				?>
			</ol>
		</div>
	</section>
	<?php
	wp_reset_postdata();
endif;

if ( is_array( $md_principles ) && $md_principles ) :
	?>
	<section class="section" aria-labelledby="principios">
		<div class="container two-col">
			<div>
				<p class="eyebrow"><?php echo esc_html( md_option( 'principles_eyebrow' ) ); ?></p>
				<h2 id="principios" class="about-h2"><?php echo esc_html( md_option( 'principles_title' ) ); ?></h2>
			</div>

			<dl class="principles">
				<?php foreach ( $md_principles as $md_item ) : ?>
					<div>
						<dt><?php echo esc_html( (string) $md_item['title'] ); ?></dt>
						<dd><?php echo esc_html( (string) $md_item['text'] ); ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>
		</div>
	</section>
	<?php
endif;

if ( is_array( $md_groups ) && $md_groups ) :
	?>
	<section class="section section--bordered" aria-labelledby="stack">
		<div class="container">
			<p class="eyebrow"><?php echo esc_html( md_option( 'stackfund_eyebrow' ) ); ?></p>
			<h2 id="stack" class="about-h2"><?php echo esc_html( md_option( 'stackfund_title' ) ); ?></h2>

			<dl class="stack-rows">
				<?php foreach ( $md_groups as $md_group ) : ?>
					<div>
						<dt><?php echo esc_html( (string) $md_group['area'] ); ?></dt>
						<dd>
							<?php
							$md_items = array();

							foreach ( $md_group['items'] ?? array() as $md_row ) {
								if ( ! empty( $md_row['item'] ) ) {
									$md_items[] = (string) $md_row['item'];
								}
							}

							echo esc_html( implode( ' · ', $md_items ) );
							?>
						</dd>
					</div>
				<?php endforeach; ?>
			</dl>

			<?php if ( is_array( $md_education ) && $md_education ) : ?>
				<dl class="edu">
					<?php foreach ( $md_education as $md_fact ) : ?>
						<div>
							<dt><?php echo esc_html( (string) $md_fact['label'] ); ?></dt>
							<dd><?php echo esc_html( (string) $md_fact['value'] ); ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>
		</div>
	</section>
	<?php
endif;

get_template_part(
	'template-parts/cta-next',
	null,
	array(
		'title' => md_option( 'next_about_title' ) ?: null,
		'text'  => md_option( 'next_about_text' ) ?: null,
	)
);

get_footer();
