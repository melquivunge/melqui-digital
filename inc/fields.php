<?php
/**
 * ACF Pro field groups, registered in code so they are versioned and reviewable.
 *
 * Deliberately absent: title, slug, dates, excerpt and featured image — those are
 * native WordPress fields. Categories and stack are taxonomies. Long-form prose
 * uses WYSIWYG rather than nested repeaters of strings.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

/**
 * A repeater holding a single text line — used for the several bullet lists in
 * the content model.
 *
 * @param string $key   Unique field key suffix.
 * @param string $name  Field name.
 * @param string $label Admin label.
 * @param string $button Add-row button label.
 * @return array<string,mixed>
 */
function md_text_list( string $key, string $name, string $label, string $button ): array {
	return array(
		'key'          => 'field_md_' . $key,
		'name'         => $name,
		'label'        => $label,
		'type'         => 'repeater',
		'layout'       => 'table',
		'button_label' => $button,
		'sub_fields'   => array(
			array(
				'key'      => 'field_md_' . $key . '_item',
				'name'     => 'item',
				'label'    => __( 'Item', 'melqui-digital' ),
				'type'     => 'text',
				'required' => 1,
			),
		),
	);
}

/**
 * Editorial header for one home section: eyebrow, title and optional lede.
 * Generated rather than written out so the options page stays consistent.
 *
 * Keys use the field_md_sec_ prefix: field_md_op_<section>_title would collide
 * with repeater sub-field keys such as the scale_points "title" column, and the
 * later definition silently wins.
 *
 * @param string $key      Field name prefix.
 * @param string $label    Admin label for the section.
 * @param bool   $with_text Whether the section has a lede paragraph.
 * @return array<int,array<string,mixed>>
 */
function md_section_header_fields( string $key, string $label, bool $with_text = false ): array {
	$fields = array(
		array(
			'key'   => 'field_md_sec_' . $key . '_eyebrow',
			'name'  => $key . '_eyebrow',
			/* translators: %s: section name. */
			'label' => sprintf( __( '%s — sobrelinha', 'melqui-digital' ), $label ),
			'type'  => 'text',
		),
		array(
			'key'   => 'field_md_sec_' . $key . '_title',
			'name'  => $key . '_title',
			/* translators: %s: section name. */
			'label' => sprintf( __( '%s — título', 'melqui-digital' ), $label ),
			'type'  => 'text',
		),
	);

	if ( $with_text ) {
		$fields[] = array(
			'key'   => 'field_md_sec_' . $key . '_text',
			'name'  => $key . '_text',
			/* translators: %s: section name. */
			'label' => sprintf( __( '%s — texto', 'melqui-digital' ), $label ),
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

		/* ---------------------------------------------------------------- Projeto */
		acf_add_local_field_group(
			array(
				'key'      => 'group_md_project',
				'title'    => __( 'Projeto — detalhes', 'melqui-digital' ),
				'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'md_project' ) ) ),
				'fields'   => array(
					array(
						'key'         => 'field_md_pj_context',
						'name'        => 'context',
						'label'       => __( 'Contexto', 'melqui-digital' ),
						'type'        => 'text',
						'instructions' => __( 'Ex.: "IBM · via Hogarth". Cliente e intermediário.', 'melqui-digital' ),
					),
					array(
						'key'         => 'field_md_pj_engagement',
						'name'        => 'engagement',
						'label'       => __( 'Engajamento', 'melqui-digital' ),
						'type'        => 'text',
						'instructions' => __( 'Enquadramento do vínculo — nunca implica emprego direto.', 'melqui-digital' ),
					),
					array( 'key' => 'field_md_pj_year', 'name' => 'year', 'label' => __( 'Período', 'melqui-digital' ), 'type' => 'text' ),
					array( 'key' => 'field_md_pj_role', 'name' => 'role', 'label' => __( 'Papel', 'melqui-digital' ), 'type' => 'text' ),
					array( 'key' => 'field_md_pj_status', 'name' => 'status', 'label' => __( 'Status', 'melqui-digital' ), 'type' => 'text' ),
					array( 'key' => 'field_md_pj_impact', 'name' => 'impact', 'label' => __( 'Impacto', 'melqui-digital' ), 'type' => 'textarea', 'rows' => 4 ),
					array( 'key' => 'field_md_pj_img_note', 'name' => 'image_note', 'label' => __( 'Nota da imagem', 'melqui-digital' ), 'type' => 'text' ),
					array( 'key' => 'field_md_pj_ext_label', 'name' => 'external_label', 'label' => __( 'Rótulo do link externo', 'melqui-digital' ), 'type' => 'text' ),
					array( 'key' => 'field_md_pj_ext_url', 'name' => 'external_url', 'label' => __( 'URL externa', 'melqui-digital' ), 'type' => 'url' ),
					array(
						'key'          => 'field_md_pj_home_label',
						'name'         => 'home_label',
						'label'        => __( 'Rótulo na home', 'melqui-digital' ),
						'instructions' => __( 'Usado só nos dois destaques do topo. Vazio = categoria · engajamento.', 'melqui-digital' ),
						'type'         => 'text',
					),
					array( 'key' => 'field_md_pj_featured', 'name' => 'featured', 'label' => __( 'Destaque', 'melqui-digital' ), 'type' => 'true_false', 'ui' => 1 ),
					array(
						'key'          => 'field_md_pj_facts',
						'name'         => 'facts',
						'label'        => __( 'Ficha técnica', 'melqui-digital' ),
						'type'         => 'repeater',
						'layout'       => 'table',
						'button_label' => __( 'Adicionar item', 'melqui-digital' ),
						'sub_fields'   => array(
							array( 'key' => 'field_md_pj_fact_label', 'name' => 'label', 'label' => __( 'Rótulo', 'melqui-digital' ), 'type' => 'text', 'required' => 1 ),
							array( 'key' => 'field_md_pj_fact_value', 'name' => 'value', 'label' => __( 'Valor', 'melqui-digital' ), 'type' => 'text', 'required' => 1 ),
						),
					),
					array(
						'key'          => 'field_md_pj_sections',
						'name'         => 'sections',
						'label'        => __( 'Seções do case study', 'melqui-digital' ),
						'type'         => 'repeater',
						'layout'       => 'block',
						'button_label' => __( 'Adicionar seção', 'melqui-digital' ),
						'sub_fields'   => array(
							array( 'key' => 'field_md_pj_sec_title', 'name' => 'title', 'label' => __( 'Título', 'melqui-digital' ), 'type' => 'text', 'required' => 1 ),
							array(
								'key'          => 'field_md_pj_sec_body',
								'name'         => 'body',
								'label'        => __( 'Texto', 'melqui-digital' ),
								'type'         => 'wysiwyg',
								'media_upload' => 0,
								'toolbar'      => 'basic',
							),
						),
					),
				),
			)
		);

		/* --------------------------------------------------------------- Serviço */
		acf_add_local_field_group(
			array(
				'key'      => 'group_md_service',
				'title'    => __( 'Serviço — detalhes', 'melqui-digital' ),
				'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'md_service' ) ) ),
				'fields'   => array(
					array( 'key' => 'field_md_sv_number', 'name' => 'number', 'label' => __( 'Número', 'melqui-digital' ), 'type' => 'text' ),
					array( 'key' => 'field_md_sv_h1', 'name' => 'h1', 'label' => __( 'H1 da página', 'melqui-digital' ), 'type' => 'text' ),
					array( 'key' => 'field_md_sv_tagline', 'name' => 'tagline', 'label' => __( 'Tagline', 'melqui-digital' ), 'type' => 'textarea', 'rows' => 2 ),
					array( 'key' => 'field_md_sv_summary', 'name' => 'summary', 'label' => __( 'Resumo', 'melqui-digital' ), 'type' => 'textarea', 'rows' => 4 ),
					array( 'key' => 'field_md_sv_problem', 'name' => 'problem', 'label' => __( 'Problema', 'melqui-digital' ), 'type' => 'textarea', 'rows' => 4 ),
					md_text_list( 'sv_forwho', 'for_who', __( 'Para quem', 'melqui-digital' ), __( 'Adicionar perfil', 'melqui-digital' ) ),
					md_text_list( 'sv_outcomes', 'outcomes', __( 'Resultados', 'melqui-digital' ), __( 'Adicionar resultado', 'melqui-digital' ) ),
					md_text_list( 'sv_caps', 'capabilities', __( 'Capacidades', 'melqui-digital' ), __( 'Adicionar capacidade', 'melqui-digital' ) ),
					array(
						'key'          => 'field_md_sv_process',
						'name'         => 'process',
						'label'        => __( 'Processo', 'melqui-digital' ),
						'type'         => 'repeater',
						'layout'       => 'block',
						'button_label' => __( 'Adicionar etapa', 'melqui-digital' ),
						'sub_fields'   => array(
							array( 'key' => 'field_md_sv_proc_title', 'name' => 'title', 'label' => __( 'Etapa', 'melqui-digital' ), 'type' => 'text', 'required' => 1 ),
							array( 'key' => 'field_md_sv_proc_text', 'name' => 'text', 'label' => __( 'Descrição', 'melqui-digital' ), 'type' => 'textarea', 'rows' => 3, 'required' => 1 ),
						),
					),
					array(
						'key'          => 'field_md_sv_faqs',
						'name'         => 'faqs',
						'label'        => __( 'Perguntas frequentes', 'melqui-digital' ),
						'instructions' => __( 'Alimenta o schema FAQPage — escreva a resposta completa e autossuficiente.', 'melqui-digital' ),
						'type'         => 'repeater',
						'layout'       => 'block',
						'button_label' => __( 'Adicionar pergunta', 'melqui-digital' ),
						'sub_fields'   => array(
							array( 'key' => 'field_md_sv_faq_q', 'name' => 'q', 'label' => __( 'Pergunta', 'melqui-digital' ), 'type' => 'text', 'required' => 1 ),
							array( 'key' => 'field_md_sv_faq_a', 'name' => 'a', 'label' => __( 'Resposta', 'melqui-digital' ), 'type' => 'textarea', 'rows' => 4, 'required' => 1 ),
						),
					),
					array(
						'key'        => 'field_md_sv_related',
						'name'       => 'related_project',
						'label'      => __( 'Projeto relacionado', 'melqui-digital' ),
						'type'       => 'post_object',
						'post_type'  => array( 'md_project' ),
						'return_format' => 'id',
					),
				),
			)
		);

		/* ----------------------------------------------------------- Experiência */
		acf_add_local_field_group(
			array(
				'key'      => 'group_md_experience',
				'title'    => __( 'Carreira — detalhes', 'melqui-digital' ),
				'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'md_experience' ) ) ),
				'fields'   => array(
					array( 'key' => 'field_md_xp_period', 'name' => 'period', 'label' => __( 'Período', 'melqui-digital' ), 'type' => 'text', 'required' => 1 ),
					array( 'key' => 'field_md_xp_role', 'name' => 'role', 'label' => __( 'Cargo', 'melqui-digital' ), 'type' => 'text', 'required' => 1 ),
					array( 'key' => 'field_md_xp_org', 'name' => 'org', 'label' => __( 'Organização', 'melqui-digital' ), 'type' => 'text', 'required' => 1 ),
					array( 'key' => 'field_md_xp_place', 'name' => 'place', 'label' => __( 'Local', 'melqui-digital' ), 'type' => 'text' ),
					array( 'key' => 'field_md_xp_summary', 'name' => 'summary', 'label' => __( 'Resumo', 'melqui-digital' ), 'type' => 'textarea', 'rows' => 3 ),
					md_text_list( 'xp_points', 'points', __( 'Entregas', 'melqui-digital' ), __( 'Adicionar entrega', 'melqui-digital' ) ),
				),
			)
		);

		/* ------------------------------------------------------ Post (GEO/answer) */
		acf_add_local_field_group(
			array(
				'key'      => 'group_md_post',
				'title'    => __( 'Artigo — resposta direta', 'melqui-digital' ),
				'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'post' ) ) ),
				'position' => 'acf_after_title',
				'fields'   => array(
					array(
						'key'          => 'field_md_po_answer',
						'name'         => 'answer',
						'label'        => __( 'Resposta direta', 'melqui-digital' ),
						'instructions' => __( 'Resposta curta e completa no topo do artigo, para leitores e answer engines (GEO). Deve fazer sentido isolada do resto do texto.', 'melqui-digital' ),
						'type'         => 'textarea',
						'rows'         => 4,
						'maxlength'    => 600,
					),
					array( 'key' => 'field_md_po_reading', 'name' => 'reading_time', 'label' => __( 'Tempo de leitura', 'melqui-digital' ), 'type' => 'text' ),
					array( 'key' => 'field_md_po_featured', 'name' => 'featured', 'label' => __( 'Artigo em destaque', 'melqui-digital' ), 'type' => 'true_false', 'ui' => 1 ),
				),
			)
		);

		/* --------------------------------------------------- Opções globais do site */
		acf_add_options_page(
			array(
				'page_title' => __( 'Opções do site', 'melqui-digital' ),
				'menu_title' => __( 'Opções do site', 'melqui-digital' ),
				'menu_slug'  => 'md-site-options',
				'capability' => 'manage_options',
				'icon_url'   => 'dashicons-admin-settings',
				'position'   => 59,
				'redirect'   => false,
			)
		);

		acf_add_local_field_group(
			array(
				'key'      => 'group_md_options',
				'title'    => __( 'Conteúdo global', 'melqui-digital' ),
				'location' => array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => 'md-site-options' ) ) ),
				'fields'   => array(
					array( 'key' => 'field_md_op_email', 'name' => 'contact_email', 'label' => __( 'E-mail de contato', 'melqui-digital' ), 'type' => 'email' ),
					array(
						'key'          => 'field_md_op_social',
						'name'         => 'social_links',
						'label'        => __( 'Redes', 'melqui-digital' ),
						'type'         => 'repeater',
						'layout'       => 'table',
						'button_label' => __( 'Adicionar rede', 'melqui-digital' ),
						'sub_fields'   => array(
							array( 'key' => 'field_md_op_social_label', 'name' => 'label', 'label' => __( 'Nome', 'melqui-digital' ), 'type' => 'text', 'required' => 1 ),
							array( 'key' => 'field_md_op_social_url', 'name' => 'url', 'label' => 'URL', 'type' => 'url', 'required' => 1 ),
						),
					),
					array( 'key' => 'field_md_op_brand_tag', 'name' => 'brand_tagline', 'label' => __( 'Tagline da marca', 'melqui-digital' ), 'type' => 'text' ),
					array( 'key' => 'field_md_op_logo', 'name' => 'brand_logo', 'label' => __( 'Logo', 'melqui-digital' ), 'type' => 'image', 'return_format' => 'id', 'preview_size' => 'thumbnail' ),
					array( 'key' => 'field_md_op_cta1_label', 'name' => 'cta_label', 'label' => __( 'CTA principal — texto', 'melqui-digital' ), 'type' => 'text' ),
					array( 'key' => 'field_md_op_cta1_url', 'name' => 'cta_url', 'label' => __( 'CTA principal — link', 'melqui-digital' ), 'type' => 'url' ),
					array( 'key' => 'field_md_op_about_eyebrow', 'name' => 'about_eyebrow', 'label' => __( 'Sobre — sobrelinha do hero', 'melqui-digital' ), 'type' => 'text' ),
					array( 'key' => 'field_md_op_about_text', 'name' => 'about_text', 'label' => __( 'Sobre — texto do hero', 'melqui-digital' ), 'type' => 'textarea', 'rows' => 5 ),
					...md_section_header_fields( 'progress', __( 'Sobre: progressão', 'melqui-digital' ) ),
					...md_section_header_fields( 'xp', __( 'Sobre: experiência', 'melqui-digital' ) ),
					...md_section_header_fields( 'principles', __( 'Sobre: princípios', 'melqui-digital' ) ),
					array(
						'key'          => 'field_md_op_principles',
						'name'         => 'principles',
						'label'        => __( 'Princípios de engenharia', 'melqui-digital' ),
						'type'         => 'repeater',
						'layout'       => 'block',
						'button_label' => __( 'Adicionar princípio', 'melqui-digital' ),
						'sub_fields'   => array(
							array( 'key' => 'field_md_op_prin_title', 'name' => 'title', 'label' => __( 'Título', 'melqui-digital' ), 'type' => 'text', 'required' => 1 ),
							array( 'key' => 'field_md_op_prin_text', 'name' => 'text', 'label' => __( 'Texto', 'melqui-digital' ), 'type' => 'textarea', 'rows' => 3, 'required' => 1 ),
						),
					),
					...md_section_header_fields( 'stackfund', __( 'Sobre: stack', 'melqui-digital' ) ),
					array(
						'key'          => 'field_md_op_education',
						'name'         => 'education',
						'label'        => __( 'Formação e base', 'melqui-digital' ),
						'type'         => 'repeater',
						'layout'       => 'table',
						'button_label' => __( 'Adicionar item', 'melqui-digital' ),
						'sub_fields'   => array(
							array( 'key' => 'field_md_op_edu_label', 'name' => 'label', 'label' => __( 'Rótulo', 'melqui-digital' ), 'type' => 'text', 'required' => 1 ),
							array( 'key' => 'field_md_op_edu_value', 'name' => 'value', 'label' => __( 'Valor', 'melqui-digital' ), 'type' => 'text', 'required' => 1 ),
						),
					),
					array( 'key' => 'field_md_op_next_ab_title', 'name' => 'next_about_title', 'label' => __( 'Próximo passo em Sobre — título', 'melqui-digital' ), 'type' => 'text' ),
					array( 'key' => 'field_md_op_next_ab_text', 'name' => 'next_about_text', 'label' => __( 'Próximo passo em Sobre — texto', 'melqui-digital' ), 'type' => 'textarea', 'rows' => 3 ),
					...md_section_header_fields( 'arch_projects', __( 'Arquivo de projetos', 'melqui-digital' ), true ),
					...md_section_header_fields( 'arch_services', __( 'Arquivo de serviços', 'melqui-digital' ), true ),
					...md_section_header_fields( 'arch_blog', __( 'Arquivo do blog', 'melqui-digital' ), true ),
					...md_section_header_fields( 'oldwork', __( 'Portfólio anterior', 'melqui-digital' ), true ),
					md_text_list( 'oldwork_items', 'oldwork_items', __( 'Portfólio anterior — nomes', 'melqui-digital' ), __( 'Adicionar projeto', 'melqui-digital' ) ),
					array( 'key' => 'field_md_op_next_pjs_title', 'name' => 'next_projects_title', 'label' => __( 'Próximo passo no arquivo de projetos — título', 'melqui-digital' ), 'type' => 'text' ),
					array( 'key' => 'field_md_op_next_pjs_text', 'name' => 'next_projects_text', 'label' => __( 'Próximo passo no arquivo de projetos — texto', 'melqui-digital' ), 'type' => 'textarea', 'rows' => 3 ),
					array( 'key' => 'field_md_op_next_svs_title', 'name' => 'next_services_title', 'label' => __( 'Próximo passo no arquivo de serviços — título', 'melqui-digital' ), 'type' => 'text' ),
					array( 'key' => 'field_md_op_next_svs_text', 'name' => 'next_services_text', 'label' => __( 'Próximo passo no arquivo de serviços — texto', 'melqui-digital' ), 'type' => 'textarea', 'rows' => 3 ),
					array( 'key' => 'field_md_op_news_title', 'name' => 'news_title', 'label' => __( 'Newsletter — título', 'melqui-digital' ), 'type' => 'text' ),
					array( 'key' => 'field_md_op_news_text', 'name' => 'news_text', 'label' => __( 'Newsletter — texto', 'melqui-digital' ), 'type' => 'textarea', 'rows' => 2 ),
					array( 'key' => 'field_md_op_next_eyebrow', 'name' => 'next_eyebrow', 'label' => __( 'Próximo passo — sobrelinha', 'melqui-digital' ), 'type' => 'text' ),
					array( 'key' => 'field_md_op_next_title', 'name' => 'next_title', 'label' => __( 'Próximo passo — título', 'melqui-digital' ), 'type' => 'text' ),
					array( 'key' => 'field_md_op_next_text', 'name' => 'next_text', 'label' => __( 'Próximo passo — texto', 'melqui-digital' ), 'type' => 'textarea', 'rows' => 3 ),
					array( 'key' => 'field_md_op_next_label', 'name' => 'next_label', 'label' => __( 'Próximo passo — botão', 'melqui-digital' ), 'type' => 'text' ),
					array( 'key' => 'field_md_op_next_pj_title', 'name' => 'next_project_title', 'label' => __( 'Próximo passo em projeto — título', 'melqui-digital' ), 'type' => 'text' ),
					array( 'key' => 'field_md_op_next_pj_text', 'name' => 'next_project_text', 'label' => __( 'Próximo passo em projeto — texto', 'melqui-digital' ), 'type' => 'textarea', 'rows' => 3 ),
					array( 'key' => 'field_md_op_footer_text', 'name' => 'footer_text', 'label' => __( 'Rodapé — descrição', 'melqui-digital' ), 'type' => 'textarea', 'rows' => 3 ),
					array( 'key' => 'field_md_op_footer_meta', 'name' => 'footer_meta', 'label' => __( 'Rodapé — localização', 'melqui-digital' ), 'type' => 'text' ),
					array( 'key' => 'field_md_op_footer_note', 'name' => 'footer_note', 'label' => __( 'Rodapé — assinatura', 'melqui-digital' ), 'type' => 'text' ),
					array( 'key' => 'field_md_op_footer_stack', 'name' => 'footer_stack', 'label' => __( 'Rodapé — stack', 'melqui-digital' ), 'type' => 'text' ),
					array(
						'key'          => 'field_md_op_caps',
						'name'         => 'capability_groups',
						'label'        => __( 'Grupos de capacidades', 'melqui-digital' ),
						'type'         => 'repeater',
						'layout'       => 'block',
						'button_label' => __( 'Adicionar grupo', 'melqui-digital' ),
						'sub_fields'   => array(
							array( 'key' => 'field_md_op_caps_area', 'name' => 'area', 'label' => __( 'Área', 'melqui-digital' ), 'type' => 'text', 'required' => 1 ),
							array( 'key' => 'field_md_op_caps_lead', 'name' => 'lead', 'label' => __( 'Descrição', 'melqui-digital' ), 'type' => 'textarea', 'rows' => 2 ),
							md_text_list( 'op_caps_items', 'items', __( 'Itens', 'melqui-digital' ), __( 'Adicionar item', 'melqui-digital' ) ),
						),
					),
				),
			)
		);
	}
);
