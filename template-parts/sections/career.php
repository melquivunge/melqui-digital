<?php
/**
 * Section: career timeline.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

$md_query = new WP_Query(
	array(
		'post_type'      => 'md_experience',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	)
);

if ( ! $md_query->have_posts() ) {
	return;
}

$md_about = get_page_by_path( 'sobre' );
?>
<section class="section section--muted" aria-labelledby="carreira">
	<div class="container split">
		<div class="split__aside">
			<p class="eyebrow"><?php echo esc_html( (string) get_sub_field( 'eyebrow' ) ); ?></p>
			<h2 id="carreira" class="display-xl"><?php echo esc_html( (string) get_sub_field( 'title' ) ); ?></h2>
			<p><?php echo esc_html( (string) get_sub_field( 'text' ) ); ?></p>

			<?php if ( $md_about && get_sub_field( 'link_label' ) ) : ?>
				<a class="mono-link" href="<?php echo esc_url( (string) get_permalink( $md_about ) ); ?>">
					<?php echo esc_html( (string) get_sub_field( 'link_label' ) ); ?> <span aria-hidden="true">&rarr;</span>
				</a>
			<?php endif; ?>
		</div>

		<ol class="career">
			<?php
			while ( $md_query->have_posts() ) :
				$md_query->the_post();
				?>
				<li class="career__row">
					<p class="career__period"><?php echo esc_html( (string) get_field( 'period' ) ); ?></p>

					<div>
						<h3><?php echo esc_html( (string) get_field( 'role' ) ); ?></h3>
						<p class="career__org"><?php echo esc_html( trim( get_field( 'org' ) . ' · ' . get_field( 'place' ), ' ·' ) ); ?></p>
						<p><?php echo esc_html( (string) get_field( 'summary' ) ); ?></p>
					</div>
				</li>
				<?php
			endwhile;
			?>
		</ol>
	</div>
</section>
<?php
wp_reset_postdata();
