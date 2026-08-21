<?php
/**
 * Section: capability groups. The groups live in the site options because the
 * about page renders the same data.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

$md_groups = function_exists( 'get_field' ) ? get_field( 'capability_groups', 'option' ) : array();

if ( ! is_array( $md_groups ) || ! $md_groups ) {
	return;
}
?>
<section class="section" aria-labelledby="capacidades">
	<div class="container">
		<p class="eyebrow"><?php echo esc_html( (string) get_sub_field( 'eyebrow' ) ); ?></p>
		<h2 id="capacidades" class="display-xl"><?php echo esc_html( (string) get_sub_field( 'title' ) ); ?></h2>

		<div class="caps">
			<?php foreach ( $md_groups as $md_group ) : ?>
				<div class="caps__group">
					<h3><?php echo esc_html( (string) $md_group['area'] ); ?></h3>

					<?php if ( ! empty( $md_group['lead'] ) ) : ?>
						<p><?php echo esc_html( (string) $md_group['lead'] ); ?></p>
					<?php endif; ?>

					<?php if ( ! empty( $md_group['items'] ) ) : ?>
						<ul class="caps__items">
							<?php foreach ( $md_group['items'] as $md_row ) : ?>
								<?php if ( empty( $md_row['item'] ) ) : continue; endif; ?>
								<li><?php echo esc_html( (string) $md_row['item'] ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
