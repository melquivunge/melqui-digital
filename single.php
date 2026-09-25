<?php
/**
 * Single article. The direct answer is printed before the body so both readers
 * and answer engines get the conclusion first (GEO).
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$md_answer = get_field( 'answer' );
	?>

	<article <?php post_class( 'section' ); ?>>
		<div class="container">
			<?php md_breadcrumbs( __( 'Notas', 'melqui-digital' ), get_permalink( (int) get_option( 'page_for_posts' ) ) ?: home_url( '/' ) ); ?>

			<h1 class="display-xl"><?php the_title(); ?></h1>

			<p class="eyebrow">
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
				<?php if ( get_field( 'reading_time' ) ) : ?>
					· <?php echo esc_html( (string) get_field( 'reading_time' ) ); ?>
				<?php endif; ?>
			</p>

			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="post-cover">
					<?php the_post_thumbnail( 'full', array( 'fetchpriority' => 'high', 'loading' => 'eager' ) ); ?>
				</figure>
			<?php endif; ?>

			<?php if ( $md_answer ) : ?>
				<div class="answer-box">
					<p><?php echo esc_html( (string) $md_answer ); ?></p>
				</div>
			<?php endif; ?>

			<div class="prose-article">
				<?php
				the_content();

				if ( ! get_the_content() ) {
					printf( '<p>%s</p>', esc_html__( 'Artigo em preparação.', 'melqui-digital' ) );
				}
				?>
			</div>
		</div>
	</article>

	<?php
endwhile;

get_footer();
