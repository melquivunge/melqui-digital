<?php
/**
 * Template Name: English (one-pager)
 *
 * A single English page for visitors who do not read Portuguese — mostly US
 * companies arriving from LinkedIn. Deliberately not a translated site: the
 * content model is ACF-repeater heavy, so a full mirror would double the cost
 * of every future edit. This page has to make one person understand who I am
 * and how to reach me, and nothing more.
 *
 * Copy lives here rather than in the editor for the same reason the ACF field
 * groups do: it is versioned in git and cannot drift silently.
 *
 * @package MelquiDigital
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

$md_email = md_option( 'contact_email', 'contato@melquivunge.com.br' );

$md_services = array(
	array(
		'WordPress engineering',
		'Custom themes, plugins and Gutenberg blocks in PHP. Multisite and multilingual setups, technical SEO, structured data, scheduled routines and the maintenance work that keeps a production property healthy.',
	),
	array(
		'Custom web systems',
		'Applications built around how a company actually works: records, workflow, permissions, reporting and integrations. PHP and Laravel on the back end, React and TypeScript on the front, a data model meant to last.',
	),
	array(
		'E-commerce and digital platforms',
		'Building and evolving stores and content platforms: catalogue modelling, product pages built for decisions, low-friction checkout, payment and shipping integrations, and an admin the team can actually operate.',
	),
	array(
		'Modernisation, performance and accessibility',
		'Auditing and repairing the technical base: legacy code modernisation including PHP 5 to PHP 8, Core Web Vitals, technical SEO, structured data, accessibility and security — without rebuilding from scratch when it is not warranted.',
	),
);

$md_roles = array(
	array( 'Mar 2024 — present', 'Senior WordPress Developer', 'In All Media, for Indeed', 'WordPress engineering on a large-scale content platform: custom themes, plugins, templates and hooks, multisite and multilingual content, structured data, performance and CI/CD, deployed and operated on WPEngine.' ),
	array( 'Apr 2023 — Mar 2024', 'WordPress Developer', 'Hogarth', 'Back-end and front-end WordPress for corporate web properties, including IBM editorial and security content platforms: migrations, SEO, ongoing support and maintenance.' ),
	array( '2022 — 2023', 'Back-end Developer', 'TITAN SOFTWARE', 'Legacy systems work — new screens and reports over MVC and OOP, and the migration of a PHP 5 codebase to PHP 8. The kind of work that teaches you to change code that cannot stop running.' ),
	array( '2017 — 2022', 'Full-stack Developer', 'Direct clients, then Vepcom (Angola)', 'Requirements, prototype, code, launch and maintenance for direct clients, then the same scope as a professional routine: full-stack PHP and JavaScript, databases, debugging and documentation.' ),
);

get_header();
?>

<article <?php post_class(); ?>>
	<header class="section page-hero">
		<div class="container">
			<p class="eyebrow eyebrow--accent">Senior WordPress &amp; Full-Stack Engineer</p>

			<h1 class="display-xl">Nine years building for the web, from direct clients to enterprise platforms.</h1>

			<p class="page-hero__lede">
				I am Melqui Vunge, a web engineer based in Brasília, Brazil, working remotely
				with international teams. I build and maintain WordPress at scale, PHP and
				Laravel back ends, and React and TypeScript applications.
			</p>

			<p class="mono-link mono-link--muted">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" hreflang="pt-BR" lang="pt-BR">Ver o site em português</a>
			</p>
		</div>
	</header>

	<section class="section" aria-labelledby="en-what">
		<div class="container two-col">
			<div>
				<p class="eyebrow">What I do</p>
				<h2 id="en-what" class="about-h2">Four kinds of work, one engineering standard.</h2>
			</div>

			<dl class="principles">
				<?php foreach ( $md_services as $md_service ) : ?>
					<div>
						<dt><?php echo esc_html( $md_service[0] ); ?></dt>
						<dd><?php echo esc_html( $md_service[1] ); ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>
		</div>
	</section>

	<section class="section section--bordered" aria-labelledby="en-work">
		<div class="container two-col">
			<div>
				<p class="eyebrow">Experience</p>
				<h2 id="en-work" class="about-h2">Where the work has happened.</h2>
			</div>

			<dl class="principles">
				<?php foreach ( $md_roles as $md_role ) : ?>
					<div>
						<dt>
							<?php echo esc_html( $md_role[1] ); ?>
							<span class="eyebrow"><?php echo esc_html( $md_role[2] . ' · ' . $md_role[0] ); ?></span>
						</dt>
						<dd><?php echo esc_html( $md_role[3] ); ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>
		</div>
	</section>

	<section class="section" aria-labelledby="en-contact">
		<div class="container">
			<p class="eyebrow">Contact</p>
			<h2 id="en-contact" class="about-h2">Tell me what the business needs to happen.</h2>

			<p class="lede">
				The clearer the business context, the more concrete my answer. Write in
				English or Portuguese — either is fine.
			</p>

			<p>
				<a class="btn btn--dark" href="mailto:<?php echo esc_attr( $md_email ); ?>">
					<?php echo esc_html( $md_email ); ?>
				</a>
			</p>
		</div>
	</section>
</article>

<?php
get_footer();
