<?php
/**
 * Contact form. Plain POST to admin-post.php — it works with JavaScript
 * disabled, so there is no client-side code to keep in sync with the server
 * rules in inc/contact.php.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only display state.
$md_state = isset( $_GET['contato'] ) ? sanitize_key( wp_unslash( $_GET['contato'] ) ) : '';
$md_ref   = isset( $_GET['ref'] ) ? preg_replace( '/[^A-Za-z0-9]/', '', (string) wp_unslash( $_GET['ref'] ) ) : '';
// phpcs:enable

$md_values = array(
	'nome'      => '',
	'email'     => '',
	'empresa'   => '',
	'tipo'      => '',
	'orcamento' => '',
	'mensagem'  => '',
);
$md_errors = array();

if ( 'erro' === $md_state && $md_ref ) {
	$md_failed = get_transient( 'md_contact_fail_' . $md_ref );

	if ( is_array( $md_failed ) ) {
		$md_values = array_merge( $md_values, $md_failed['values'] );
		$md_errors = $md_failed['errors'];
		delete_transient( 'md_contact_fail_' . $md_ref );
	}
}

/**
 * Print the aria-describedby target for a field error.
 *
 * @param array<string,string> $errors Error bag.
 * @param string               $field  Field name.
 */
$md_error_for = static function ( array $errors, string $field ): void {
	if ( empty( $errors[ $field ] ) ) {
		return;
	}

	printf(
		'<p class="field__error" id="%s-erro">%s</p>',
		esc_attr( $field ),
		esc_html( $errors[ $field ] )
	);
};
?>

<div id="contato-form" class="contact">
	<?php if ( 'ok' === $md_state ) : ?>
		<p class="notice notice--ok" role="status">
			<?php esc_html_e( 'Mensagem enviada. Retorno normalmente em até dois dias úteis.', 'melqui-digital' ); ?>
		</p>
	<?php elseif ( 'limite' === $md_state ) : ?>
		<p class="notice notice--warn" role="alert">
			<?php esc_html_e( 'Você já enviou várias mensagens. Tente novamente mais tarde ou use o e-mail direto.', 'melqui-digital' ); ?>
		</p>
	<?php elseif ( 'expirado' === $md_state ) : ?>
		<p class="notice notice--warn" role="alert">
			<?php esc_html_e( 'A sessão do formulário expirou. Envie novamente.', 'melqui-digital' ); ?>
		</p>
	<?php elseif ( $md_errors ) : ?>
		<p class="notice notice--warn" role="alert">
			<?php esc_html_e( 'Confira os campos destacados abaixo.', 'melqui-digital' ); ?>
		</p>
	<?php endif; ?>

	<form class="form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
		<input type="hidden" name="action" value="<?php echo esc_attr( MD_CONTACT_ACTION ); ?>">
		<input type="hidden" name="md_started" value="<?php echo esc_attr( (string) time() ); ?>">
		<?php wp_nonce_field( MD_CONTACT_ACTION, 'md_contact_nonce' ); ?>

		<div class="form__trap" aria-hidden="true">
			<label for="md_website"><?php esc_html_e( 'Não preencha este campo', 'melqui-digital' ); ?></label>
			<input type="text" id="md_website" name="md_website" tabindex="-1" autocomplete="off" value="">
		</div>

		<div class="form__row">
			<div class="field">
				<label for="nome"><?php esc_html_e( 'Nome', 'melqui-digital' ); ?> <span aria-hidden="true">*</span></label>
				<input type="text" id="nome" name="nome" required maxlength="100"
					value="<?php echo esc_attr( $md_values['nome'] ); ?>"
					<?php echo isset( $md_errors['nome'] ) ? 'aria-invalid="true" aria-describedby="nome-erro"' : ''; ?>>
				<?php $md_error_for( $md_errors, 'nome' ); ?>
			</div>

			<div class="field">
				<label for="email"><?php esc_html_e( 'E-mail', 'melqui-digital' ); ?> <span aria-hidden="true">*</span></label>
				<input type="email" id="email" name="email" required maxlength="200" autocomplete="email"
					value="<?php echo esc_attr( $md_values['email'] ); ?>"
					<?php echo isset( $md_errors['email'] ) ? 'aria-invalid="true" aria-describedby="email-erro"' : ''; ?>>
				<?php $md_error_for( $md_errors, 'email' ); ?>
			</div>
		</div>

		<div class="form__row">
			<div class="field">
				<label for="empresa"><?php esc_html_e( 'Empresa', 'melqui-digital' ); ?></label>
				<input type="text" id="empresa" name="empresa" maxlength="120"
					value="<?php echo esc_attr( $md_values['empresa'] ); ?>">
				<p class="field__hint"><?php esc_html_e( 'Opcional', 'melqui-digital' ); ?></p>
			</div>

			<div class="field">
				<label for="tipo"><?php esc_html_e( 'Tipo de projeto', 'melqui-digital' ); ?> <span aria-hidden="true">*</span></label>
				<select id="tipo" name="tipo" required
					<?php echo isset( $md_errors['tipo'] ) ? 'aria-invalid="true" aria-describedby="tipo-erro"' : ''; ?>>
					<option value=""><?php esc_html_e( 'Selecione…', 'melqui-digital' ); ?></option>
					<?php foreach ( md_contact_types() as $md_type ) : ?>
						<option value="<?php echo esc_attr( $md_type ); ?>" <?php selected( $md_values['tipo'], $md_type ); ?>>
							<?php echo esc_html( $md_type ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<?php $md_error_for( $md_errors, 'tipo' ); ?>
			</div>
		</div>

		<div class="field">
			<label for="orcamento"><?php esc_html_e( 'Orçamento aproximado', 'melqui-digital' ); ?></label>
			<select id="orcamento" name="orcamento">
				<option value=""><?php esc_html_e( 'Selecione…', 'melqui-digital' ); ?></option>
				<?php foreach ( md_contact_budgets() as $md_budget ) : ?>
					<option value="<?php echo esc_attr( $md_budget ); ?>" <?php selected( $md_values['orcamento'], $md_budget ); ?>>
						<?php echo esc_html( $md_budget ); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<p class="field__hint"><?php esc_html_e( 'Opcional', 'melqui-digital' ); ?></p>
		</div>

		<div class="field">
			<label for="mensagem"><?php esc_html_e( 'Mensagem', 'melqui-digital' ); ?> <span aria-hidden="true">*</span></label>
			<textarea id="mensagem" name="mensagem" rows="7" required minlength="20" maxlength="5000"
				<?php echo isset( $md_errors['mensagem'] ) ? 'aria-invalid="true" aria-describedby="mensagem-erro"' : ''; ?>><?php echo esc_textarea( $md_values['mensagem'] ); ?></textarea>
			<?php $md_error_for( $md_errors, 'mensagem' ); ?>
			<p class="field__hint"><?php esc_html_e( 'Contexto do negócio, prazo e o que já existe hoje.', 'melqui-digital' ); ?></p>
		</div>

		<button type="submit" class="btn btn--primary"><?php esc_html_e( 'Enviar mensagem', 'melqui-digital' ); ?></button>
	</form>
</div>
