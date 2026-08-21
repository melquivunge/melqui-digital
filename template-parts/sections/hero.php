<?php
/**
 * Section: hero.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

$md_portrait = get_sub_field( 'portrait' );
$md_proof    = get_sub_field( 'proof_rail' );
?>
<section class="hero">
	<div class="hero__grid" aria-hidden="true"></div>

	<div class="container hero__inner">
		<div class="hero__copy">
			<?php if ( get_sub_field( 'eyebrow' ) ) : ?>
				<p class="hero__eyebrow"><?php echo esc_html( (string) get_sub_field( 'eyebrow' ) ); ?></p>
			<?php endif; ?>

			<h1 class="hero__title"><?php echo esc_html( (string) get_sub_field( 'title' ) ); ?></h1>

			<?php if ( get_sub_field( 'text' ) ) : ?>
				<p class="hero__lede"><?php echo esc_html( (string) get_sub_field( 'text' ) ); ?></p>
			<?php endif; ?>

			<div class="hero__actions">
				<?php if ( get_sub_field( 'cta_label' ) ) : ?>
					<a class="btn btn--primary" href="<?php echo esc_url( (string) get_sub_field( 'cta_url' ) ); ?>">
						<?php echo esc_html( (string) get_sub_field( 'cta_label' ) ); ?> <span aria-hidden="true">&rarr;</span>
					</a>
				<?php endif; ?>

				<?php if ( get_sub_field( 'cta2_label' ) ) : ?>
					<a class="link-arrow" href="<?php echo esc_url( (string) get_sub_field( 'cta2_url' ) ); ?>">
						<?php echo esc_html( (string) get_sub_field( 'cta2_label' ) ); ?> <span aria-hidden="true">&rarr;</span>
					</a>
				<?php endif; ?>
			</div>

			<?php if ( get_sub_field( 'meta' ) ) : ?>
				<p class="hero__meta"><?php echo esc_html( (string) get_sub_field( 'meta' ) ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $md_portrait ) : ?>
			<figure class="hero__portrait">
				<?php
				// Above the fold: eager and prioritised as the LCP candidate.
				echo wp_get_attachment_image(
					(int) $md_portrait,
					'large',
					false,
					array( 'loading' => 'eager', 'fetchpriority' => 'high', 'decoding' => 'async' )
				);
				?>
				<figcaption>
					<span><?php bloginfo( 'name' ); ?></span>
					<span><?php echo esc_html( md_option( 'brand_tagline' ) ); ?></span>
				</figcaption>
			</figure>
		<?php endif; ?>
	</div>

	<?php if ( is_array( $md_proof ) && $md_proof ) : ?>
		<div class="container">
			<ul class="proof-rail">
				<?php foreach ( $md_proof as $md_row ) : ?>
					<?php if ( empty( $md_row['item'] ) ) : continue; endif; ?>
					<li><?php echo esc_html( (string) $md_row['item'] ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
</section>
