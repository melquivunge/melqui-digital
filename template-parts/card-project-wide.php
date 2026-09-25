<?php
/**
 * Wide project card: image, meta, stack and status. Used where a project is
 * referenced from another page.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

$md_stack = get_the_terms( get_the_ID(), 'md_stack' );
?>
<article <?php post_class( 'wide-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="wide-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php the_post_thumbnail( 'large', array( 'loading' => 'lazy' ) ); ?>
		</a>
	<?php endif; ?>

	<p class="eyebrow"><?php echo esc_html( md_project_label( get_the_ID() ) ); ?></p>

	<h3 class="wide-card__title">
		<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
	</h3>

	<p class="eyebrow"><?php echo esc_html( (string) get_field( 'context' ) ); ?></p>

	<p><?php echo esc_html( (string) get_field( 'impact' ) ); ?></p>

	<?php if ( $md_stack && ! is_wp_error( $md_stack ) ) : ?>
		<ul class="tags">
			<?php foreach ( $md_stack as $md_term ) : ?>
				<li><a href="<?php echo esc_url( (string) get_term_link( $md_term ) ); ?>"><?php echo esc_html( $md_term->name ); ?></a></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<p class="eyebrow eyebrow--accent"><?php echo esc_html( md_project_cta( get_the_ID() ) ); ?></p>

	<?php if ( get_field( 'image_note' ) ) : ?>
		<p class="wide-card__note"><?php echo esc_html( (string) get_field( 'image_note' ) ); ?></p>
	<?php endif; ?>
</article>
