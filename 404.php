<?php
/**
 * 404.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="section">
	<div class="container">
		<p class="eyebrow">404</p>
		<h1 class="display-xl"><?php esc_html_e( 'Esta página não existe', 'melqui-digital' ); ?></h1>
		<p class="lede"><?php esc_html_e( 'O endereço pode ter mudado ou o conteúdo pode ter saído do ar.', 'melqui-digital' ); ?></p>

		<p>
			<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Voltar ao início', 'melqui-digital' ); ?></a>
			<a class="btn btn--ghost" href="<?php echo esc_url( (string) get_post_type_archive_link( 'md_project' ) ); ?>"><?php esc_html_e( 'Ver projetos', 'melqui-digital' ); ?></a>
		</p>
	</div>
</section>

<?php
get_footer();
