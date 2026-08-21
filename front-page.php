<?php
/**
 * Front page: renders whatever sections the Home page is composed of.
 *
 * There is no hardcoded section order here — everything comes from the builder
 * on the page set as the front page.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

get_header();

$md_front = (int) get_option( 'page_on_front' );

if ( $md_front ) {
	md_render_sections( $md_front );
}

get_footer();
