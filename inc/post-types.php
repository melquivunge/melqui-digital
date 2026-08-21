<?php
/**
 * Content model: custom post types and taxonomies.
 *
 * Only structures that WordPress does not already provide are registered here.
 * Posts stay native; categories, tags and stack are taxonomies rather than ACF
 * fields so archives, filtering and feeds come for free.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	static function (): void {
		register_post_type(
			'md_project',
			array(
				'label'         => __( 'Projetos', 'melqui-digital' ),
				'labels'        => array(
					'name'          => __( 'Projetos', 'melqui-digital' ),
					'singular_name' => __( 'Projeto', 'melqui-digital' ),
					'add_new_item'  => __( 'Adicionar projeto', 'melqui-digital' ),
					'edit_item'     => __( 'Editar projeto', 'melqui-digital' ),
				),
				'public'        => true,
				'has_archive'   => 'projetos',
				'rewrite'       => array(
					'slug'       => 'projetos',
					'with_front' => false,
				),
				'menu_icon'     => 'dashicons-portfolio',
				'menu_position' => 20,
				'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
				'show_in_rest'  => true,
			)
		);

		register_post_type(
			'md_service',
			array(
				'label'         => __( 'Serviços', 'melqui-digital' ),
				'labels'        => array(
					'name'          => __( 'Serviços', 'melqui-digital' ),
					'singular_name' => __( 'Serviço', 'melqui-digital' ),
					'add_new_item'  => __( 'Adicionar serviço', 'melqui-digital' ),
					'edit_item'     => __( 'Editar serviço', 'melqui-digital' ),
				),
				'public'        => true,
				'has_archive'   => 'servicos',
				'rewrite'       => array(
					'slug'       => 'servicos',
					'with_front' => false,
				),
				'menu_icon'     => 'dashicons-hammer',
				'menu_position' => 21,
				'supports'      => array( 'title', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
				'show_in_rest'  => true,
			)
		);

		// Career entries are not public URLs — they render inside the about and
		// home pages only.
		register_post_type(
			'md_experience',
			array(
				'label'              => __( 'Experiência', 'melqui-digital' ),
				'labels'             => array(
					'name'          => __( 'Experiência', 'melqui-digital' ),
					'singular_name' => __( 'Entrada de carreira', 'melqui-digital' ),
				),
				'public'             => false,
				'show_ui'            => true,
				'publicly_queryable' => false,
				'has_archive'        => false,
				'menu_icon'          => 'dashicons-businessperson',
				'menu_position'      => 22,
				'supports'           => array( 'title', 'revisions', 'page-attributes' ),
				'show_in_rest'       => true,
			)
		);

		register_taxonomy(
			'md_project_category',
			array( 'md_project' ),
			array(
				'label'             => __( 'Categorias de projeto', 'melqui-digital' ),
				'hierarchical'      => true,
				'public'            => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array(
					'slug'       => 'projetos/categoria',
					'with_front' => false,
				),
			)
		);

		// Shared across projects, services and career entries — this is exactly
		// why it is a taxonomy and not a repeater of strings.
		register_taxonomy(
			'md_stack',
			array( 'md_project', 'md_service', 'md_experience' ),
			array(
				'label'             => __( 'Stack', 'melqui-digital' ),
				'hierarchical'      => false,
				'public'            => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array(
					'slug'       => 'stack',
					'with_front' => false,
				),
			)
		);
	}
);

/**
 * The md_project CPT generates an attachment rule — projetos/[^/]+/([^/]+)/?$ —
 * that is matched before the nested category taxonomy rule, so
 * /projetos/categoria/<termo>/ resolved to an attachment and 404'd. Attachment
 * pages are not used by this theme, so the taxonomy rules are simply given
 * priority instead of unpicking the permastruct.
 */
add_action(
	'init',
	static function (): void {
		add_rewrite_rule(
			'projetos/categoria/([^/]+)/page/([0-9]{1,})/?$',
			'index.php?md_project_category=$matches[1]&paged=$matches[2]',
			'top'
		);

		add_rewrite_rule(
			'projetos/categoria/([^/]+)/?$',
			'index.php?md_project_category=$matches[1]',
			'top'
		);
	},
	20
);

/**
 * Flush rewrite rules once after the CPTs change, instead of on every load.
 */
add_action(
	'init',
	static function (): void {
		if ( get_option( 'md_rewrite_version' ) === MD_VERSION ) {
			return;
		}

		flush_rewrite_rules( false );
		update_option( 'md_rewrite_version', MD_VERSION );
	},
	99
);

/**
 * CPT archives are curated, not chronological: order them by the drag-and-drop
 * position from the admin list instead of by date.
 */
add_action(
	'pre_get_posts',
	static function ( WP_Query $query ): void {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}

		if ( ! $query->is_post_type_archive( array( 'md_project', 'md_service' ) )
			&& ! $query->is_tax( array( 'md_project_category', 'md_stack' ) ) ) {
			return;
		}

		$query->set( 'orderby', 'menu_order' );
		$query->set( 'order', 'ASC' );
	}
);
