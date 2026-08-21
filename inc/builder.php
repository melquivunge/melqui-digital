<?php
/**
 * Section builder: an ACF Flexible Content field that lets any page be composed
 * from reorderable, removable blocks.
 *
 * Field keys use the field_md_fx_ prefix. Reusing field_md_op_ here would
 * collide with the options page and the later definition silently wins — that
 * already cost us the "escala" section once.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

/**
 * A text/textarea pair used by most section headers.
 *
 * @param string $layout    Layout name.
 * @param bool   $with_text Whether the section carries a lede.
 * @return array<int,array<string,mixed>>
 */
function md_fx_header( string $layout, bool $with_text = false ): array {
	$fields = array(
		array(
			'key'   => 'field_md_fx_' . $layout . '_eyebrow',
			'name'  => 'eyebrow',
			'label' => __( 'Sobrelinha', 'melqui-digital' ),
			'type'  => 'text',
		),
		array(
			'key'   => 'field_md_fx_' . $layout . '_title',
			'name'  => 'title',
			'label' => __( 'Título', 'melqui-digital' ),
			'type'  => 'text',
		),
	);

	if ( $with_text ) {
		$fields[] = array(
			'key'   => 'field_md_fx_' . $layout . '_text',
			'name'  => 'text',
			'label' => __( 'Texto', 'melqui-digital' ),
			'type'  => 'textarea',
			'rows'  => 3,
		);
	}

	return $fields;
}

add_action(
	'acf/init',
	static function (): void {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		acf_add_local_field_group(
			array(
				'key'                   => 'group_md_builder',
				'title'                 => __( 'Construtor de seções', 'melqui-digital' ),
				'location'              => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'page' ) ) ),
				'position'              => 'normal',
				'hide_on_screen'        => array(),
				'menu_order'            => 5,
				'fields'                => array(
					array(
						'key'          => 'field_md_fx_sections',
						'name'         => 'sections',
						'label'        => __( 'Seções', 'melqui-digital' ),
						'instructions' => __( 'Arraste para reordenar. Remova o que não usar. Deixe vazio para a página renderizar apenas o conteúdo do editor.', 'melqui-digital' ),
						'type'         => 'flexible_content',
						'button_label' => __( 'Adicionar seção', 'melqui-digital' ),
						'layouts'      => array(

							'hero' => array(
								'key'        => 'layout_md_hero',
								'name'       => 'hero',
								'label'      => __( 'Hero', 'melqui-digital' ),
								'display'    => 'block',
								'sub_fields' => array(
									array( 'key' => 'field_md_fx_hero_eyebrow', 'name' => 'eyebrow', 'label' => __( 'Sobrelinha', 'melqui-digital' ), 'type' => 'text' ),
									array( 'key' => 'field_md_fx_hero_title', 'name' => 'title', 'label' => __( 'Título', 'melqui-digital' ), 'type' => 'textarea', 'rows' => 3 ),
									array( 'key' => 'field_md_fx_hero_text', 'name' => 'text', 'label' => __( 'Texto', 'melqui-digital' ), 'type' => 'textarea', 'rows' => 4 ),
									array( 'key' => 'field_md_fx_hero_meta', 'name' => 'meta', 'label' => __( 'Rodapé do hero', 'melqui-digital' ), 'type' => 'text' ),
									array( 'key' => 'field_md_fx_hero_portrait', 'name' => 'portrait', 'label' => __( 'Retrato', 'melqui-digital' ), 'type' => 'image', 'return_format' => 'id', 'preview_size' => 'medium' ),
									array( 'key' => 'field_md_fx_hero_cta1l', 'name' => 'cta_label', 'label' => __( 'CTA principal — texto', 'melqui-digital' ), 'type' => 'text' ),
									array( 'key' => 'field_md_fx_hero_cta1u', 'name' => 'cta_url', 'label' => __( 'CTA principal — link', 'melqui-digital' ), 'type' => 'url' ),
									array( 'key' => 'field_md_fx_hero_cta2l', 'name' => 'cta2_label', 'label' => __( 'CTA secundário — texto', 'melqui-digital' ), 'type' => 'text' ),
									array( 'key' => 'field_md_fx_hero_cta2u', 'name' => 'cta2_url', 'label' => __( 'CTA secundário — link', 'melqui-digital' ), 'type' => 'url' ),
									array(
										'key'          => 'field_md_fx_hero_proof',
										'name'         => 'proof_rail',
										'label'        => __( 'Trilho de prova', 'melqui-digital' ),
										'type'         => 'repeater',
										'layout'       => 'table',
										'button_label' => __( 'Adicionar item', 'melqui-digital' ),
										'sub_fields'   => array(
											array( 'key' => 'field_md_fx_hero_proof_item', 'name' => 'item', 'label' => __( 'Item', 'melqui-digital' ), 'type' => 'text', 'required' => 1 ),
										),
									),
								),
							),

							'work' => array(
								'key'        => 'layout_md_work',
								'name'       => 'work',
								'label'      => __( 'Trabalho selecionado', 'melqui-digital' ),
								'display'    => 'block',
								'sub_fields' => array(
									array( 'key' => 'field_md_fx_work_title', 'name' => 'title', 'label' => __( 'Título', 'melqui-digital' ), 'type' => 'text' ),
									array( 'key' => 'field_md_fx_work_link', 'name' => 'link_label', 'label' => __( 'Link do arquivo', 'melqui-digital' ), 'type' => 'text' ),
									array( 'key' => 'field_md_fx_work_leads', 'name' => 'leads', 'label' => __( 'Quantos destaques no topo', 'melqui-digital' ), 'type' => 'number', 'default_value' => 2, 'min' => 0, 'max' => 4 ),
								),
							),

							'scale' => array(
								'key'        => 'layout_md_scale',
								'name'       => 'scale',
								'label'      => __( 'Escala', 'melqui-digital' ),
								'display'    => 'block',
								'sub_fields' => array_merge(
									md_fx_header( 'scale', true ),
									array(
										array(
											'key'          => 'field_md_fx_scale_points',
											'name'         => 'points',
											'label'        => __( 'Pontos', 'melqui-digital' ),
											'type'         => 'repeater',
											'layout'       => 'block',
											'button_label' => __( 'Adicionar ponto', 'melqui-digital' ),
											'sub_fields'   => array(
												array( 'key' => 'field_md_fx_scale_n', 'name' => 'n', 'label' => __( 'Número', 'melqui-digital' ), 'type' => 'text' ),
												array( 'key' => 'field_md_fx_scale_pt', 'name' => 'title', 'label' => __( 'Título', 'melqui-digital' ), 'type' => 'text', 'required' => 1 ),
												array( 'key' => 'field_md_fx_scale_px', 'name' => 'text', 'label' => __( 'Texto', 'melqui-digital' ), 'type' => 'textarea', 'rows' => 3 ),
											),
										),
									)
								),
							),

							'services' => array(
								'key'        => 'layout_md_services',
								'name'       => 'services',
								'label'      => __( 'Serviços', 'melqui-digital' ),
								'display'    => 'block',
								'sub_fields' => md_fx_header( 'services' ),
							),

							'career' => array(
								'key'        => 'layout_md_career',
								'name'       => 'career',
								'label'      => __( 'Trajetória', 'melqui-digital' ),
								'display'    => 'block',
								'sub_fields' => array_merge(
									md_fx_header( 'career', true ),
									array(
										array( 'key' => 'field_md_fx_career_link', 'name' => 'link_label', 'label' => __( 'Texto do link', 'melqui-digital' ), 'type' => 'text' ),
									)
								),
							),

							'capabilities' => array(
								'key'        => 'layout_md_caps',
								'name'       => 'capabilities',
								'label'      => __( 'Capacidades', 'melqui-digital' ),
								'display'    => 'block',
								'sub_fields' => md_fx_header( 'caps' ),
							),

							'notes' => array(
								'key'        => 'layout_md_notes',
								'name'       => 'notes',
								'label'      => __( 'Notas', 'melqui-digital' ),
								'display'    => 'block',
								'sub_fields' => array(
									array( 'key' => 'field_md_fx_notes_title', 'name' => 'title', 'label' => __( 'Título', 'melqui-digital' ), 'type' => 'text' ),
									array( 'key' => 'field_md_fx_notes_link', 'name' => 'link_label', 'label' => __( 'Texto do link', 'melqui-digital' ), 'type' => 'text' ),
								),
							),

							'closing' => array(
								'key'        => 'layout_md_closing',
								'name'       => 'closing',
								'label'      => __( 'Fechamento', 'melqui-digital' ),
								'display'    => 'block',
								'sub_fields' => array(
									array( 'key' => 'field_md_fx_closing_title', 'name' => 'title', 'label' => __( 'Título', 'melqui-digital' ), 'type' => 'textarea', 'rows' => 2 ),
									array( 'key' => 'field_md_fx_closing_ae', 'name' => 'a_eyebrow', 'label' => __( 'Coluna 1 — sobrelinha', 'melqui-digital' ), 'type' => 'text' ),
									array( 'key' => 'field_md_fx_closing_at', 'name' => 'a_title', 'label' => __( 'Coluna 1 — título', 'melqui-digital' ), 'type' => 'text' ),
									array( 'key' => 'field_md_fx_closing_ax', 'name' => 'a_text', 'label' => __( 'Coluna 1 — texto', 'melqui-digital' ), 'type' => 'textarea', 'rows' => 3 ),
									array( 'key' => 'field_md_fx_closing_be', 'name' => 'b_eyebrow', 'label' => __( 'Coluna 2 — sobrelinha', 'melqui-digital' ), 'type' => 'text' ),
									array( 'key' => 'field_md_fx_closing_bt', 'name' => 'b_title', 'label' => __( 'Coluna 2 — título', 'melqui-digital' ), 'type' => 'text' ),
									array( 'key' => 'field_md_fx_closing_bx', 'name' => 'b_text', 'label' => __( 'Coluna 2 — texto', 'melqui-digital' ), 'type' => 'textarea', 'rows' => 3 ),
								),
							),
						),
					),
				),
			)
		);
	}
);

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
