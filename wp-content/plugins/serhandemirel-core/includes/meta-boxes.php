<?php
/**
 * Edit-screen boxes for the fields declared in fields.php.
 *
 * @package serhandemirel-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add one meta box per field group.
 *
 * @param string $post_type Current post type.
 */
function sdc_add_meta_boxes( $post_type ) {
	foreach ( sdc_field_groups() as $id => $group ) {
		if ( in_array( $post_type, $group['post_types'], true ) ) {
			add_meta_box( 'sdc-' . $id, $group['title'], 'sdc_render_meta_box', $post_type, $group['context'], 'high', array( 'group' => $id ) );
		}
	}
}
add_action( 'add_meta_boxes', 'sdc_add_meta_boxes' );

/**
 * Whether the current user may edit a field.
 *
 * @param array $field Field definition.
 * @return bool
 */
function sdc_user_can_edit_field( $field ) {
	return empty( $field['cap'] ) || current_user_can( $field['cap'] );
}

/**
 * Render a field group.
 *
 * @param WP_Post $post Post being edited.
 * @param array   $box  Meta box arguments.
 */
function sdc_render_meta_box( $post, $box ) {
	$group = sdc_field_groups()[ $box['args']['group'] ];
	wp_nonce_field( 'sdc_save_' . $box['args']['group'], 'sdc_nonce_' . $box['args']['group'] );

	echo '<div class="sdc-fields">';
	foreach ( $group['fields'] as $name => $field ) {
		if ( ! sdc_user_can_edit_field( $field ) ) {
			continue;
		}
		sdc_render_field( $name, $field, sdc_get( $post->ID, $name ) );
	}
	echo '</div>';
}

/**
 * Render one input.
 *
 * @param string $name  Field name.
 * @param array  $field Field definition.
 * @param mixed  $value Current value.
 */
function sdc_render_field( $name, $field, $value ) {
	$id         = 'sdc-field-' . $name;
	$input_name = 'sdc[' . $name . ']';

	echo '<div class="sdc-field sdc-field--' . esc_attr( $field['type'] ) . '">';

	if ( 'checkbox' === $field['type'] ) {
		printf(
			'<input type="hidden" name="%1$s" value="0"><label><input type="checkbox" id="%2$s" name="%1$s" value="1" %3$s> %4$s</label>',
			esc_attr( $input_name ),
			esc_attr( $id ),
			checked( (bool) $value, true, false ),
			esc_html( $field['label'] )
		);
	} else {
		printf( '<label for="%s"><strong>%s</strong></label>', esc_attr( $id ), esc_html( $field['label'] ) );

		switch ( $field['type'] ) {
			case 'textarea':
				printf( '<textarea id="%s" name="%s" rows="3">%s</textarea>', esc_attr( $id ), esc_attr( $input_name ), esc_textarea( $value ) );
				break;

			case 'code':
				printf( '<textarea id="%s" name="%s" rows="5" class="code">%s</textarea>', esc_attr( $id ), esc_attr( $input_name ), esc_textarea( $value ) );
				break;

			case 'select':
				printf( '<select id="%s" name="%s">', esc_attr( $id ), esc_attr( $input_name ) );
				$current = '' === $value ? ( $field['default'] ?? '' ) : $value;
				foreach ( $field['options'] as $option => $label ) {
					printf( '<option value="%s" %s>%s</option>', esc_attr( $option ), selected( $current, $option, false ), esc_html( $label ) );
				}
				echo '</select>';
				break;

			case 'post':
				$choices = get_posts(
					array(
						'post_type'      => $field['post_type'],
						'posts_per_page' => 200,
						'orderby'        => 'title',
						'order'          => 'ASC',
						'post_status'    => array( 'publish', 'draft', 'private' ),
						'lang'           => '',
					)
				);
				printf( '<select id="%s" name="%s"><option value="0">%s</option>', esc_attr( $id ), esc_attr( $input_name ), esc_html__( '— None —', 'serhandemirel-core' ) );
				foreach ( $choices as $choice ) {
					printf( '<option value="%d" %s>%s</option>', (int) $choice->ID, selected( (int) $value, $choice->ID, false ), esc_html( get_the_title( $choice ) ) );
				}
				echo '</select>';
				break;

			case 'image':
				$src = sdc_attachment_src( (int) $value, 'medium' );
				printf(
					'<div class="sdc-media" data-multiple="0"><input type="hidden" id="%1$s" name="%2$s" value="%3$s"><div class="sdc-media__preview">%4$s</div><button type="button" class="button sdc-media__pick">%5$s</button> <button type="button" class="button-link sdc-media__clear">%6$s</button></div>',
					esc_attr( $id ),
					esc_attr( $input_name ),
					esc_attr( $value ? (int) $value : '' ),
					$src ? '<img src="' . esc_url( $src ) . '" alt="">' : '',
					esc_html__( 'Choose image', 'serhandemirel-core' ),
					esc_html__( 'Remove', 'serhandemirel-core' )
				);
				break;

			case 'gallery':
				$preview = '';
				foreach ( array_filter( explode( ',', (string) $value ) ) as $attachment_id ) {
					$src = sdc_attachment_src( (int) $attachment_id, 'thumbnail' );
					if ( $src ) {
						$preview .= '<img src="' . esc_url( $src ) . '" alt="">';
					}
				}
				printf(
					'<div class="sdc-media" data-multiple="1"><input type="hidden" id="%1$s" name="%2$s" value="%3$s"><div class="sdc-media__preview">%4$s</div><button type="button" class="button sdc-media__pick">%5$s</button> <button type="button" class="button-link sdc-media__clear">%6$s</button></div>',
					esc_attr( $id ),
					esc_attr( $input_name ),
					esc_attr( $value ),
					$preview, // Built from escaped parts above.
					esc_html__( 'Choose images', 'serhandemirel-core' ),
					esc_html__( 'Clear', 'serhandemirel-core' )
				);
				break;

			default:
				$type = in_array( $field['type'], array( 'url', 'email', 'number' ), true ) ? $field['type'] : 'text';
				printf(
					'<input type="%s" id="%s" name="%s" value="%s">',
					esc_attr( $type ),
					esc_attr( $id ),
					esc_attr( $input_name ),
					esc_attr( 'number' === $type && ! $value ? '' : $value )
				);
		}
	}

	if ( ! empty( $field['help'] ) ) {
		echo '<p class="description">' . esc_html( $field['help'] ) . '</p>';
	}
	echo '</div>';
}

/**
 * Save submitted fields.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 */
function sdc_save_meta_boxes( $post_id, $post ) {
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( empty( $_POST['sdc'] ) || ! is_array( $_POST['sdc'] ) ) {
		return;
	}
	$submitted = wp_unslash( $_POST['sdc'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per field by register_post_meta.

	foreach ( sdc_field_groups() as $id => $group ) {
		if ( ! in_array( $post->post_type, $group['post_types'], true ) ) {
			continue;
		}
		$nonce = isset( $_POST[ 'sdc_nonce_' . $id ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'sdc_nonce_' . $id ] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'sdc_save_' . $id ) ) {
			continue;
		}
		foreach ( $group['fields'] as $name => $field ) {
			if ( ! sdc_user_can_edit_field( $field ) || ! array_key_exists( $name, $submitted ) ) {
				continue;
			}
			update_post_meta( $post_id, sdc_meta_key( $name ), $submitted[ $name ] );
		}
	}
}
add_action( 'save_post', 'sdc_save_meta_boxes', 10, 2 );

/**
 * Media picker script and box styles on edit screens; logo column styles on lists.
 *
 * @param string $hook Admin page hook.
 */
function sdc_admin_assets( $hook ) {
	if ( 'edit.php' === $hook ) {
		wp_enqueue_style( 'sdc-admin', SDC_URL . 'assets/admin.css', array(), SDC_VERSION );
		return;
	}
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script( 'sdc-admin', SDC_URL . 'assets/admin.js', array( 'jquery' ), SDC_VERSION, true );
	wp_enqueue_style( 'sdc-admin', SDC_URL . 'assets/admin.css', array(), SDC_VERSION );
}
add_action( 'admin_enqueue_scripts', 'sdc_admin_assets' );
