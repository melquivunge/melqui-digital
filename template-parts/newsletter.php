<?php
/**
 * Newsletter signup.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

if ( '' === md_option( 'news_title' ) ) {
	return;
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display state only.
$md_state = isset( $_GET['assinar'] ) ? sanitize_key( wp_unslash( $_GET['assinar'] ) ) : '';
?>
<section class="section section--muted newsletter" id="newsletter" aria-labelledby="assinar">
	<div class="container newsletter__inner">
		<div>
			<h2 id="assinar" class="newsletter__title"><?php echo esc_html( md_option( 'news_title' ) ); ?></h2>
			<p class="newsletter__text"><?php echo esc_html( md_option( 'news_text' ) ); ?></p>

			<?php if ( 'ok' === $md_state ) : ?>
				<p class="notice notice--ok" role="status"><?php esc_html_e( 'Inscrição registrada. Obrigado.', 'melqui-digital' ); ?></p>
			<?php elseif ( 'invalido' === $md_state ) : ?>
				<p class="notice notice--warn" role="alert"><?php esc_html_e( 'Informe um e-mail válido.', 'melqui-digital' ); ?></p>
			<?php elseif ( 'limite' === $md_state || 'expirado' === $md_state ) : ?>
				<p class="notice notice--warn" role="alert"><?php esc_html_e( 'Não foi possível registrar agora. Tente novamente mais tarde.', 'melqui-digital' ); ?></p>
			<?php endif; ?>
		</div>

		<form class="newsletter__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="<?php echo esc_attr( MD_SUB_ACTION ); ?>">
			<?php wp_nonce_field( MD_SUB_ACTION, 'md_sub_nonce' ); ?>

			<div class="form__trap" aria-hidden="true">
				<label for="md_website_sub"><?php esc_html_e( 'Não preencha este campo', 'melqui-digital' ); ?></label>
				<input type="text" id="md_website_sub" name="md_website" tabindex="-1" autocomplete="off" value="">
			</div>

			<label class="screen-reader-text" for="md_sub_email"><?php esc_html_e( 'Seu e-mail', 'melqui-digital' ); ?></label>
			<input type="email" id="md_sub_email" name="md_sub_email" required maxlength="200"
				placeholder="<?php esc_attr_e( 'seu@email.com', 'melqui-digital' ); ?>" autocomplete="email">

			<button type="submit" class="btn btn--primary"><?php esc_html_e( 'Assinar', 'melqui-digital' ); ?></button>
		</form>
	</div>
</section>
