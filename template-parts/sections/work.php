<?php
/**
 * Section: selected work. Projects come from the CPT in menu order, so the
 * editor curates by dragging in the Projetos list.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

$md_lead_count = (int) ( get_sub_field( 'leads' ) ?: 2 );

$md_query = new WP_Query(
	array(
		'post_type'      => 'md_project',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	)
);

$md_all   = $md_query->posts;
$md_leads = array_slice( $md_all, 0, $md_lead_count );
$md_rest  = array_slice( $md_all, $md_lead_count );

if ( ! $md_all ) {
	return;
}
?>
<section class="section work" aria-labelledby="trabalho">
	<div class="container">
		<div class="section-head">
			<h2 id="trabalho" class="display-xl"><?php echo esc_html( (string) get_sub_field( 'title' ) ); ?></h2>

			<?php if ( get_sub_field( 'link_label' ) ) : ?>
				<a class="mono-link" href="<?php echo esc_url( (string) get_post_type_archive_link( 'md_project' ) ); ?>">
					<?php echo esc_html( (string) get_sub_field( 'link_label' ) ); ?> <span aria-hidden="true">&#8599;</span>
				</a>
			<?php endif; ?>
		</div>

		<?php if ( $md_leads ) : ?>
			<div class="work__leads">
				<?php foreach ( $md_leads as $md_index => $md_lead ) : ?>
					<article class="work-lead <?php echo 1 === $md_index ? 'work-lead--dark' : ''; ?>">
						<a class="work-lead__media" href="<?php echo esc_url( (string) get_permalink( $md_lead ) ); ?>" tabindex="-1" aria-hidden="true">
							<?php echo get_the_post_thumbnail( $md_lead, 'large', array( 'loading' => 'lazy' ) ); ?>
						</a>

						<div class="work-lead__body">
							<p class="eyebrow eyebrow--accent"><?php echo esc_html( md_project_label( $md_lead->ID ) ); ?></p>

							<h3 class="work-lead__title">
								<a href="<?php echo esc_url( (string) get_permalink( $md_lead ) ); ?>"><?php echo esc_html( get_the_title( $md_lead ) ); ?></a>
							</h3>

							<p><?php echo esc_html( (string) get_field( 'impact', $md_lead->ID ) ); ?></p>

							<a class="mono-link" href="<?php echo esc_url( (string) get_permalink( $md_lead ) ); ?>">
								<?php echo esc_html( md_project_cta( $md_lead->ID ) ); ?> <span aria-hidden="true">&#8599;</span>
							</a>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( $md_rest ) : ?>
			<div class="work__grid">
				<?php foreach ( $md_rest as $md_item ) : ?>
					<article class="work-card">
						<a class="work-card__media" href="<?php echo esc_url( (string) get_permalink( $md_item ) ); ?>" tabindex="-1" aria-hidden="true">
							<?php
							if ( has_post_thumbnail( $md_item ) ) {
								echo get_the_post_thumbnail( $md_item, 'md-card', array( 'loading' => 'lazy' ) );
							} else {
								$md_cat = get_the_terms( $md_item->ID, 'md_project_category' );

								printf(
									'<span class="work-card__placeholder">%s</span>',
									esc_html( $md_cat && ! is_wp_error( $md_cat ) ? $md_cat[0]->name : '' )
								);
							}
							?>
						</a>

						<p class="eyebrow"><?php echo esc_html( (string) get_field( 'engagement', $md_item->ID ) ); ?></p>

						<h3 class="work-card__title">
							<a href="<?php echo esc_url( (string) get_permalink( $md_item ) ); ?>"><?php echo esc_html( get_the_title( $md_item ) ); ?></a>
						</h3>

						<p><?php echo esc_html( wp_trim_words( (string) get_field( 'impact', $md_item->ID ), 30 ) ); ?></p>

						<p class="eyebrow eyebrow--accent"><?php echo esc_html( md_project_cta( $md_item->ID ) ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php
wp_reset_postdata();
