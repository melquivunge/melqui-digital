<?php
/**
 * Render the page section builder (flexible content field `sections`).
 *
 * The field group itself is in acf-json/group_md_builder.json.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

/**
 * Render every section of a page's builder.
 *
 * @param int $post_id Page holding the builder.
 */
function md_render_sections( int $post_id ): void {
	if ( ! function_exists( 'have_rows' ) || ! have_rows( 'sections', $post_id ) ) {
		return;
	}

	while ( have_rows( 'sections', $post_id ) ) {
		the_row();

		$layout = get_row_layout();

		// One template part per layout; an unknown layout renders nothing
		// rather than fataling.
		get_template_part( 'template-parts/sections/' . $layout );

		/**
		 * Lets a plugin insert its own band between two sections without
		 * knowing the composition in advance.
		 *
		 * @param string $layout Layout that just rendered.
		 */
		do_action( 'melqui_digital_after_section', $layout );
	}
}
