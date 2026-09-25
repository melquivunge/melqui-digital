<?php
/**
 * Generic page.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>

	<article <?php post_class(); ?>>
		<header class="section page-hero">
			<div class="container">
				<h1 class="display-xl"><?php the_title(); ?></h1>

				<?php if ( has_excerpt() ) : ?>
					<p class="page-hero__lede"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
			</div>
		</header>

		<div class="section">
			<div class="container">
				<div class="prose-article"><?php the_content(); ?></div>
			</div>
		</div>

		<?php md_render_sections( get_the_ID() ); ?>
	</article>

	<?php
endwhile;

get_footer();
