<?php
/**
 * SEO and GEO output: canonical, social meta and JSON-LD.
 *
 * Ported from the React app's src/lib/seo.ts so the structured data stays
 * identical between the two implementations. Everything is escaped on output.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

/**
 * The English one-pager, found by its template rather than a hardcoded slug so
 * renaming the page in the admin cannot break hreflang.
 */
function md_en_page_id(): int {
	static $id = null;

	if ( null !== $id ) {
		return $id;
	}

	$found = get_posts(
		array(
			'post_type'      => 'page',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_wp_page_template',
			'meta_value'     => 'page-en.php',
		)
	);

	$id = $found ? (int) $found[0] : 0;

	return $id;
}

function md_is_en_page(): bool {
	// /llms.txt answers on `init`, before the main query exists — is_page()
	// there is a _doing_it_wrong notice that only shows up under WP_DEBUG.
	if ( ! did_action( 'wp' ) ) {
		return false;
	}

	$id = md_en_page_id();

	return $id > 0 && is_page( $id );
}

/**
 * Read a global ACF option with a fallback, safe when ACF is not active.
 *
 * @param string $name    Field name.
 * @param string $default Value used when unset or ACF is missing.
 */
function md_option( string $name, string $default = '' ): string {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}

	$value = get_field( $name, 'option' );

	return ( is_string( $value ) && '' !== $value ) ? $value : $default;
}

/**
 * The Person entity, reused as publisher/author across every graph.
 *
 * @return array<string,mixed>
 */
function md_person_schema(): array {
	$site_url = untrailingslashit( home_url() );

	$person = array(
		'@type'         => 'Person',
		'@id'           => $site_url . '/#melqui-vunge',
		'name'          => 'Melqui Vunge',
		'jobTitle'      => 'Senior WordPress & Full-Stack Engineer',
		'description'   => 'Engenheiro web sênior baseado em Brasília, Brasil. Engenharia WordPress em plataformas de conteúdo de grande escala, backend em PHP e Laravel e aplicações em React e TypeScript.',
		'email'         => 'mailto:' . md_option( 'contact_email', 'contato@melquivunge.com.br' ),
		'url'           => $site_url,
		'worksFor'      => array(
			'@type' => 'Organization',
			'name'  => 'In All Media',
		),
		'alumniOf'      => array(
			'@type' => 'EducationalOrganization',
			'name'  => 'International Training College Lingua',
		),
		'knowsLanguage' => array( 'pt-BR', 'en' ),
		'knowsAbout'    => array(
			'Engenharia WordPress',
			'WordPress multisite',
			'WooCommerce',
			'PHP',
			'Laravel',
			'React',
			'TypeScript',
			'E-commerce',
			'Performance web',
			'Core Web Vitals',
			'Segurança de aplicações web',
			'SEO técnico',
			'Dados estruturados',
		),
		'address'       => array(
			'@type'           => 'PostalAddress',
			'addressLocality' => 'Brasília',
			'addressRegion'   => 'DF',
			'addressCountry'  => 'BR',
		),
	);

	// sameAs is how a search or answer engine confirms this Person is the same
	// entity it already knows from LinkedIn/GitHub. Empty array omitted: an
	// empty sameAs is worse than none.
	$same_as = md_social_urls();

	if ( $same_as ) {
		$person['sameAs'] = $same_as;
	}

	return $person;
}

/**
 * URLs from the `social_links` options repeater, for sameAs.
 *
 * @return array<int,string>
 */
function md_social_urls(): array {
	if ( ! function_exists( 'get_field' ) ) {
		return array();
	}

	$rows = get_field( 'social_links', 'option' );
	$urls = array();

	if ( is_array( $rows ) ) {
		foreach ( $rows as $row ) {
			$url = is_array( $row ) && isset( $row['url'] ) ? (string) $row['url'] : '';

			if ( '' !== $url && filter_var( $url, FILTER_VALIDATE_URL ) ) {
				$urls[] = $url;
			}
		}
	}

	return array_values( array_unique( $urls ) );
}

/**
 * Print a JSON-LD block. Uses wp_json_encode and the safe unescaped-slashes
 * flags so URLs stay readable without allowing tag injection.
 *
 * @param array<string,mixed> $data Graph payload.
 */
function md_print_jsonld( array $data ): void {
	// JSON_HEX_TAG escapes < and > to \u003C/\u003E so the payload can never
	// break out of the script element.
	$json = wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP );

	if ( false === $json ) {
		return;
	}

	echo '<script type="application/ld+json">' . $json . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON encoded with HEX_TAG above.
}

/**
 * Canonical, Open Graph and Twitter tags.
 */
add_action(
	'wp_head',
	static function (): void {
		$url         = is_singular() ? get_permalink() : home_url( add_query_arg( array() ) );
		$url         = is_string( $url ) ? $url : home_url( '/' );
		$title       = wp_get_document_title();
		$description = md_meta_description();
		$image       = is_singular() && has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'full' ) : '';

		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );

		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );

		printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $description ) );
		printf( '<meta property="og:type" content="%s">' . "\n", esc_attr( is_singular( 'post' ) ? 'article' : 'website' ) );
		printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
		printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
		printf( '<meta property="og:locale" content="%s">' . "\n", esc_attr( md_is_en_page() ? 'en_US' : 'pt_BR' ) );

		printf( '<meta name="twitter:card" content="%s">' . "\n", esc_attr( 'summary_large_image' ) );
		printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
		printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $description ) );

		if ( $image ) {
			printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
			printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $image ) );
		}

		// hreflang only means something as a reciprocal set. The pt-BR site and
		// the English one-pager are the only pair that exists; every other page
		// just points at itself, which is valid and says nothing.
		$en_id = md_en_page_id();

		if ( $en_id && ( is_front_page() || md_is_en_page() ) ) {
			printf( '<link rel="alternate" hreflang="pt-BR" href="%s">' . "\n", esc_url( home_url( '/' ) ) );
			printf( '<link rel="alternate" hreflang="en" href="%s">' . "\n", esc_url( (string) get_permalink( $en_id ) ) );
			printf( '<link rel="alternate" hreflang="x-default" href="%s">' . "\n", esc_url( home_url( '/' ) ) );
		} else {
			printf( '<link rel="alternate" hreflang="pt-BR" href="%s">' . "\n", esc_url( $url ) );
			printf( '<link rel="alternate" hreflang="x-default" href="%s">' . "\n", esc_url( $url ) );
		}
	},
	1
);

/**
 * Robots directives go through core's wp_robots so anything else on the site —
 * a plugin, another template — can override them for its own pages. Printing a
 * hardcoded tag here made those overrides impossible.
 */
// Core prints its own canonical on singular views; this theme prints one for
// every template, so core's would be a duplicate.
remove_action( 'wp_head', 'rel_canonical' );

add_filter(
	'wp_robots',
	static function ( array $robots ): array {
		// Runs late so a plugin that marked the page noindex wins: emitting
		// "index, follow, noindex, nofollow" is contradictory and unreliable.
		if ( ! empty( $robots['noindex'] ) ) {
			unset( $robots['index'], $robots['follow'] );

			return $robots;
		}

		$robots['index']             = true;
		$robots['follow']            = true;
		$robots['max-image-preview'] = 'large';

		return $robots;
	},
	100
);

/**
 * Description used by meta tags and social cards.
 */
function md_meta_description(): string {
	if ( md_is_en_page() ) {
		return 'Senior WordPress and full-stack engineer based in Brasília, Brazil. WordPress at scale, PHP and Laravel back ends, React and TypeScript applications.';
	}

	if ( is_singular() ) {
		// GEO: the direct answer is the best possible summary when present.
		$answer = function_exists( 'get_field' ) ? get_field( 'answer' ) : '';

		if ( is_string( $answer ) && '' !== $answer ) {
			return wp_strip_all_tags( $answer );
		}

		$excerpt = get_the_excerpt();

		if ( '' !== $excerpt ) {
			return wp_strip_all_tags( $excerpt );
		}
	}

	return wp_strip_all_tags( get_bloginfo( 'description' ) );
}

/**
 * JSON-LD graph, tailored per template.
 */
add_action(
	'wp_head',
	static function (): void {
		$site_url = untrailingslashit( home_url() );
		$graph    = array( md_person_schema() );

		if ( is_front_page() ) {
			$graph[] = array(
				'@type'      => 'WebSite',
				'@id'        => $site_url . '/#website',
				'url'        => $site_url,
				'name'       => get_bloginfo( 'name' ),
				'inLanguage' => 'pt-BR',
				'publisher'  => array( '@id' => $site_url . '/#melqui-vunge' ),
			);
		}

		if ( is_singular( 'post' ) ) {
			$graph[] = array(
				'@type'            => 'Article',
				'@id'              => get_permalink() . '#article',
				'headline'         => get_the_title(),
				'description'      => md_meta_description(),
				'datePublished'    => get_the_date( 'c' ),
				'dateModified'     => get_the_modified_date( 'c' ),
				'inLanguage'       => 'pt-BR',
				'author'           => array( '@id' => $site_url . '/#melqui-vunge' ),
				'publisher'        => array( '@id' => $site_url . '/#melqui-vunge' ),
				'mainEntityOfPage' => array( '@type' => 'WebPage', '@id' => get_permalink() ),
			);
		}

		if ( is_singular( 'md_service' ) ) {
			$graph[] = array(
				'@type'       => 'Service',
				'@id'         => get_permalink() . '#service',
				'name'        => get_the_title(),
				'description' => md_meta_description(),
				'provider'    => array( '@id' => $site_url . '/#melqui-vunge' ),
				'areaServed'  => 'BR',
			);

			$faq = md_faq_schema();

			if ( $faq ) {
				$graph[] = $faq;
			}
		}

		md_print_jsonld(
			array(
				'@context' => 'https://schema.org',
				'@graph'   => $graph,
			)
		);
	},
	2
);

/**
 * FAQPage built from the service FAQ repeater — this is what the repeater buys.
 *
 * @return array<string,mixed>|null
 */
function md_faq_schema(): ?array {
	if ( ! function_exists( 'get_field' ) ) {
		return null;
	}

	$faqs = get_field( 'faqs' );

	if ( ! is_array( $faqs ) || ! $faqs ) {
		return null;
	}

	$entities = array();

	foreach ( $faqs as $faq ) {
		if ( empty( $faq['q'] ) || empty( $faq['a'] ) ) {
			continue;
		}

		$entities[] = array(
			'@type'          => 'Question',
			'name'           => wp_strip_all_tags( (string) $faq['q'] ),
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => wp_strip_all_tags( (string) $faq['a'] ),
			),
		);
	}

	if ( ! $entities ) {
		return null;
	}

	return array(
		'@type'      => 'FAQPage',
		'@id'        => get_permalink() . '#faq',
		'mainEntity' => $entities,
	);
}

/**
 * Redirects from the previous site, so the little link equity it had is not
 * lost when the URLs change.
 *
 * @return array<string,string> Old path => new path.
 */
function md_legacy_redirects(): array {
	return array(
		'/10-melhores-drag-and-drop-plugins-para-wordpress-2022' => '/construtores-de-pagina-wordpress/',
		'/blog' => '/notas/',
	);
}

add_action(
	'template_redirect',
	static function (): void {
		if ( ! is_404() ) {
			return;
		}

		$path = untrailingslashit( wp_parse_url( add_query_arg( array() ), PHP_URL_PATH ) ?? '' );
		$map  = md_legacy_redirects();

		if ( isset( $map[ $path ] ) ) {
			wp_safe_redirect( home_url( $map[ $path ] ), 301 );
			exit;
		}
	}
);

/**
 * GEO: let AI crawlers in explicitly.
 *
 * Silence is ambiguous — some agents treat an undeclared user-agent as
 * disallowed. Declaring them also documents the intent for whoever reads
 * robots.txt next.
 */
add_filter(
	'robots_txt',
	static function ( string $output ): string {
		$agents = array(
			'GPTBot',            // OpenAI training + browsing
			'OAI-SearchBot',     // ChatGPT search
			'ChatGPT-User',      // ChatGPT on-demand fetch
			'ClaudeBot',         // Anthropic
			'Claude-User',
			'PerplexityBot',
			'Google-Extended',   // Gemini grounding
			'Applebot-Extended',
			'CCBot',             // Common Crawl — feeds most open models
		);

		$lines = array( '' );

		foreach ( $agents as $agent ) {
			$lines[] = 'User-agent: ' . $agent;
			$lines[] = 'Allow: /';
			$lines[] = '';
		}

		$lines[] = 'LLM-Content: ' . home_url( '/llms.txt' );

		return $output . implode( "\n", $lines ) . "\n";
	}
);

/**
 * GEO: /llms.txt — a map of the site in the plain Markdown agents parse best.
 *
 * Built from live posts so it cannot go stale, unlike a static file.
 */
function md_llms_txt(): string {
	$out = array(
		'# ' . html_entity_decode( get_bloginfo( 'name' ), ENT_QUOTES, 'UTF-8' ),
		'',
		'> ' . md_meta_description(),
		'',
	);

	$sections = array(
		'Serviços' => 'md_service',
		'Projetos' => 'md_project',
		'Notas'    => 'post',
		'Páginas'  => 'page',
	);

	foreach ( $sections as $label => $type ) {
		// ponytail: 50 per type covers this site by an order of magnitude;
		// paginate if it ever outgrows that.
		$posts = get_posts(
			array(
				'post_type'        => $type,
				'posts_per_page'   => 50,
				'orderby'          => 'menu_order date',
				'order'            => 'ASC',
				'suppress_filters' => false,
			)
		);

		if ( ! $posts ) {
			continue;
		}

		$out[] = '## ' . $label;
		$out[] = '';

		foreach ( $posts as $post ) {
			$note = wp_strip_all_tags( get_the_excerpt( $post ) );
			$note = html_entity_decode( $note, ENT_QUOTES, 'UTF-8' );
			$note = trim( preg_replace( '/\s+/', ' ', $note ) ?? '' );

			$out[] = sprintf(
				'- [%s](%s)%s',
				html_entity_decode( get_the_title( $post ), ENT_QUOTES, 'UTF-8' ),
				get_permalink( $post ),
				'' !== $note ? ': ' . $note : ''
			);
		}

		$out[] = '';
	}

	return implode( "\n", $out );
}

add_action(
	'init',
	static function (): void {
		$uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$path = wp_parse_url( $uri, PHP_URL_PATH );

		if ( '/llms.txt' !== $path ) {
			return;
		}

		header( 'Content-Type: text/plain; charset=utf-8' );
		header( 'X-Robots-Tag: noindex' );
		echo md_llms_txt(); // phpcs:ignore WordPress.Security.EscapeOutput -- plain text, not HTML.
		exit;
	}
);

/**
 * Use the hero H1 as the document title on singular views.
 *
 * The post title is an editorial label — several are in English ("Custom Web
 * Systems") while the page itself, its H1 and the whole site are pt-BR. The
 * title tag is what shows in the SERP, so it has to speak the visitor's
 * language and carry the same terms as the heading it belongs to.
 *
 * Reuses the existing `h1` field rather than adding an seo_title: one field to
 * keep in sync is better than two that can drift apart.
 */
add_filter(
	'document_title_parts',
	static function ( array $parts ): array {
		if ( ! is_singular() || ! function_exists( 'get_field' ) ) {
			return $parts;
		}

		$h1 = get_field( 'h1' );

		if ( is_string( $h1 ) && '' !== trim( $h1 ) ) {
			$parts['title'] = trim( $h1 );
		}

		return $parts;
	}
);

/**
 * The English page is the one document on this site that is not pt-BR, and the
 * lang attribute is what a screen reader switches voice on.
 */
add_filter(
	'language_attributes',
	static function ( string $output ): string {
		if ( ! md_is_en_page() ) {
			return $output;
		}

		return (string) preg_replace( '/lang="[^"]*"/', 'lang="en"', $output );
	}
);
