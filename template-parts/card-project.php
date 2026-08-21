<?php
/**
 * Project card. Used by the home, the project archive and the stack taxonomy.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;
?>
<article <?php post_class( 'card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php the_post_thumbnail( 'md-card', array( 'loading' => 'lazy' ) ); ?>
		</a>
	<?php endif; ?>

	<?php if ( get_field( 'context' ) ) : ?>
		<p class="eyebrow"><?php echo esc_html( (string) get_field( 'context' ) ); ?></p>
	<?php endif; ?>

	<h3 class="card__title">
		<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
	</h3>

	<p><?php echo esc_html( wp_trim_words( (string) get_field( 'impact' ), 28 ) ); ?></p>

	<?php if ( get_field( 'year' ) ) : ?>
		<p class="eyebrow"><?php echo esc_html( (string) get_field( 'year' ) ); ?></p>
	<?php endif; ?>
</article>
