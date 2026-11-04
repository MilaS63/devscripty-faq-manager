<?php
/**
 * "Card style" box on the question edit screen: dark card + optional icon.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'add_meta_boxes', 'dsfaq_add_style_box' );
function dsfaq_add_style_box() {
	add_meta_box( 'dsfaq_style', __( 'Card style', 'devscripty-faq-manager' ), 'dsfaq_render_style_box', 'ds_faq', 'side', 'default' );
}

function dsfaq_render_style_box( $post ) {
	wp_nonce_field( 'dsfaq_style_save', 'dsfaq_style_nonce' );
	$dark = '1' === get_post_meta( $post->ID, '_dsfaq_dark', true );
	$icon = (string) get_post_meta( $post->ID, '_dsfaq_icon', true );
	?>
	<p>
		<label>
			<input type="checkbox" name="dsfaq_dark" value="1" <?php checked( $dark ); ?>>
			<?php esc_html_e( 'Dark card', 'devscripty-faq-manager' ); ?>
		</label>
	</p>
	<p>
		<label for="dsfaq_icon"><?php esc_html_e( 'Icon before the question', 'devscripty-faq-manager' ); ?></label><br>
		<select name="dsfaq_icon" id="dsfaq_icon" style="width:100%;">
			<?php foreach ( dsfaq_icon_choices() as $key => $label ) : ?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $icon, $key ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<?php
}

add_action( 'save_post_ds_faq', 'dsfaq_save_style_box' );
function dsfaq_save_style_box( $post_id ) {
	if ( ! isset( $_POST['dsfaq_style_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dsfaq_style_nonce'] ) ), 'dsfaq_style_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( ! empty( $_POST['dsfaq_dark'] ) ) {
		update_post_meta( $post_id, '_dsfaq_dark', '1' );
	} else {
		delete_post_meta( $post_id, '_dsfaq_dark' );
	}

	$icon = isset( $_POST['dsfaq_icon'] ) ? sanitize_key( wp_unslash( $_POST['dsfaq_icon'] ) ) : '';
	if ( '' !== $icon && array_key_exists( $icon, dsfaq_icon_choices() ) ) {
		update_post_meta( $post_id, '_dsfaq_icon', $icon );
	} else {
		delete_post_meta( $post_id, '_dsfaq_icon' );
	}
}
