<?php
/**
 * ACF Pro: options page and JSON sync.
 *
 * Field groups live in acf-json/. Edit them in WP admin; ACF writes the JSON
 * back into the theme. Keys stay stable so existing post meta keeps working.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

add_filter(
	'acf/settings/save_json',
	static function ( string $path ): string {
		// Local (WP_DEBUG): write into the theme so edits can be committed.
		// Production: do not write the theme directory. Load still comes from
		// acf-json/; the next deploy is the source of truth.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			return MD_DIR . '/acf-json';
		}

		return MD_DIR . '/acf-json-disabled';
	}
);

add_filter(
	'acf/settings/load_json',
	static function ( array $paths ): array {
		$paths[] = MD_DIR . '/acf-json';
		return array_values( array_unique( $paths ) );
	}
);

add_action(
	'acf/init',
	static function (): void {
		if ( ! function_exists( 'acf_add_options_page' ) ) {
			return;
		}

		acf_add_options_page(
			array(
				'page_title' => __( 'Opções do site', 'melqui-digital' ),
				'menu_title' => __( 'Opções do site', 'melqui-digital' ),
				'menu_slug'  => 'md-site-options',
				'capability' => 'manage_options',
				'icon_url'   => 'dashicons-admin-settings',
				'position'   => 59,
				'redirect'   => false,
			)
		);
	}
);
