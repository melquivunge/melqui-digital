<?php
/**
 * Contact form: rendering, server-side validation, storage and notification.
 *
 * The React version validated in the browser only. That is a UX affordance, not
 * a trust boundary — every rule is re-checked here, and the select values are
 * matched against an allowlist rather than trusted as submitted.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

const MD_CONTACT_ACTION   = 'md_contact';
const MD_CONTACT_MAX_HOUR = 3;
const MD_CONTACT_MIN_SECS = 3;

/**
 * Allowed project types. Submitted values must match one of these exactly.
 *
 * @return array<int,string>
 */
function md_contact_types(): array {
	return array(
		'Site institucional',
		'WordPress / tema customizado',
		'E-commerce',
		'Aplicação web',
		'Performance & SEO',
		'Outro',
	);
}

/**
 * Allowed budget ranges.
 *
 * @return array<int,string>
 */
function md_contact_budgets(): array {
	return array(
		'Até R$ 10.000',
		'R$ 10.000 – R$ 30.000',
		'R$ 30.000 – R$ 80.000',
		'Acima de R$ 80.000',
		'Ainda não definido',
	);
}

/**
 * Messages are stored so a wp_mail failure never loses a lead. Not public, and
 * readable only by users who can manage options.
 */
add_action(
	'init',
	static function (): void {
		register_post_type(
			'md_message',
			array(
				'label'              => __( 'Mensagens', 'melqui-digital' ),
				'labels'             => array(
					'name'          => __( 'Mensagens', 'melqui-digital' ),
					'singular_name' => __( 'Mensagem', 'melqui-digital' ),
				),
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_rest'       => false,
				'has_archive'        => false,
				'exclude_from_search' => true,
				'menu_icon'          => 'dashicons-email',
				'menu_position'      => 26,
				'supports'           => array( 'title', 'editor' ),
				'capability_type'    => 'post',
				'capabilities'       => array(
					'create_posts' => 'do_not_allow',
				),
				'map_meta_cap'       => true,
			)
		);
	}
);

/**
 * A coarse client fingerprint for rate limiting. The address is hashed so no
 * raw IP is persisted.
 */
function md_contact_client_key(): string {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? (string) wp_unslash( $_SERVER['REMOTE_ADDR'] ) : '';

	// ponytail: REMOTE_ADDR only. Behind a proxy or CDN this is the edge address
	// and the limit becomes global — switch to the proxy's trusted client header
	// when one is actually in front of this site.
	return 'md_contact_' . hash( 'sha256', $ip . wp_salt() );
}

/**
 * Read, sanitise and validate the submission.
 *
 * @param array<string,mixed> $raw Raw request data.
 * @return array{values:array<string,string>,errors:array<string,string>}
 */
function md_contact_validate( array $raw ): array {
	$values = array(
		'nome'      => sanitize_text_field( (string) ( $raw['nome'] ?? '' ) ),
		'email'     => sanitize_email( (string) ( $raw['email'] ?? '' ) ),
		'empresa'   => sanitize_text_field( (string) ( $raw['empresa'] ?? '' ) ),
		'tipo'      => sanitize_text_field( (string) ( $raw['tipo'] ?? '' ) ),
		'orcamento' => sanitize_text_field( (string) ( $raw['orcamento'] ?? '' ) ),
		'mensagem'  => sanitize_textarea_field( (string) ( $raw['mensagem'] ?? '' ) ),
	);

	// Length caps applied before anything is stored or mailed.
	$limits = array(
		'nome'     => 100,
		'email'    => 200,
		'empresa'  => 120,
		'mensagem' => 5000,
	);

	foreach ( $limits as $field => $max ) {
		$values[ $field ] = mb_substr( $values[ $field ], 0, $max );
	}

	$errors = array();

	// sanitize_email() strips illegal characters rather than rejecting, so
	// "evil@x.com\nBcc: victim@target.com" becomes the syntactically valid
	// "evil@x.comBccvictimtarget.com". The newline is gone either way — header
	// injection is already dead — but a silently rewritten address is a bad
	// lead, so treat any rewrite as invalid input.
	$email_raw = trim( (string) ( $raw['email'] ?? '' ) );

	if ( '' !== $email_raw && $email_raw !== $values['email'] ) {
		$errors['email'] = __( 'Informe um e-mail válido.', 'melqui-digital' );
	}

	if ( mb_strlen( trim( $values['nome'] ) ) < 2 ) {
		$errors['nome'] = __( 'Informe seu nome.', 'melqui-digital' );
	}

	if ( ! is_email( $values['email'] ) ) {
		$errors['email'] = __( 'Informe um e-mail válido.', 'melqui-digital' );
	}

	if ( ! in_array( $values['tipo'], md_contact_types(), true ) ) {
		$errors['tipo'] = __( 'Selecione o tipo de projeto.', 'melqui-digital' );
	}

	if ( '' !== $values['orcamento'] && ! in_array( $values['orcamento'], md_contact_budgets(), true ) ) {
		$errors['orcamento'] = __( 'Selecione uma faixa de orçamento válida.', 'melqui-digital' );
	}

	if ( mb_strlen( trim( $values['mensagem'] ) ) < 20 ) {
		$errors['mensagem'] = __( 'Descreva o projeto com pelo menos 20 caracteres.', 'melqui-digital' );
	}

	return array(
		'values' => $values,
		'errors' => $errors,
	);
}

/**
 * Handle the submission. Bound to both logged-out and logged-in visitors.
 */
function md_contact_handle(): void {
	$redirect = wp_get_referer() ?: home_url( '/contato/' );

	if ( ! isset( $_POST['md_contact_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['md_contact_nonce'] ) ), MD_CONTACT_ACTION ) ) {
		wp_safe_redirect( add_query_arg( 'contato', 'expirado', $redirect ) );
		exit;
	}

	// Honeypot: a field hidden from people, irresistible to bots. Answer as if
	// it worked so the bot gets no signal.
	if ( ! empty( $_POST['md_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'contato', 'ok', $redirect ) );
		exit;
	}

	// Anything submitted faster than a human can type is not a human.
	$started = isset( $_POST['md_started'] ) ? (int) $_POST['md_started'] : 0;

	if ( $started > 0 && ( time() - $started ) < MD_CONTACT_MIN_SECS ) {
		wp_safe_redirect( add_query_arg( 'contato', 'ok', $redirect ) );
		exit;
	}

	$key   = md_contact_client_key();
	$count = (int) get_transient( $key );

	if ( $count >= MD_CONTACT_MAX_HOUR ) {
		wp_safe_redirect( add_query_arg( 'contato', 'limite', $redirect ) );
		exit;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified above.
	$result = md_contact_validate( wp_unslash( $_POST ) );

	if ( $result['errors'] ) {
		// Errors and previous input travel in a short-lived transient rather
		// than the URL, so nothing user-supplied is ever reflected in a link.
		$token = wp_generate_password( 20, false );
		set_transient( 'md_contact_fail_' . $token, $result, 5 * MINUTE_IN_SECONDS );

		wp_safe_redirect(
			add_query_arg(
				array(
					'contato' => 'erro',
					'ref'     => $token,
				),
				$redirect
			) . '#contato-form'
		);
		exit;
	}

	set_transient( $key, $count + 1, HOUR_IN_SECONDS );

	$values = $result['values'];

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'md_message',
			'post_status'  => 'private',
			/* translators: 1: sender name, 2: project type. */
			'post_title'   => sprintf( __( '%1$s — %2$s', 'melqui-digital' ), $values['nome'], $values['tipo'] ),
			'post_content' => $values['mensagem'],
		),
		true
	);

	if ( ! is_wp_error( $post_id ) ) {
		foreach ( array( 'email', 'empresa', 'tipo', 'orcamento' ) as $field ) {
			update_post_meta( (int) $post_id, '_md_' . $field, $values[ $field ] );
		}
	}

	md_contact_notify( $values );

	wp_safe_redirect( add_query_arg( 'contato', 'ok', $redirect ) . '#contato-form' );
	exit;
}

add_action( 'admin_post_nopriv_' . MD_CONTACT_ACTION, 'md_contact_handle' );
add_action( 'admin_post_' . MD_CONTACT_ACTION, 'md_contact_handle' );

/**
 * Email the site owner. From: stays on this domain — only Reply-To carries the
 * sender, so the message cannot spoof another origin.
 *
 * @param array<string,string> $values Validated values.
 */
function md_contact_notify( array $values ): void {
	$to = md_option( 'contact_email', (string) get_option( 'admin_email' ) );

	if ( ! is_email( $to ) ) {
		return;
	}

	$body = sprintf(
		"Nome: %s\nE-mail: %s\nEmpresa: %s\nTipo: %s\nOrçamento: %s\n\n%s\n",
		$values['nome'],
		$values['email'],
		$values['empresa'] ?: '—',
		$values['tipo'],
		$values['orcamento'] ?: '—',
		$values['mensagem']
	);

	wp_mail(
		$to,
		sprintf( '[%s] %s', get_bloginfo( 'name' ), $values['tipo'] ),
		$body,
		array( 'Reply-To: ' . $values['nome'] . ' <' . $values['email'] . '>' )
	);
}

/* -------------------------------------------------------------------------- */
/* Newsletter                                                                  */
/* -------------------------------------------------------------------------- */

const MD_SUB_ACTION = 'md_subscribe';

/**
 * Subscribers are stored locally until a provider is wired in. Nothing is
 * discarded, and the list can be exported at any point.
 */
add_action(
	'init',
	static function (): void {
		register_post_type(
			'md_subscriber',
			array(
				'label'               => __( 'Inscritos', 'melqui-digital' ),
				'labels'              => array(
					'name'          => __( 'Inscritos', 'melqui-digital' ),
					'singular_name' => __( 'Inscrito', 'melqui-digital' ),
				),
				'public'              => false,
				'publicly_queryable'  => false,
				'show_ui'             => true,
				'show_in_rest'        => false,
				'has_archive'         => false,
				'exclude_from_search' => true,
				'menu_icon'           => 'dashicons-megaphone',
				'menu_position'       => 27,
				'supports'            => array( 'title' ),
				'capability_type'     => 'post',
				'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'        => true,
			)
		);
	}
);

/**
 * Handle a newsletter signup. Same defences as the contact form.
 *
 * ponytail: stores locally. Swap the wp_insert_post call for the provider's
 * API when one is configured; the validation above stays as is.
 */
function md_subscribe_handle(): void {
	$redirect = wp_get_referer() ?: home_url( '/' );

	if ( ! isset( $_POST['md_sub_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['md_sub_nonce'] ) ), MD_SUB_ACTION ) ) {
		wp_safe_redirect( add_query_arg( 'assinar', 'expirado', $redirect ) );
		exit;
	}

	if ( ! empty( $_POST['md_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'assinar', 'ok', $redirect ) . '#newsletter' );
		exit;
	}

	$key   = md_contact_client_key() . '_sub';
	$count = (int) get_transient( $key );

	if ( $count >= MD_CONTACT_MAX_HOUR ) {
		wp_safe_redirect( add_query_arg( 'assinar', 'limite', $redirect ) . '#newsletter' );
		exit;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified above.
	$raw   = trim( (string) wp_unslash( $_POST['md_sub_email'] ?? '' ) );
	$email = sanitize_email( $raw );

	// As in the contact form: a rewritten address is invalid input, not a fix.
	if ( '' === $raw || $raw !== $email || ! is_email( $email ) ) {
		wp_safe_redirect( add_query_arg( 'assinar', 'invalido', $redirect ) . '#newsletter' );
		exit;
	}

	set_transient( $key, $count + 1, HOUR_IN_SECONDS );

	$existing = get_posts(
		array(
			'post_type'      => 'md_subscriber',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'title'          => $email,
		)
	);

	if ( ! $existing ) {
		wp_insert_post(
			array(
				'post_type'   => 'md_subscriber',
				'post_status' => 'private',
				'post_title'  => $email,
			)
		);
	}

	wp_safe_redirect( add_query_arg( 'assinar', 'ok', $redirect ) . '#newsletter' );
	exit;
}

add_action( 'admin_post_nopriv_' . MD_SUB_ACTION, 'md_subscribe_handle' );
add_action( 'admin_post_' . MD_SUB_ACTION, 'md_subscribe_handle' );
