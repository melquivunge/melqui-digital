<?php
/**
 * Project archive.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

get_header();

$md_oldwork = function_exists( 'get_field' ) ? get_field( 'oldwork_items', 'option' ) : array();
?>

<header class="pj-hero arch-hero">
	<div class="pj-hero__grid" aria-hidden="true"></div>

	<div class="container">
		<?php md_archive_breadcrumb( __( 'Projetos', 'melqui-digital' ) ); ?>

		<p class="eyebrow eyebrow--accent"><?php echo esc_html( md_option( 'arch_projects_eyebrow' ) ); ?></p>
		<h1 class="display-xl"><?php echo esc_html( md_option( 'arch_projects_title' ) ); ?></h1>
		<p class="pj-hero__lede"><?php echo esc_html( md_option( 'arch_projects_text' ) ); ?></p>
	</div>
</header>

<section class="section">
	<div class="container">
		<?php md_term_filter( 'md_project_category', (string) get_post_type_archive_link( 'md_project' ) ); ?>

		<?php if ( have_posts() ) : ?>
			<div class="stack">
				<?php
				$md_i = 0;

				while ( have_posts() ) :
					the_post();
					?>
					<article <?php post_class( 'stack__item ' . ( 0 === $md_i % 2 ? '' : 'stack__item--right' ) ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<a class="stack__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
								<?php the_post_thumbnail( 'large', array( 'loading' => $md_i > 1 ? 'lazy' : 'eager' ) ); ?>
							</a>
						<?php endif; ?>

						<div class="stack__body">
							<p class="eyebrow"><?php echo esc_html( md_project_label( get_the_ID() ) ); ?></p>

							<h2 class="stack__title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>

							<p class="eyebrow"><?php echo esc_html( (string) get_field( 'context' ) ); ?></p>

							<p><?php echo esc_html( (string) get_field( 'impact' ) ); ?></p>

							<?php
							$md_stack = get_the_terms( get_the_ID(), 'md_stack' );

							if ( $md_stack && ! is_wp_error( $md_stack ) ) :
								?>
								<ul class="tags">
									<?php foreach ( $md_stack as $md_term ) : ?>
										<li><a href="<?php echo esc_url( (string) get_term_link( $md_term ) ); ?>"><?php echo esc_html( $md_term->name ); ?></a></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>

							<p class="eyebrow eyebrow--accent"><?php echo esc_html( md_project_cta( get_the_ID() ) ); ?></p>

							<?php if ( get_field( 'image_note' ) ) : ?>
								<p class="stack__note"><?php echo esc_html( (string) get_field( 'image_note' ) ); ?></p>
							<?php endif; ?>
						</div>
					</article>
					<?php
					++$md_i;
				endwhile;
				?>
			</div>

			<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Nenhum projeto nesta categoria ainda.', 'melqui-digital' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php if ( is_array( $md_oldwork ) && $md_oldwork ) : ?>
	<section class="section section--muted" aria-labelledby="arquivo">
		<div class="container two-col">
			<div>
				<p class="eyebrow"><?php echo esc_html( md_option( 'oldwork_eyebrow' ) ); ?></p>
				<h2 id="arquivo" class="oldwork__title"><?php echo esc_html( md_option( 'oldwork_title' ) ); ?></h2>
				<p class="oldwork__text"><?php echo esc_html( md_option( 'oldwork_text' ) ); ?></p>
			</div>

			<ul class="oldwork">
				<?php foreach ( $md_oldwork as $md_row ) : ?>
					<?php if ( empty( $md_row['item'] ) ) : continue; endif; ?>
					<li><?php echo esc_html( (string) $md_row['item'] ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
<?php endif; ?>

<?php
get_template_part(
	'template-parts/cta-next',
	null,
	array(
		'title' => md_option( 'next_projects_title' ) ?: null,
		'text'  => md_option( 'next_projects_text' ) ?: null,
	)
);

get_footer();
