<?php
/**
 * Section: engineering at scale.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

$md_points = get_sub_field( 'points' );

if ( ! is_array( $md_points ) || ! $md_points ) {
	return;
}
?>
<section class="section scale" aria-labelledby="escala">
	<div class="scale__bg" aria-hidden="true"></div>

	<div class="container scale__inner">
		<div class="scale__head">
			<p class="eyebrow eyebrow--accent"><?php echo esc_html( (string) get_sub_field( 'eyebrow' ) ); ?></p>
			<h2 id="escala" class="display-xl"><?php echo esc_html( (string) get_sub_field( 'title' ) ); ?></h2>
			<p class="scale__lede"><?php echo esc_html( (string) get_sub_field( 'text' ) ); ?></p>
		</div>

		<ol class="scale__list">
			<?php foreach ( $md_points as $md_point ) : ?>
				<li class="scale__item">
					<p class="scale__n"><?php echo esc_html( (string) ( $md_point['n'] ?? '' ) ); ?></p>
					<h3><?php echo esc_html( (string) ( $md_point['title'] ?? '' ) ); ?></h3>
					<p><?php echo esc_html( (string) ( $md_point['text'] ?? '' ) ); ?></p>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
