<?php
/**
 * One-click importer for the default DevScripty FAQ (admin only).
 * Idempotent: a question whose title already exists is skipped.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'dsfaq_import_menu' );
function dsfaq_import_menu() {
	add_submenu_page(
		'edit.php?post_type=ds_faq',
		__( 'Import DevScripty FAQ', 'devscripty-faq-manager' ),
		__( 'Import DevScripty FAQ', 'devscripty-faq-manager' ),
		'manage_options',
		'dsfaq-import',
		'dsfaq_import_page'
	);
}

/**
 * Create the missing default categories and the questions that do not exist yet.
 *
 * @return array{created:int,skipped:int}
 */
function dsfaq_run_import() {
	$items = require __DIR__ . '/default-faq.php';

	foreach ( dsfaq_default_categories() as $slug => $name ) {
		if ( ! term_exists( $slug, 'ds_faq_cat' ) ) {
			wp_insert_term( $name, 'ds_faq_cat', array( 'slug' => $slug ) );
		}
	}

	$created = 0;
	$skipped = 0;

	foreach ( $items as $i => $item ) {
		$exists = get_posts(
			array(
				'post_type'   => 'ds_faq',
				'post_status' => 'any',
				'title'       => $item['q'],
				'numberposts' => 1,
				'fields'      => 'ids',
			)
		);
		if ( $exists ) {
			++$skipped;
			continue;
		}

		$post_id = wp_insert_post(
			array(
				'post_type'    => 'ds_faq',
				'post_status'  => 'publish',
				'post_title'   => $item['q'],
				'post_content' => $item['a'],
				'menu_order'   => ( $i + 1 ) * 10,
			),
			true
		);
		if ( is_wp_error( $post_id ) ) {
			continue;
		}

		wp_set_object_terms( $post_id, array( $item['cat'] ), 'ds_faq_cat' );
		if ( ! empty( $item['dark'] ) ) {
			update_post_meta( $post_id, '_dsfaq_dark', '1' );
		}
		if ( ! empty( $item['icon'] ) ) {
			update_post_meta( $post_id, '_dsfaq_icon', sanitize_key( $item['icon'] ) );
		}
		++$created;
	}

	return array(
		'created' => $created,
		'skipped' => $skipped,
	);
}

function dsfaq_import_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to access this page.', 'devscripty-faq-manager' ) );
	}

	$result = null;
	if ( isset( $_POST['dsfaq_import_run'] ) ) {
		check_admin_referer( 'dsfaq_import' );
		$result = dsfaq_run_import();
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Import DevScripty FAQ', 'devscripty-faq-manager' ); ?></h1>

		<?php if ( $result ) : ?>
			<div class="notice notice-success is-dismissible">
				<p>
					<?php
					printf(
						/* translators: 1: number created, 2: number skipped */
						esc_html__( 'Done. Created: %1$d, skipped (already exist): %2$d.', 'devscripty-faq-manager' ),
						(int) $result['created'],
						(int) $result['skipped']
					);
					?>
				</p>
			</div>
		<?php endif; ?>

		<?php $counts = wp_count_posts( 'ds_faq' ); ?>
		<p><strong><?php esc_html_e( 'Published questions:', 'devscripty-faq-manager' ); ?></strong> <?php echo (int) $counts->publish; ?> &nbsp;|&nbsp; <strong><?php esc_html_e( 'Shortcode:', 'devscripty-faq-manager' ); ?></strong> <code>[ds_faq open="first"]</code></p>

		<p><?php esc_html_e( 'Creates the five categories and 12 ready-made questions and answers. Existing questions with the same title are skipped, so it is safe to run twice.', 'devscripty-faq-manager' ); ?></p>

		<form method="post">
			<?php wp_nonce_field( 'dsfaq_import' ); ?>
			<?php submit_button( __( 'Import questions', 'devscripty-faq-manager' ), 'primary', 'dsfaq_import_run' ); ?>
		</form>
	</div>
	<?php
}
