<?php
/**
 * Blog index. WordPress uses this for the posts page, so index.php is left as
 * the fallback for searches, taxonomies and anything unmatched.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

get_header();

$md_featured = md_featured_post();
?>

<header class="section page-hero">
	<div class="container">
		<?php md_archive_breadcrumb( __( 'Blog', 'melqui-digital' ) ); ?>

		<p class="eyebrow"><?php echo esc_html( md_option( 'arch_blog_eyebrow' ) ); ?></p>
		<h1 class="display-xl"><?php echo esc_html( md_option( 'arch_blog_title' ) ); ?></h1>
	</div>
</header>

<?php if ( $md_featured && ! is_paged() ) : ?>
	<section class="section section--bordered" aria-labelledby="destaque">
		<div class="container">
			<p class="eyebrow" id="destaque"><?php esc_html_e( 'Em destaque', 'melqui-digital' ); ?></p>

			<div class="feature">
				<div>
					<p class="eyebrow eyebrow--accent"><?php echo esc_html( md_post_meta( $md_featured->ID ) ); ?></p>

					<h2 class="feature__title">
						<a href="<?php echo esc_url( (string) get_permalink( $md_featured ) ); ?>"><?php echo esc_html( get_the_title( $md_featured ) ); ?></a>
					</h2>

					<p><?php echo esc_html( get_the_excerpt( $md_featured ) ); ?></p>
				</div>

				<?php if ( get_field( 'answer', $md_featured->ID ) ) : ?>
					<aside class="feature__answer">
						<p><?php echo esc_html( (string) get_field( 'answer', $md_featured->ID ) ); ?></p>
						<p class="eyebrow"><?php echo esc_html( get_the_date( '', $md_featured ) ); ?></p>
					</aside>
				<?php endif; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<section class="filter-bar">
	<div class="container filter-bar__inner">
		<?php md_term_filter( 'category', (string) get_permalink( (int) get_option( 'page_for_posts' ) ) ); ?>

		<?php get_search_form(); ?>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="post-grid">
				<?php
				while ( have_posts() ) :
					the_post();

					if ( $md_featured && get_the_ID() === $md_featured->ID && ! is_paged() ) {
						continue;
					}
					?>
					<article <?php post_class( 'post-card' ); ?>>
						<p class="eyebrow eyebrow--accent"><?php echo esc_html( md_post_meta( get_the_ID() ) ); ?></p>

						<h2 class="post-card__title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h2>

						<p><?php echo esc_html( get_the_excerpt() ); ?></p>

						<p class="eyebrow"><?php echo esc_html( get_the_date() ); ?></p>
					</article>
					<?php
				endwhile;
				?>
			</div>

			<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Nenhum artigo encontrado para esse filtro.', 'melqui-digital' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_template_part( 'template-parts/newsletter' );

get_footer();
