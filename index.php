<?php
/**
 * Fallback template — archives, blog, search and anything without a more
 * specific template.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<h1 class="display-xl"><?php echo esc_html( md_archive_title() ); ?></h1>

			<div class="grid grid--3">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
<?php
					$md_part = array(
						'md_project' => 'project',
						'md_service' => 'service',
					)[ get_post_type() ] ?? 'post';

					get_template_part( 'template-parts/card', $md_part );
					?>
					<?php
				endwhile;
				?>
			</div>

			<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
		<?php else : ?>
			<h1 class="display-xl"><?php esc_html_e( 'Nada encontrado', 'melqui-digital' ); ?></h1>
			<p><?php esc_html_e( 'Nenhum conteúdo corresponde a esta consulta.', 'melqui-digital' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
