<?php
/**
 * Section: latest notes.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

$md_query = new WP_Query(
	array(
		'post_type'      => 'post',
		'posts_per_page' => 6,
		'no_found_rows'  => true,
	)
);

if ( ! $md_query->have_posts() ) {
	return;
}

$md_list = $md_query->posts;

// The flagged article leads the section; date order decides the rest.
usort(
	$md_list,
	static fn( WP_Post $a, WP_Post $b ): int =>
		(int) (bool) get_field( 'featured', $b->ID ) <=> (int) (bool) get_field( 'featured', $a->ID )
);

$md_featured = array_shift( $md_list );
$md_rail     = array_slice( $md_list, 0, 3 );
$md_tail     = array_slice( $md_list, 3 );
$md_blog     = (int) get_option( 'page_for_posts' );
?>
<section class="section notes" aria-labelledby="notas">
	<div class="container">
		<div class="section-head">
			<h2 id="notas" class="display-xl"><?php echo esc_html( (string) get_sub_field( 'title' ) ); ?></h2>

			<?php if ( $md_blog && get_sub_field( 'link_label' ) ) : ?>
				<a class="mono-link mono-link--muted" href="<?php echo esc_url( (string) get_permalink( $md_blog ) ); ?>">
					<?php echo esc_html( (string) get_sub_field( 'link_label' ) ); ?>
				</a>
			<?php endif; ?>
		</div>

		<div class="notes__top">
			<article class="notes__lead">
				<p class="eyebrow eyebrow--accent"><?php echo esc_html( md_post_meta( $md_featured->ID ) ); ?></p>

				<h3 class="notes__title">
					<a href="<?php echo esc_url( (string) get_permalink( $md_featured ) ); ?>"><?php echo esc_html( get_the_title( $md_featured ) ); ?></a>
				</h3>

				<p><?php echo esc_html( (string) ( get_field( 'answer', $md_featured->ID ) ?: get_the_excerpt( $md_featured ) ) ); ?></p>

				<p class="eyebrow"><?php echo esc_html( get_the_date( '', $md_featured ) ); ?></p>
			</article>

			<?php if ( $md_rail ) : ?>
				<ul class="notes__rail">
					<?php foreach ( $md_rail as $md_post ) : ?>
						<li>
							<?php $md_cats = get_the_category( $md_post->ID ); ?>
							<p class="eyebrow"><?php echo esc_html( $md_cats ? $md_cats[0]->name : '' ); ?></p>
							<a href="<?php echo esc_url( (string) get_permalink( $md_post ) ); ?>"><?php echo esc_html( get_the_title( $md_post ) ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<?php if ( $md_tail ) : ?>
			<ul class="notes__tail">
				<?php foreach ( $md_tail as $md_post ) : ?>
					<li>
						<?php $md_cats = get_the_category( $md_post->ID ); ?>
						<p class="eyebrow"><?php echo esc_html( $md_cats ? $md_cats[0]->name : '' ); ?></p>
						<a href="<?php echo esc_url( (string) get_permalink( $md_post ) ); ?>"><?php echo esc_html( get_the_title( $md_post ) ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
<?php
wp_reset_postdata();
