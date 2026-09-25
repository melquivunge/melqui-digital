<?php
/**
 * Closing call to action, shared by every internal template.
 *
 * Pass 'title' and 'text' in $args to override the site-wide copy.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

$md_title = $args['title'] ?? md_option( 'next_title' );
$md_text  = $args['text'] ?? md_option( 'next_text' );

if ( '' === $md_title ) {
	return;
}
?>
<section class="section next" aria-labelledby="proximo-passo">
	<div class="container next__inner">
		<div>
			<p class="eyebrow"><?php echo esc_html( md_option( 'next_eyebrow' ) ); ?></p>
			<h2 id="proximo-passo" class="next__title"><?php echo esc_html( $md_title ); ?></h2>
			<p class="next__text"><?php echo esc_html( $md_text ); ?></p>
		</div>

		<a class="btn btn--light" href="<?php echo esc_url( md_option( 'cta_url', home_url( '/contato/' ) ) ); ?>">
			<?php echo esc_html( md_option( 'next_label' ) ); ?> <span aria-hidden="true">&rarr;</span>
		</a>
	</div>
</section>
