<?php
/**
 * Theme supports, menus and editor configuration.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	static function (): void {
		load_theme_textdomain( 'melqui-digital', MD_DIR . '/languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'editor-styles' );
		add_theme_support(
			'html5',
			array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
		);

		// The palette is defined in SCSS tokens; disable the editor's own so the
		// two never drift apart.
		add_theme_support( 'disable-custom-colors' );
		add_theme_support( 'disable-custom-font-sizes' );

		register_nav_menus(
			array(
				'primary' => __( 'Menu principal', 'melqui-digital' ),
				'footer'  => __( 'Menu do rodapé', 'melqui-digital' ),
			)
		);

		// Image size for project/case cards. Crop keeps card grids aligned.
		add_image_size( 'md-card', 720, 480, true );
	}
);

/**
 * Content width used by oEmbed and wide images.
 */
add_action(
	'after_setup_theme',
	static function (): void {
		$GLOBALS['content_width'] = 1216;
	},
	0
);
