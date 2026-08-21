<?php
/**
 * Asset loading and front-end performance trimming.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

/**
 * Cache-bust from file mtime so browsers never hold a stale build.
 */
function md_asset_version( string $relative_path ): string {
	$file = MD_DIR . '/' . ltrim( $relative_path, '/' );

	return file_exists( $file ) ? (string) filemtime( $file ) : MD_VERSION;
}

add_action(
	'wp_enqueue_scripts',
	static function (): void {
		wp_enqueue_style(
			'melqui-digital',
			MD_URI . '/assets/css/main.css',
			array(),
			md_asset_version( 'assets/css/main.css' )
		);

		if ( file_exists( MD_DIR . '/assets/js/main.js' ) ) {
			wp_enqueue_script(
				'melqui-digital',
				MD_URI . '/assets/js/main.js',
				array(),
				md_asset_version( 'assets/js/main.js' ),
				array(
					'strategy'  => 'defer',
					'in_footer' => true,
				)
			);
		}

		// Gutenberg's stylesheet is only needed where post content renders.
		if ( ! is_singular( array( 'post', 'page' ) ) ) {
			wp_dequeue_style( 'wp-block-library' );
			wp_dequeue_style( 'wp-block-library-theme' );
			wp_dequeue_style( 'global-styles' );
		}

		wp_dequeue_style( 'classic-theme-styles' );
	},
	20
);

/**
 * Preconnect to the font host so the CSS request starts earlier.
 *
 * ponytail: Google Fonts kept to match the React app. Self-hosting removes a
 * third-party round trip — switch when the font list is final.
 */
add_filter(
	'wp_resource_hints',
	static function ( array $hints, string $relation ): array {
		if ( 'preconnect' === $relation ) {
			$hints[] = array(
				'href'        => 'https://fonts.gstatic.com',
				'crossorigin' => 'anonymous',
			);
			$hints[] = 'https://fonts.googleapis.com';
		}

		return $hints;
	},
	10,
	2
);

add_action(
	'wp_enqueue_scripts',
	static function (): void {
		wp_enqueue_style(
			'melqui-digital-fonts',
			'https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=IBM+Plex+Sans:wght@400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap',
			array(),
			null
		);
	},
	5
);

/**
 * Remove front-end payload the theme does not use.
 */
add_action(
	'init',
	static function (): void {
		if ( is_admin() ) {
			return;
		}

		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	}
);

/**
 * Site icon links.
 *
 * Not using the core Site Icon option: it needs an attachment in the database,
 * so it would not survive a fresh install of this theme. Shipping the files
 * with the theme keeps the icon versioned in git like everything else.
 */
add_action(
	'wp_head',
	static function (): void {
		// Someone set the icon in Customizer after all — core prints its own
		// tags then, and two sets of icon links is worse than either alone.
		if ( has_site_icon() ) {
			return;
		}

		$img = get_template_directory_uri() . '/assets/img/';

		printf(
			'<link rel="icon" href="%1$s" sizes="any">' . "\n"
			. '<link rel="icon" type="image/png" href="%2$s" sizes="192x192">' . "\n"
			. '<link rel="apple-touch-icon" href="%3$s">' . "\n",
			esc_url( home_url( '/favicon.ico' ) ),
			esc_url( $img . 'icon-192.png' ),
			esc_url( $img . 'apple-touch-icon.png' )
		);
	},
	2
);
