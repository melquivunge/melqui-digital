<?php
/**
 * Small template helpers.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

/**
 * Archive heading that also covers the blog index and search results.
 */
function md_archive_title(): string {
	if ( is_search() ) {
		/* translators: %s: search query. */
		return sprintf( __( 'Resultados para "%s"', 'melqui-digital' ), get_search_query() );
	}

	if ( is_home() ) {
		return __( 'Notas', 'melqui-digital' );
	}

	return wp_strip_all_tags( get_the_archive_title() );
}

/**
 * Breadcrumb trail plus its BreadcrumbList schema, emitted from one source.
 *
 * @param string $parent_label Label of the parent listing.
 * @param string $parent_url    URL of the parent listing.
 * @param string $current_label Short label for the current page.
 */
function md_breadcrumbs( string $parent_label = '', string $parent_url = '', string $current_label = '' ): void {
	$items = array(
		array(
			'name' => __( 'Início', 'melqui-digital' ),
			'url'  => home_url( '/' ),
		),
	);

	// A top-level page has no listing above it: pass an empty label and the
	// trail stays two levels instead of repeating the page title.
	if ( '' !== $parent_label ) {
		$items[] = array(
			'name' => $parent_label,
			'url'  => $parent_url,
		);
	}

	$items[] = array(
		// A page title can be a full sentence; pass a short label for the trail.
		'name' => '' !== $current_label ? $current_label : wp_strip_all_tags( get_the_title() ),
		'url'  => (string) get_permalink(),
	);

	echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Trilha de navegação', 'melqui-digital' ) . '"><ol>';

	foreach ( $items as $index => $item ) {
		$is_last = ( count( $items ) - 1 ) === $index;

		printf(
			'<li>%s</li>',
			$is_last
				? '<span aria-current="page">' . esc_html( $item['name'] ) . '</span>'
				: '<a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['name'] ) . '</a>'
		);
	}

	echo '</ol></nav>';

	md_print_jsonld(
		array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => array_map(
				static fn( int $position, array $item ): array => array(
					'@type'    => 'ListItem',
					'position' => $position + 1,
					'name'     => $item['name'],
					'item'     => $item['url'],
				),
				array_keys( $items ),
				$items
			),
		)
	);
}

/**
 * Render one of the single-value repeaters as a titled list.
 *
 * @param mixed  $rows  Repeater rows, each holding an "item" key.
 * @param string $title Section heading.
 */
function md_item_list( $rows, string $title ): void {
	if ( ! is_array( $rows ) || ! $rows ) {
		return;
	}

	printf( '<h2>%s</h2><ul class="item-list">', esc_html( $title ) );

	foreach ( $rows as $row ) {
		if ( empty( $row['item'] ) ) {
			continue;
		}

		printf( '<li>%s</li>', esc_html( (string) $row['item'] ) );
	}

	echo '</ul>';
}

/**
 * Term filter links for an archive.
 *
 * @param string $taxonomy    Taxonomy slug.
 * @param string $archive_url URL of the unfiltered archive.
 */
function md_term_filter( string $taxonomy, string $archive_url ): void {
	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
		)
	);

	if ( is_wp_error( $terms ) || ! $terms ) {
		return;
	}

	$current = get_queried_object();
	$current = $current instanceof WP_Term ? $current->term_id : 0;

	echo '<ul class="tags tags--filter">';

	printf(
		'<li><a href="%s"%s>%s</a></li>',
		esc_url( $archive_url ),
		$current ? '' : ' aria-current="page"',
		esc_html__( 'Todos', 'melqui-digital' )
	);

	foreach ( $terms as $term ) {
		printf(
			'<li><a href="%s"%s>%s</a></li>',
			esc_url( (string) get_term_link( $term ) ),
			$current === $term->term_id ? ' aria-current="page"' : '',
			esc_html( $term->name )
		);
	}

	echo '</ul>';
}

/**
 * Editorial label for a project. Falls back to category · engagement so a new
 * project never renders an empty eyebrow.
 */
function md_project_label( int $post_id ): string {
	$label = (string) get_field( 'home_label', $post_id );

	if ( '' !== $label ) {
		return $label;
	}

	$terms = get_the_terms( $post_id, 'md_project_category' );
	$parts = array();

	if ( $terms && ! is_wp_error( $terms ) ) {
		$parts[] = $terms[0]->name;
	}

	$engagement = (string) get_field( 'engagement', $post_id );

	if ( '' !== $engagement ) {
		$parts[] = $engagement;
	}

	return implode( ' · ', $parts );
}

/**
 * A project has a case study when it actually has written sections — there is
 * no separate flag to fall out of sync with the content.
 */
function md_has_case_study( int $post_id ): bool {
	$sections = get_field( 'sections', $post_id );

	return is_array( $sections ) && (bool) $sections;
}

/**
 * Call to action under a project card: read it, or say why there is nothing to
 * read yet.
 */
function md_project_cta( int $post_id ): string {
	return md_has_case_study( $post_id )
		? __( 'Ver case study', 'melqui-digital' )
		: (string) get_field( 'status', $post_id );
}

/**
 * A repeater of single-value rows rendered as hairline-separated lines.
 *
 * @param mixed $rows Repeater rows holding an "item" key.
 */
function md_line_list( $rows ): void {
	if ( ! is_array( $rows ) || ! $rows ) {
		return;
	}

	echo '<ul class="line-list">';

	foreach ( $rows as $row ) {
		if ( empty( $row['item'] ) ) {
			continue;
		}

		printf( '<li>%s</li>', esc_html( (string) $row['item'] ) );
	}

	echo '</ul>';
}

/**
 * Links to the sibling services, so a visitor never dead-ends on one page.
 *
 * @param int $current_id Service being viewed.
 */
function md_other_services( int $current_id ): void {
	$others = get_posts(
		array(
			'post_type'      => 'md_service',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'exclude'        => array( $current_id ),
		)
	);

	if ( ! $others ) {
		return;
	}

	echo '<section class="section other-services" aria-labelledby="outros">';
	echo '<div class="container">';
	printf( '<h2 id="outros" class="eyebrow">%s</h2>', esc_html__( 'Outros serviços', 'melqui-digital' ) );
	echo '<ul>';

	foreach ( $others as $other ) {
		printf(
			'<li><a href="%s">%s</a></li>',
			esc_url( (string) get_permalink( $other ) ),
			esc_html( get_the_title( $other ) )
		);
	}

	echo '</ul></div></section>';
}

/**
 * Breadcrumb for archives, where there is no current post to name.
 *
 * @param string $label Archive label.
 */
function md_archive_breadcrumb( string $label ): void {
	printf(
		'<nav class="breadcrumbs" aria-label="%s"><ol><li><a href="%s">%s</a></li><li><span aria-current="page">%s</span></li></ol></nav>',
		esc_attr__( 'Trilha de navegação', 'melqui-digital' ),
		esc_url( home_url( '/' ) ),
		esc_html__( 'Início', 'melqui-digital' ),
		esc_html( $label )
	);
}

/**
 * "Categoria · tempo de leitura" line used on article cards.
 */
function md_post_meta( int $post_id ): string {
	$categories = get_the_category( $post_id );
	$parts      = array();

	if ( $categories ) {
		$parts[] = $categories[0]->name;
	}

	$reading = (string) get_field( 'reading_time', $post_id );

	if ( '' !== $reading ) {
		$parts[] = $reading;
	}

	return implode( ' · ', $parts );
}

/**
 * The article flagged as featured, falling back to the most recent one.
 */
function md_featured_post(): ?WP_Post {
	$flagged = get_posts(
		array(
			'post_type'      => 'post',
			'posts_per_page' => 1,
			'meta_key'       => 'featured',
			'meta_value'     => '1',
		)
	);

	if ( $flagged ) {
		return $flagged[0];
	}

	$latest = get_posts( array( 'post_type' => 'post', 'posts_per_page' => 1 ) );

	return $latest ? $latest[0] : null;
}
