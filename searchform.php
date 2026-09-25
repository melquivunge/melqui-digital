<?php
/**
 * Search form.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="md-search"><?php esc_html_e( 'Buscar artigos', 'melqui-digital' ); ?></label>

	<input type="search" id="md-search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>"
		placeholder="<?php esc_attr_e( 'Buscar artigos', 'melqui-digital' ); ?>">

	<input type="hidden" name="post_type" value="post">

	<button type="submit"><?php esc_html_e( 'Buscar', 'melqui-digital' ); ?></button>
</form>
