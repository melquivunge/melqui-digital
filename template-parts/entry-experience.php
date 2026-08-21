<?php
/**
 * One career entry in the about timeline.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

$md_points = get_field( 'points' );
?>
<li class="timeline__entry">
	<p class="timeline__period"><?php echo esc_html( (string) get_field( 'period' ) ); ?></p>

	<div>
		<h3><?php echo esc_html( (string) get_field( 'role' ) ); ?></h3>
		<p class="eyebrow"><?php echo esc_html( trim( get_field( 'org' ) . ' · ' . get_field( 'place' ), ' ·' ) ); ?></p>
		<p><?php echo esc_html( (string) get_field( 'summary' ) ); ?></p>
	</div>

	<?php if ( is_array( $md_points ) && $md_points ) : ?>
		<ul class="item-list">
			<?php foreach ( $md_points as $md_point ) : ?>
				<?php if ( empty( $md_point['item'] ) ) : continue; endif; ?>
				<li><?php echo esc_html( (string) $md_point['item'] ); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</li>
