<?php
/**
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e( 'Ir para o conteúdo', 'melqui-digital' ); ?></a>

<header class="site-header">
	<div class="container site-header__inner">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php
			$md_logo = function_exists( 'get_field' ) ? get_field( 'brand_logo', 'option' ) : 0;

			if ( $md_logo ) {
				echo wp_get_attachment_image( (int) $md_logo, 'thumbnail', false, array( 'class' => 'brand__mark', 'alt' => '' ) );
			}
			?>

			<span class="brand__text">
				<span class="brand__name"><?php bloginfo( 'name' ); ?></span>
				<?php if ( md_option( 'brand_tagline' ) ) : ?>
					<span class="brand__tagline"><?php echo esc_html( md_option( 'brand_tagline' ) ); ?></span>
				<?php endif; ?>
			</span>
		</a>

		<button class="nav-toggle" type="button" data-nav-toggle hidden
			aria-expanded="true" aria-controls="site-nav">
			<span class="screen-reader-text" data-label-open="<?php esc_attr_e( 'Abrir menu', 'melqui-digital' ); ?>" data-label-close="<?php esc_attr_e( 'Fechar menu', 'melqui-digital' ); ?>"><?php esc_html_e( 'Abrir menu', 'melqui-digital' ); ?></span>
			<span class="nav-toggle__bars" aria-hidden="true"></span>
		</button>

		<div id="site-nav" class="site-nav" data-open="true">
			<nav aria-label="<?php esc_attr_e( 'Menu principal', 'melqui-digital' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>

			<?php if ( md_option( 'cta_label' ) ) : ?>
				<a class="btn btn--dark site-header__cta" href="<?php echo esc_url( md_option( 'cta_url', home_url( '/contato/' ) ) ); ?>">
					<?php echo esc_html( md_option( 'cta_label' ) ); ?>
				</a>
			<?php endif; ?>
		</div>

	</div>
</header>

<main id="main">
