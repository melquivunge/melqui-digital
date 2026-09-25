<?php
/**
 * Melqui Digital — bootstrap.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

define( 'MD_VERSION', '1.0.1' );
define( 'MD_DIR', get_template_directory() );
define( 'MD_URI', get_template_directory_uri() );

require_once MD_DIR . '/inc/setup.php';
require_once MD_DIR . '/inc/assets.php';
require_once MD_DIR . '/inc/post-types.php';
require_once MD_DIR . '/inc/fields.php';
require_once MD_DIR . '/inc/builder.php';
require_once MD_DIR . '/inc/template-tags.php';
require_once MD_DIR . '/inc/seo.php';
require_once MD_DIR . '/inc/contact.php';
