<?php
/**
 * The "Serhan Demirel" settings screen in wp-admin.
 *
 * One page with a tab per group in sd_option_tabs(). Tabs with translated
 * fields get a language switcher; saving a tab only touches that tab's
 * fields, and translated fields only in the selected language.
 *
 * @package serhandemirel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the menu page.
 */
function sd_options_menu() {
	add_menu_page(
		__( 'Serhan Demirel theme settings', 'serhandemirel' ),
		__( 'Serhan Demirel', 'serhandemirel' ),
		'manage_options',
		'sd-theme',
		'sd_render_options_page',
		'dashicons-admin-customizer',
		59
	);
}
add_action( 'admin_menu', 'sd_options_menu' );

/**
 * Current tab and language from the URL, validated.
 *
 * @return array{0: string, 1: string}
 */
function sd_options_screen_state() {
	$tabs = sd_option_tabs();
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only navigation.
	$tab  = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'general';
	$lang = isset( $_GET['lang'] ) ? sanitize_key( wp_unslash( $_GET['lang'] ) ) : sd_default_lang();
	// phpcs:enable
	if ( ! isset( $tabs[ $tab ] ) ) {
		$tab = 'general';
	}
	if ( ! isset( sd_languages()[ $lang ] ) ) {
		$lang = sd_default_lang();
	}
	return array( $tab, $lang );
}

/**
 * Whether a tab has fields entered per language.
 *
 * @param string $tab Tab id.
 * @return bool
 */
function sd_tab_has_i18n( $tab ) {
	foreach ( sd_option_tabs()[ $tab ]['fields'] as $field ) {
		if ( ! empty( $field['i18n'] ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Print the settings screen.
 */
function sd_render_options_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	list( $tab, $lang ) = sd_options_screen_state();
	$tabs               = sd_option_tabs();
	$languages          = sd_languages();
	$default_lang       = sd_default_lang();
	$base_url           = admin_url( 'admin.php?page=sd-theme' );
	?>
	<div class="wrap sd-options">
		<h1><?php esc_html_e( 'Serhan Demirel theme settings', 'serhandemirel' ); ?></h1>

		<?php if ( isset( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only. ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved.', 'serhandemirel' ); ?></p></div>
		<?php endif; ?>

		<nav class="nav-tab-wrapper">
			<?php foreach ( $tabs as $id => $def ) : ?>
				<a class="nav-tab <?php echo $id === $tab ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( array( 'tab' => $id, 'lang' => $lang ), $base_url ) ); ?>"><?php echo esc_html( $def['title'] ); ?></a>
			<?php endforeach; ?>
		</nav>

		<?php if ( sd_tab_has_i18n( $tab ) && count( $languages ) > 1 ) : ?>
			<p class="sd-options__langs">
				<?php esc_html_e( 'Editing texts in:', 'serhandemirel' ); ?>
				<?php foreach ( $languages as $slug => $name ) : ?>
					<a class="button <?php echo $slug === $lang ? 'button-primary' : ''; ?>" href="<?php echo esc_url( add_query_arg( array( 'tab' => $tab, 'lang' => $slug ), $base_url ) ); ?>"><?php echo esc_html( $name ); ?></a>
				<?php endforeach; ?>
			</p>
			<?php if ( $lang !== $default_lang ) : ?>
				<p class="description"><?php esc_html_e( 'Fields marked with a globe are saved for this language only. Empty ones show the default-language text on the site (shown in grey). Other fields are shared by all languages.', 'serhandemirel' ); ?></p>
			<?php endif; ?>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="sd_save_options">
			<input type="hidden" name="tab" value="<?php echo esc_attr( $tab ); ?>">
			<input type="hidden" name="lang" value="<?php echo esc_attr( $lang ); ?>">
			<?php wp_nonce_field( 'sd_save_options' ); ?>

			<table class="form-table" role="presentation">
				<?php
				foreach ( $tabs[ $tab ]['fields'] as $key => $field ) {
					if ( ! empty( $field['cap'] ) && ! current_user_can( $field['cap'] ) ) {
						continue;
					}
					sd_render_option_row( $key, $field, $lang, $default_lang, count( $languages ) > 1 );
				}
				?>
			</table>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * Print one settings row.
 *
 * @param string $key          Option key.
 * @param array  $field        Field definition.
 * @param string $lang         Language being edited.
 * @param string $default_lang Default language.
 * @param bool   $multilingual More than one language exists.
 */
function sd_render_option_row( $key, $field, $lang, $default_lang, $multilingual ) {
	$i18n    = ! empty( $field['i18n'] );
	$stored  = sd_stored_value( $key, $i18n ? $lang : null );
	$default = $field['default'] ?? ( 'checkbox' === $field['type'] ? false : '' );

	// Placeholder: what the site shows when this language's field is empty.
	$fallback = $default;
	if ( $i18n && $lang !== $default_lang ) {
		$base     = sd_stored_value( $key, $default_lang );
		$fallback = ( null !== $base && '' !== $base ) ? $base : $default;
	}
	$value = $stored;
	if ( null === $value ) {
		$value = ( $i18n && $lang !== $default_lang ) ? '' : $default;
	}

	$id   = 'sd-opt-' . $key;
	$name = 'sd[' . $key . ']';
	?>
	<tr>
		<th scope="row">
			<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
			<?php if ( $i18n && $multilingual ) : ?>
				<span class="dashicons dashicons-translation" title="<?php esc_attr_e( 'Per language', 'serhandemirel' ); ?>"></span>
			<?php endif; ?>
		</th>
		<td>
			<?php
			switch ( $field['type'] ) {
				case 'checkbox':
					printf(
						'<input type="hidden" name="%1$s" value="0"><label><input type="checkbox" id="%2$s" name="%1$s" value="1" %3$s> %4$s</label>',
						esc_attr( $name ),
						esc_attr( $id ),
						checked( (bool) $value, true, false ),
						esc_html__( 'On', 'serhandemirel' )
					);
					break;
				case 'textarea':
				case 'lines':
				case 'code':
					printf(
						'<textarea id="%s" name="%s" rows="%d" class="large-text %s" placeholder="%s">%s</textarea>',
						esc_attr( $id ),
						esc_attr( $name ),
						'lines' === $field['type'] ? 10 : ( 'code' === $field['type'] ? 6 : 3 ),
						'code' === $field['type'] ? 'code' : '',
						esc_attr( (string) $fallback ),
						esc_textarea( (string) $value )
					);
					break;
				case 'image':
					$src = $value ? wp_get_attachment_image_url( (int) $value, 'medium' ) : '';
					printf(
						'<div class="sd-image"><input type="hidden" id="%1$s" name="%2$s" value="%3$s"><div class="sd-image__preview">%4$s</div><button type="button" class="button sd-image__pick">%5$s</button> <button type="button" class="button-link sd-image__clear">%6$s</button></div>',
						esc_attr( $id ),
						esc_attr( $name ),
						esc_attr( $value ? (int) $value : '' ),
						$src ? '<img src="' . esc_url( $src ) . '" alt="">' : '',
						esc_html__( 'Choose image', 'serhandemirel' ),
						esc_html__( 'Remove', 'serhandemirel' )
					);
					break;
				default:
					printf(
						'<input type="%s" id="%s" name="%s" value="%s" class="regular-text" placeholder="%s">',
						esc_attr( in_array( $field['type'], array( 'url', 'email' ), true ) ? $field['type'] : 'text' ),
						esc_attr( $id ),
						esc_attr( $name ),
						esc_attr( (string) $value ),
						esc_attr( (string) $fallback )
					);
			}
			if ( ! empty( $field['help'] ) ) {
				echo '<p class="description">' . esc_html( $field['help'] ) . '</p>';
			}
			?>
		</td>
	</tr>
	<?php
}

/**
 * Save one tab.
 */
function sd_save_options() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'serhandemirel' ) );
	}
	check_admin_referer( 'sd_save_options' );

	$tabs = sd_option_tabs();
	$tab  = isset( $_POST['tab'] ) ? sanitize_key( wp_unslash( $_POST['tab'] ) ) : '';
	$lang = isset( $_POST['lang'] ) ? sanitize_key( wp_unslash( $_POST['lang'] ) ) : '';
	if ( ! isset( $tabs[ $tab ] ) || ! isset( sd_languages()[ $lang ] ) ) {
		wp_die( esc_html__( 'Unknown settings tab or language.', 'serhandemirel' ) );
	}

	$submitted = isset( $_POST['sd'] ) && is_array( $_POST['sd'] ) ? wp_unslash( $_POST['sd'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per field below.
	$stored    = sd_stored_options();

	foreach ( $tabs[ $tab ]['fields'] as $key => $field ) {
		if ( ( ! empty( $field['cap'] ) && ! current_user_can( $field['cap'] ) ) || ! array_key_exists( $key, $submitted ) ) {
			continue;
		}
		$value = sd_sanitize_option( $submitted[ $key ], $field );
		if ( ! empty( $field['i18n'] ) ) {
			$stored['lang'][ $lang ][ $key ] = $value;
		} else {
			$stored['global'][ $key ] = $value;
		}
	}

	update_option( 'sd_theme_options', $stored );

	wp_safe_redirect( add_query_arg( array( 'page' => 'sd-theme', 'tab' => $tab, 'lang' => $lang, 'updated' => 1 ), admin_url( 'admin.php' ) ) );
	exit;
}
add_action( 'admin_post_sd_save_options', 'sd_save_options' );

/**
 * Media picker and styles for the settings screen.
 *
 * @param string $hook Admin page hook.
 */
function sd_options_assets( $hook ) {
	if ( 'toplevel_page_sd-theme' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	wp_add_inline_style( 'common', '.sd-options__langs .button{margin-right:4px}.sd-options th .dashicons{color:#787c82;font-size:16px;vertical-align:middle}.sd-image__preview img{max-height:120px;width:auto;margin-bottom:6px;border-radius:4px}' );
	wp_add_inline_script(
		'media-editor',
		"jQuery(function($){\$(document).on('click','.sd-image__pick',function(e){e.preventDefault();var box=\$(this).closest('.sd-image');var frame=wp.media({multiple:false,library:{type:'image'}});frame.on('select',function(){var a=frame.state().get('selection').first().toJSON();box.find('input').val(a.id);var s=(a.sizes&&a.sizes.medium)||a;box.find('.sd-image__preview').html(\$('<img alt=\"\">').attr('src',s.url));});frame.open();});\$(document).on('click','.sd-image__clear',function(e){e.preventDefault();var box=\$(this).closest('.sd-image');box.find('input').val('');box.find('.sd-image__preview').empty();});});"
	);
}
add_action( 'admin_enqueue_scripts', 'sd_options_assets' );
