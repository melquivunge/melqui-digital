<?php
/**
 * Article card.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

$md_terms = get_the_category();
?>
<article <?php post_class( 'card' ); ?>>
	<?php if ( $md_terms ) : ?>
		<p class="eyebrow"><?php echo esc_html( $md_terms[0]->name ); ?></p>
	<?php endif; ?>

	<h3 class="card__title">
		<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
	</h3>

	<p><?php echo esc_html( get_the_excerpt() ); ?></p>

	<p class="eyebrow">
		<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
		<?php if ( get_field( 'reading_time' ) ) : ?>
			· <?php echo esc_html( (string) get_field( 'reading_time' ) ); ?>
		<?php endif; ?>
	</p>
</article>
