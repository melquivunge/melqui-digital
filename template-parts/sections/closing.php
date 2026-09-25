<?php
/**
 * Section: two ways to start a conversation.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

$md_email = md_option( 'contact_email' );
?>
<section class="section closing" aria-labelledby="conversa">
	<div class="container">
		<h2 id="conversa" class="display-xl closing__title"><?php echo esc_html( (string) get_sub_field( 'title' ) ); ?></h2>

		<div class="closing__cols">
			<div class="closing__col">
				<p class="eyebrow eyebrow--accent"><?php echo esc_html( (string) get_sub_field( 'a_eyebrow' ) ); ?></p>
				<h3><?php echo esc_html( (string) get_sub_field( 'a_title' ) ); ?></h3>
				<p><?php echo esc_html( (string) get_sub_field( 'a_text' ) ); ?></p>

				<a class="btn btn--light" href="<?php echo esc_url( md_option( 'cta_url', home_url( '/contato/' ) ) ); ?>">
					<?php echo esc_html( md_option( 'cta_label', __( 'Falar sobre um projeto', 'melqui-digital' ) ) ); ?>
					<span aria-hidden="true">&rarr;</span>
				</a>
			</div>

			<div class="closing__col">
				<p class="eyebrow eyebrow--accent"><?php echo esc_html( (string) get_sub_field( 'b_eyebrow' ) ); ?></p>
				<h3><?php echo esc_html( (string) get_sub_field( 'b_title' ) ); ?></h3>
				<p><?php echo esc_html( (string) get_sub_field( 'b_text' ) ); ?></p>

				<?php if ( $md_email ) : ?>
					<a class="link-arrow" href="<?php echo esc_url( 'mailto:' . $md_email ); ?>">
						<?php echo esc_html( $md_email ); ?> <span aria-hidden="true">&#8599;</span>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
