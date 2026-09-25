<?php
/**
 * Section: services, listed from the CPT.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

$md_query = new WP_Query(
	array(
		'post_type'      => 'md_service',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	)
);

if ( ! $md_query->have_posts() ) {
	return;
}
?>
<section class="section" aria-labelledby="servicos">
	<div class="container">
		<p class="eyebrow"><?php echo esc_html( (string) get_sub_field( 'eyebrow' ) ); ?></p>
		<h2 id="servicos" class="display-xl"><?php echo esc_html( (string) get_sub_field( 'title' ) ); ?></h2>

		<ol class="rows">
			<?php
			while ( $md_query->have_posts() ) :
				$md_query->the_post();
				?>
				<li class="rows__item">
					<span class="rows__n"><?php echo esc_html( (string) get_field( 'number' ) ); ?></span>

					<h3 class="rows__title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h3>

					<p class="rows__text"><?php echo esc_html( (string) get_field( 'tagline' ) ); ?></p>

					<span class="rows__arrow" aria-hidden="true">&#8599;</span>
				</li>
				<?php
			endwhile;
			?>
		</ol>
	</div>
</section>
<?php
wp_reset_postdata();
