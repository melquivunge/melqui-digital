<?php
/**
 * Service card.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;
?>
<article <?php post_class( 'card' ); ?>>
	<?php if ( get_field( 'number' ) ) : ?>
		<p class="eyebrow"><?php echo esc_html( (string) get_field( 'number' ) ); ?></p>
	<?php endif; ?>

	<h3 class="card__title">
		<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
	</h3>

	<p><?php echo esc_html( (string) get_field( 'tagline' ) ); ?></p>
</article>
