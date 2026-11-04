<?php
/**
 * Plugin Name: DevScripty FAQ Manager
 * Description: Manage questions and answers in the WordPress admin and output them anywhere with the [ds_faq] shortcode: category tabs, animated accordion, optional search. No AJAX, no REST calls on the front end.
 * Version:     1.3.1
 * Author:      DevScripty
 * License:     GPL-2.0-or-later
 * Text Domain: devscripty-faq-manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DSFAQ_VERSION', '1.3.1' );
define( 'DSFAQ_URL', plugin_dir_url( __FILE__ ) );

if ( is_admin() ) {
	require_once __DIR__ . '/includes/import.php';
	require_once __DIR__ . '/includes/admin-meta.php';
}

/**
 * Register the FAQ post type and the category taxonomy.
 * Both are non-public: no front-end URLs, no rewrite rules, so no 404/403 surprises.
 * The REST API is off on purpose, which keeps the classic editor for answers.
 */
add_action( 'init', 'dsfaq_register_content_types' );
function dsfaq_register_content_types() {
	register_post_type(
		'ds_faq',
		array(
			'labels'              => array(
				'name'               => __( 'FAQ', 'devscripty-faq-manager' ),
				'singular_name'      => __( 'Question', 'devscripty-faq-manager' ),
				'add_new'            => __( 'Add question', 'devscripty-faq-manager' ),
				'add_new_item'       => __( 'Add new question', 'devscripty-faq-manager' ),
				'edit_item'          => __( 'Edit question', 'devscripty-faq-manager' ),
				'all_items'          => __( 'All questions', 'devscripty-faq-manager' ),
				'search_items'       => __( 'Search questions', 'devscripty-faq-manager' ),
				'not_found'          => __( 'No questions found.', 'devscripty-faq-manager' ),
				'not_found_in_trash' => __( 'No questions found in Trash.', 'devscripty-faq-manager' ),
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => false,
			'menu_icon'           => 'dashicons-editor-help',
			'menu_position'       => 26,
			'supports'            => array( 'title', 'editor', 'page-attributes' ),
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
		)
	);

	register_taxonomy(
		'ds_faq_cat',
		'ds_faq',
		array(
			'labels'            => array(
				'name'          => __( 'FAQ categories', 'devscripty-faq-manager' ),
				'singular_name' => __( 'FAQ category', 'devscripty-faq-manager' ),
			),
			'public'            => false,
			'show_ui'           => true,
			'show_in_rest'      => false,
			'show_admin_column' => true,
			'hierarchical'      => true,
			'rewrite'           => false,
			'query_var'         => false,
		)
	);
}

/**
 * Admin list: sort by the manual "Order" value by default.
 */
add_action( 'pre_get_posts', 'dsfaq_admin_default_order' );
function dsfaq_admin_default_order( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( 'ds_faq' !== $query->get( 'post_type' ) || $query->get( 'orderby' ) ) {
		return;
	}
	$query->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
}

add_filter( 'manage_ds_faq_posts_columns', 'dsfaq_admin_columns' );
function dsfaq_admin_columns( $columns ) {
	$columns['ds_order'] = __( 'Order', 'devscripty-faq-manager' );
	return $columns;
}

add_action( 'manage_ds_faq_posts_custom_column', 'dsfaq_admin_column_content', 10, 2 );
function dsfaq_admin_column_content( $column, $post_id ) {
	if ( 'ds_order' === $column ) {
		echo (int) get_post_field( 'menu_order', $post_id );
	}
}

/**
 * Register front-end assets. They are only enqueued when the shortcode is used.
 */
add_action( 'wp_enqueue_scripts', 'dsfaq_register_assets' );
function dsfaq_register_assets() {
	wp_register_style( 'ds-faq', DSFAQ_URL . 'assets/faq.css', array(), DSFAQ_VERSION );
	wp_register_script( 'ds-faq', DSFAQ_URL . 'assets/faq.js', array(), DSFAQ_VERSION, true );
}

/**
 * Default categories (slug => name). Slugs match the old static markup.
 */
function dsfaq_default_categories() {
	return array(
		'working'    => 'Working With Us',
		'technical'  => 'Technical & Speed',
		'security'   => 'Security & AI Code',
		'pricing'    => 'Pricing & Timelines',
		'guarantees' => 'Guarantees',
	);
}

/**
 * Optional icons shown to the left of a question (set per question in the "Card style" box).
 */
function dsfaq_icon_choices() {
	return array(
		''       => __( 'None', 'devscripty-faq-manager' ),
		'lock'   => __( 'Lock', 'devscripty-faq-manager' ),
		'shield' => __( 'Shield', 'devscripty-faq-manager' ),
		'bolt'   => __( 'Lightning', 'devscripty-faq-manager' ),
		'clock'  => __( 'Clock', 'devscripty-faq-manager' ),
		'check'  => __( 'Check', 'devscripty-faq-manager' ),
	);
}

function dsfaq_icon_svg( $key ) {
	$paths = array(
		'lock'   => '<rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/>',
		'shield' => '<path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z"/>',
		'bolt'   => '<path d="M13 3L5 14h6l-1 7 8-11h-6l1-7z"/>',
		'clock'  => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'check'  => '<circle cx="12" cy="12" r="9"/><path d="M8 12.5l3 3 5-6"/>',
	);
	if ( ! isset( $paths[ $key ] ) ) {
		return '';
	}
	return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths[ $key ] . '</svg>';
}

/**
 * On activation: create the default categories (only if none exist yet).
 * The slugs match the old static markup (data-category values).
 */
register_activation_hook( __FILE__, 'dsfaq_activate' );
function dsfaq_activate() {
	dsfaq_register_content_types();

	$existing = get_terms(
		array(
			'taxonomy'   => 'ds_faq_cat',
			'hide_empty' => false,
			'fields'     => 'ids',
		)
	);
	if ( is_wp_error( $existing ) || ! empty( $existing ) ) {
		return;
	}

	$defaults = dsfaq_default_categories();
	foreach ( $defaults as $slug => $name ) {
		wp_insert_term( $name, 'ds_faq_cat', array( 'slug' => $slug ) );
	}
}

/**
 * Shortcode:
 * [ds_faq category="slug1,slug2" ids="1,2,3" limit="10" open="no|first|all" single="yes|no" tabs="auto|yes|no" search="yes|no" theme="light|dark" schema="yes|no"]
 */
add_shortcode( 'ds_faq', 'dsfaq_shortcode' );
function dsfaq_shortcode( $atts ) {
	static $instance       = 0;
	static $schema_printed = false;

	$a = shortcode_atts(
		array(
			'category' => '',
			'ids'      => '',
			'limit'    => -1,
			'open'     => 'no',
			'single'   => 'yes',
			'tabs'     => 'auto',
			'search'   => 'no',
			'theme'    => 'light',
			'schema'   => 'yes',
		),
		$atts,
		'ds_faq'
	);

	$args = array(
		'post_type'      => 'ds_faq',
		'post_status'    => 'publish',
		'posts_per_page' => (int) $a['limit'],
		'orderby'        => array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		),
		'no_found_rows'  => true,
	);

	if ( '' !== trim( $a['category'] ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'ds_faq_cat',
				'field'    => 'slug',
				'terms'    => array_map( 'sanitize_title', explode( ',', $a['category'] ) ),
			),
		);
	}

	if ( '' !== trim( $a['ids'] ) ) {
		$args['post__in'] = array_filter( array_map( 'absint', explode( ',', $a['ids'] ) ) );
	}

	$query = new WP_Query( $args );
	if ( ! $query->have_posts() ) {
		// Visitors see nothing; logged-in editors get a hint so an empty block is never a mystery.
		if ( current_user_can( 'edit_posts' ) ) {
			return '<div class="ds-faq__notice" style="padding:16px 20px;border:1px dashed #9E886D;border-radius:12px;font:14px/1.5 sans-serif;color:#55524E;">'
				. esc_html__( 'DevScripty FAQ: no published questions match this shortcode (visible to editors only). Add questions under FAQ, or run FAQ > Import DevScripty FAQ.', 'devscripty-faq-manager' )
				. '</div>';
		}
		return '<!-- ds_faq: no published questions -->';
	}

	// Collect the items and the categories that are actually present.
	$items   = array();
	$present = array();
	while ( $query->have_posts() ) {
		$query->the_post();
		$post_id = get_the_ID();
		$slugs   = array();
		$terms   = get_the_terms( $post_id, 'ds_faq_cat' );
		if ( is_array( $terms ) ) {
			foreach ( $terms as $term ) {
				$slugs[]                = $term->slug;
				$present[ $term->slug ] = $term->name;
			}
		}
		$items[] = array(
			'q'    => get_the_title(),
			'a'    => wpautop( wp_kses_post( get_post_field( 'post_content', $post_id ) ) ),
			'cats' => $slugs,
			'dark' => '1' === get_post_meta( $post_id, '_dsfaq_dark', true ),
			'icon' => (string) get_post_meta( $post_id, '_dsfaq_icon', true ),
		);
	}
	wp_reset_postdata();

	// Category tabs, in the order the categories were created. "auto" = only when not already filtered by category.
	$tab_list = array();
	if ( 'no' !== $a['tabs'] && $present ) {
		$show_tabs = ( 'yes' === $a['tabs'] ) || ( '' === trim( $a['category'] ) );
		if ( $show_tabs ) {
			$all_terms = get_terms(
				array(
					'taxonomy'   => 'ds_faq_cat',
					'orderby'    => 'term_id',
					'order'      => 'ASC',
					'hide_empty' => false,
				)
			);
			if ( ! is_wp_error( $all_terms ) ) {
				foreach ( $all_terms as $term ) {
					if ( isset( $present[ $term->slug ] ) ) {
						$tab_list[ $term->slug ] = $term->name;
					}
				}
			}
		}
		if ( count( $tab_list ) < 2 ) {
			$tab_list = array();
		}
	}

	++$instance;
	wp_enqueue_style( 'ds-faq' );
	wp_enqueue_script( 'ds-faq' );

	$open       = in_array( $a['open'], array( 'first', 'all' ), true ) ? $a['open'] : 'no';
	$single     = ( 'yes' === $a['single'] && 'all' !== $open ) ? '1' : '0';
	$has_search = 'yes' === $a['search'];
	$dark_all   = 'dark' === $a['theme'];
	$schema     = array();

	ob_start();
	?>
	<div class="ds-faq<?php echo $dark_all ? ' ds-faq--dark' : ''; ?>" data-ds-faq data-single="<?php echo esc_attr( $single ); ?>">

		<?php if ( $tab_list ) : ?>
			<div class="ds-faq__tabs">
				<button type="button" class="ds-faq__tab is-active" data-category="all" aria-pressed="true"><?php esc_html_e( 'All', 'devscripty-faq-manager' ); ?></button>
				<?php foreach ( $tab_list as $slug => $name ) : ?>
					<button type="button" class="ds-faq__tab" data-category="<?php echo esc_attr( $slug ); ?>" aria-pressed="false"><?php echo esc_html( $name ); ?></button>
				<?php endforeach; ?>
			</div>
			<div class="ds-faq__divider"></div>
		<?php endif; ?>

		<?php if ( $has_search ) : ?>
			<div class="ds-faq__search">
				<label class="ds-faq__sr" for="ds-faq-search-<?php echo (int) $instance; ?>"><?php esc_html_e( 'Search questions', 'devscripty-faq-manager' ); ?></label>
				<input type="search" id="ds-faq-search-<?php echo (int) $instance; ?>" class="ds-faq__search-input" placeholder="<?php esc_attr_e( 'Search questions...', 'devscripty-faq-manager' ); ?>" data-ds-faq-search autocomplete="off">
			</div>
		<?php endif; ?>

		<div class="ds-faq__list">
			<?php
			foreach ( $items as $n => $item ) :
				$is_open  = ( 'all' === $open ) || ( 'first' === $open && 0 === $n );
				$uid      = $instance . '-' . $n;
				$schema[] = array(
					'@type'          => 'Question',
					'name'           => wp_strip_all_tags( $item['q'] ),
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => $item['a'],
					),
				);
				?>
				<div class="ds-faq__item<?php echo $is_open ? ' is-open' : ''; ?><?php echo $item['dark'] ? ' ds-faq__item--dark' : ''; ?>" data-ds-faq-item data-categories="<?php echo esc_attr( implode( ' ', $item['cats'] ) ); ?>">
					<h3 class="ds-faq__heading">
						<button type="button" class="ds-faq__q" id="ds-faq-q-<?php echo esc_attr( $uid ); ?>" aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>" aria-controls="ds-faq-a-<?php echo esc_attr( $uid ); ?>">
							<span class="ds-faq__q-main">
								<?php if ( '' !== $item['icon'] && dsfaq_icon_svg( $item['icon'] ) ) : ?>
									<span class="ds-faq__lead" aria-hidden="true"><?php echo dsfaq_icon_svg( $item['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup built in dsfaq_icon_svg(). ?></span>
								<?php endif; ?>
								<span class="ds-faq__q-text"><?php echo esc_html( $item['q'] ); ?></span>
							</span>
							<span class="ds-faq__icon" aria-hidden="true">
								<svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M3 5.5L7 9.5L11 5.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
							</span>
						</button>
					</h3>
					<div class="ds-faq__a" id="ds-faq-a-<?php echo esc_attr( $uid ); ?>" role="region" aria-labelledby="ds-faq-q-<?php echo esc_attr( $uid ); ?>">
						<div class="ds-faq__a-clip">
							<div class="ds-faq__a-inner"><?php echo $item['a']; // phpcs:ignore WordPress.Security.EscapeOutput -- sanitized with wp_kses_post above. ?></div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<p class="ds-faq__empty" data-ds-faq-empty hidden><?php esc_html_e( 'No matching questions found.', 'devscripty-faq-manager' ); ?></p>
	</div>
	<?php

	// FAQPage structured data: printed once per page. Use schema="no" if another plugin already outputs FAQ schema for the same page.
	if ( 'yes' === $a['schema'] && ! $schema_printed && $schema ) {
		$schema_printed = true;
		$json           = wp_json_encode(
			array(
				'@context'   => 'https://schema.org',
				'@type'      => 'FAQPage',
				'mainEntity' => $schema,
			),
			JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		);
		echo '<script type="application/ld+json">' . $json . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput -- JSON encoded with JSON_HEX_TAG.
	}

	return ob_get_clean();
}
