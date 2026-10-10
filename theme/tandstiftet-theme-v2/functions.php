<?php
/**
 * Tändstiftet MC — Theme Functions
 *
 * @package tandstiftet
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

// Auto-bump on every file change — prevents stale cached CSS/JS after theme updates.
// (Falls back to the static string if filemtime() ever fails, e.g. unusual host permissions.)
$tmc_style_path = get_stylesheet_directory() . '/style.css';
$tmc_js_path     = get_template_directory() . '/assets/js/main.js';
$tmc_style_time  = file_exists( $tmc_style_path ) ? filemtime( $tmc_style_path ) : 0;
$tmc_js_time     = file_exists( $tmc_js_path ) ? filemtime( $tmc_js_path ) : 0;
define( 'TMC_VERSION', ( $tmc_style_time || $tmc_js_time ) ? (string) max( $tmc_style_time, $tmc_js_time ) : '1.0.0' );
define( 'TMC_DIR',       get_template_directory() );
define( 'TMC_URI',       get_template_directory_uri() );

/* =========================================================
   1. THEME SETUP
   ========================================================= */
function tmc_setup() {
	load_theme_textdomain( 'tandstiftet', TMC_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form','comment-form','comment-list','gallery','caption','style','script' ] );
	add_theme_support( 'custom-logo', [
		'height'      => 120,
		'width'       => 120,
		'flex-height' => true,
		'flex-width'  => true,
	] );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'responsive-embeds' );

	// Image sizes
	add_image_size( 'rider-portrait',  480, 640, true );
	add_image_size( 'rider-card',      400, 533, true );
	add_image_size( 'news-thumb',      800, 450, true );
	add_image_size( 'gallery-thumb',   600, 450, true );
	add_image_size( 'sponsor-logo',    300, 150, false );
	add_image_size( 'hero-bg',        1920, 900, true );

	// Nav menus
	register_nav_menus( [
		'primary'          => __( 'Primär navigation', 'tandstiftet' ),
		'footer'           => __( 'Sidfot navigation',  'tandstiftet' ),
		'mobile'           => __( 'Mobil navigation',   'tandstiftet' ),
		'footer-om'        => __( 'Sidfot — Om klubben', 'tandstiftet' ),
		'footer-trackracing' => __( 'Sidfot — Trackracing', 'tandstiftet' ),
		'footer-besok'     => __( 'Sidfot — Besök oss', 'tandstiftet' ),
		'footer-praktiskt' => __( 'Sidfot — Praktiskt', 'tandstiftet' ),
	] );
}
add_action( 'after_setup_theme', 'tmc_setup' );

/* Preconnect to Google Fonts origins — speeds up and stabilizes font loading. */
function tmc_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = [ 'href' => 'https://fonts.googleapis.com' ];
		$urls[] = [ 'href' => 'https://fonts.gstatic.com', 'crossorigin' ];
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'tmc_resource_hints', 10, 2 );

/* =========================================================
   2. ENQUEUE ASSETS
   ========================================================= */
function tmc_enqueue_assets() {
	// Fonts — enqueued as a real stylesheet dependency (not @import) so cache/minify
	// plugins can't silently strip it, and it loads even before consent-gated scripts.
	wp_enqueue_style(
		'tmc-google-fonts',
		'https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow+Condensed:ital,wght@0,300;0,400;0,600;0,700;1,400;1,700&family=Barlow:wght@300;400;500;600&display=swap',
		[],
		null
	);

	wp_enqueue_style(
		'tmc-style',
		get_stylesheet_uri(),
		[ 'tmc-google-fonts' ],
		TMC_VERSION
	);

	wp_enqueue_script(
		'tmc-main',
		TMC_URI . '/assets/js/main.js',
		[],
		TMC_VERSION,
		true
	);

	// Pass data to JS
	wp_localize_script( 'tmc-main', 'TMC', [
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'tmc_nonce' ),
	] );

	// Comment reply script
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'tmc_enqueue_assets' );

/* =========================================================
   3. CUSTOM POST TYPES
   ========================================================= */

/**
 * 3a. Events (tmc_event)
 */
function tmc_register_cpt_event() {
	$labels = [
		'name'               => __( 'Tävlingar & Evenemang', 'tandstiftet' ),
		'singular_name'      => __( 'Evenemang',             'tandstiftet' ),
		'add_new_item'       => __( 'Lägg till evenemang',   'tandstiftet' ),
		'edit_item'          => __( 'Redigera evenemang',    'tandstiftet' ),
		'menu_name'          => __( 'Evenemang',             'tandstiftet' ),
	];
	register_post_type( 'tmc_event', [
		'labels'        => $labels,
		'public'        => true,
		'menu_icon'     => 'dashicons-calendar-alt',
		'menu_position' => 5,
		'supports'      => [ 'title','editor','thumbnail','excerpt','custom-fields' ],
		'has_archive'   => true,
		'rewrite'       => [ 'slug' => 'evenemang' ],
		'show_in_rest'  => true,
	] );
}
add_action( 'init', 'tmc_register_cpt_event' );

/**
 * 3b. Riders / Förare (tmc_rider)
 */
function tmc_register_cpt_rider() {
	$labels = [
		'name'               => __( 'Förare',           'tandstiftet' ),
		'singular_name'      => __( 'Förare',           'tandstiftet' ),
		'add_new_item'       => __( 'Lägg till förare', 'tandstiftet' ),
		'edit_item'          => __( 'Redigera förare',  'tandstiftet' ),
		'menu_name'          => __( 'Förare',           'tandstiftet' ),
	];
	register_post_type( 'tmc_rider', [
		'labels'        => $labels,
		'public'        => true,
		'menu_icon'     => 'dashicons-groups',
		'menu_position' => 6,
		'supports'      => [ 'title','editor','thumbnail','custom-fields','page-attributes' ],
		'has_archive'   => false,
		'rewrite'       => [ 'slug' => 'forare' ],
		'show_in_rest'  => true,
	] );
}
add_action( 'init', 'tmc_register_cpt_rider' );

/**
 * 3c. Sponsors (tmc_sponsor)
 */
function tmc_register_cpt_sponsor() {
	$labels = [
		'name'          => __( 'Sponsorer',           'tandstiftet' ),
		'singular_name' => __( 'Sponsor',             'tandstiftet' ),
		'add_new_item'  => __( 'Lägg till sponsor',   'tandstiftet' ),
		'edit_item'     => __( 'Redigera sponsor',    'tandstiftet' ),
		'menu_name'     => __( 'Sponsorer',           'tandstiftet' ),
	];
	register_post_type( 'tmc_sponsor', [
		'labels'        => $labels,
		'public'        => false,
		'show_ui'       => true,
		'menu_icon'     => 'dashicons-star-filled',
		'menu_position' => 7,
		'supports'      => [ 'title','thumbnail','custom-fields' ],
		'show_in_rest'  => true,
	] );
}
add_action( 'init', 'tmc_register_cpt_sponsor' );

/**
 * 3d. Gallery Albums (tmc_gallery)
 */
function tmc_register_cpt_gallery() {
	$labels = [
		'name'          => __( 'Galleri',               'tandstiftet' ),
		'singular_name' => __( 'Fotoalbum',             'tandstiftet' ),
		'add_new_item'  => __( 'Lägg till fotoalbum',   'tandstiftet' ),
		'edit_item'     => __( 'Redigera fotoalbum',    'tandstiftet' ),
		'menu_name'     => __( 'Galleri',               'tandstiftet' ),
	];
	register_post_type( 'tmc_gallery', [
		'labels'        => $labels,
		'public'        => true,
		'menu_icon'     => 'dashicons-format-gallery',
		'menu_position' => 8,
		'supports'      => [ 'title','editor','thumbnail','custom-fields' ],
		'has_archive'   => true,
		'rewrite'       => [ 'slug' => 'galleri' ],
		'show_in_rest'  => true,
	] );
}
add_action( 'init', 'tmc_register_cpt_gallery' );

/* =========================================================
   4. TAXONOMIES
   ========================================================= */

/**
 * Rider Division (division-1, vuxen, ungdom, veteran …)
 */
function tmc_register_taxonomies() {
	register_taxonomy( 'tmc_division', 'tmc_rider', [
		'labels' => [
			'name'          => __( 'Division / Klass',   'tandstiftet' ),
			'singular_name' => __( 'Division',           'tandstiftet' ),
			'add_new_item'  => __( 'Lägg till division', 'tandstiftet' ),
		],
		'public'            => true,
		'hierarchical'      => false,
		'show_in_rest'      => true,
		'rewrite'           => [ 'slug' => 'division' ],
	] );

	register_taxonomy( 'tmc_event_type', 'tmc_event', [
		'labels' => [
			'name'          => __( 'Tävlingstyp',          'tandstiftet' ),
			'singular_name' => __( 'Typ',                  'tandstiftet' ),
			'add_new_item'  => __( 'Lägg till tävlingstyp','tandstiftet' ),
		],
		'public'       => true,
		'hierarchical' => false,
		'show_in_rest' => true,
		'rewrite'      => [ 'slug' => 'tavlingstyp' ],
	] );

	register_taxonomy( 'tmc_sponsor_tier', 'tmc_sponsor', [
		'labels' => [
			'name'          => __( 'Sponsornivå',        'tandstiftet' ),
			'singular_name' => __( 'Nivå',               'tandstiftet' ),
		],
		'public'       => false,
		'show_ui'      => true,
		'hierarchical' => false,
		'show_in_rest' => true,
	] );
}
add_action( 'init', 'tmc_register_taxonomies' );

/* =========================================================
   5. CUSTOM META BOXES
   ========================================================= */
function tmc_add_meta_boxes() {

	// ---- Rider meta ----
	add_meta_box(
		'tmc_rider_details',
		__( 'Förarinformation', 'tandstiftet' ),
		'tmc_rider_meta_cb',
		'tmc_rider',
		'normal',
		'high'
	);

	// ---- Event meta ----
	add_meta_box(
		'tmc_event_details',
		__( 'Evenemangsinformation', 'tandstiftet' ),
		'tmc_event_meta_cb',
		'tmc_event',
		'normal',
		'high'
	);

	// ---- Sponsor meta ----
	add_meta_box(
		'tmc_sponsor_details',
		__( 'Sponsorinformation', 'tandstiftet' ),
		'tmc_sponsor_meta_cb',
		'tmc_sponsor',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'tmc_add_meta_boxes' );

/* -- Rider meta callback -- */
function tmc_rider_meta_cb( $post ) {
	wp_nonce_field( 'tmc_rider_meta', 'tmc_rider_nonce' );

	$number       = get_post_meta( $post->ID, 'tmc_rider_number', true );
	$bike         = get_post_meta( $post->ID, 'tmc_rider_bike', true );
	$achievements = get_post_meta( $post->ID, 'tmc_rider_achievements', true );
	$is_leader    = get_post_meta( $post->ID, 'tmc_rider_is_leader', true );
	$leader_role  = get_post_meta( $post->ID, 'tmc_rider_leader_role', true );
	$order        = get_post_meta( $post->ID, 'tmc_rider_order', true );

	// Ensure default division terms exist
	$default_terms = [
		'division-1' => 'Division 1',
		'vuxen'      => 'Vuxna',
		'ungdom'     => 'Ungdomar',
	];
	foreach ( $default_terms as $slug => $name ) {
		if ( ! term_exists( $slug, 'tmc_division' ) ) {
			wp_insert_term( $name, 'tmc_division', [ 'slug' => $slug ] );
		}
	}

	$divisions     = get_terms( [ 'taxonomy' => 'tmc_division', 'hide_empty' => false ] );
	$current_terms = wp_get_post_terms( $post->ID, 'tmc_division', [ 'fields' => 'slugs' ] );
	$current_slug  = ! empty( $current_terms ) ? $current_terms[0] : '';
	?>
	<style>
		.tmc-meta-section { margin: 1.2em 0 0.5em; padding: 0 0 0.3em; border-bottom: 1px solid #ddd; }
		.tmc-meta-section h4 { margin: 0; font-size: 13px; text-transform: uppercase; color: #666; letter-spacing: 0.05em; }
		.tmc-meta-hint { color: #888; font-size: 12px; font-style: italic; margin-top: 3px; }
		.tmc-division-select { width: 100%; max-width: 300px; }
	</style>

	<div class="tmc-meta-section"><h4><?php esc_html_e( '👤 Grundinfo', 'tandstiftet' ); ?></h4></div>
	<p class="tmc-meta-hint"><?php esc_html_e( 'Namn skrivs i rubriktfältet "Titel" längst upp på sidan.', 'tandstiftet' ); ?></p>

	<table class="form-table">
		<tr>
			<th><label for="tmc_rider_number"><?php esc_html_e( 'Startnummer', 'tandstiftet' ); ?></label></th>
			<td><input type="text" id="tmc_rider_number" name="tmc_rider_number" value="<?php echo esc_attr( $number ); ?>" class="small-text">
			<p class="tmc-meta-hint"><?php esc_html_e( 'T.ex. 12 eller 77', 'tandstiftet' ); ?></p></td>
		</tr>
		<tr>
			<th><label for="tmc_rider_bike"><?php esc_html_e( 'Cykel / Maskinfabrikat', 'tandstiftet' ); ?></label></th>
			<td><input type="text" id="tmc_rider_bike" name="tmc_rider_bike" value="<?php echo esc_attr( $bike ); ?>" class="regular-text">
			<p class="tmc-meta-hint"><?php esc_html_e( 'T.ex. Jawa 500cc eller Honda CRF', 'tandstiftet' ); ?></p></td>
		</tr>
	</table>

	<div class="tmc-meta-section"><h4><?php esc_html_e( '🏁 Division / Klass', 'tandstiftet' ); ?></h4></div>
	<p class="tmc-meta-hint"><?php esc_html_e( 'Välj vilken sektion föraren ska visas under på Förare & Lag-sidan.', 'tandstiftet' ); ?></p>
	<table class="form-table">
		<tr>
			<th><label for="tmc_rider_division"><?php esc_html_e( 'Division', 'tandstiftet' ); ?></label></th>
			<td>
				<select id="tmc_rider_division" name="tmc_rider_division" class="tmc-division-select">
					<option value=""><?php esc_html_e( '— Välj division —', 'tandstiftet' ); ?></option>
					<?php foreach ( $divisions as $term ) : ?>
						<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $current_slug, $term->slug ); ?>>
							<?php echo esc_html( $term->name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
	</table>

	<div class="tmc-meta-section"><h4><?php esc_html_e( '🖼 Foto', 'tandstiftet' ); ?></h4></div>
	<p class="tmc-meta-hint"><?php esc_html_e( 'Sätt "Inläggsbild" (längst ned till höger på sidan) som förarens porträtt.', 'tandstiftet' ); ?></p>

	<div class="tmc-meta-section"><h4><?php esc_html_e( '🏆 Meriter', 'tandstiftet' ); ?></h4></div>
	<table class="form-table">
		<tr>
			<th><label for="tmc_rider_achievements"><?php esc_html_e( 'Meriter', 'tandstiftet' ); ?></label></th>
			<td><textarea id="tmc_rider_achievements" name="tmc_rider_achievements" rows="4" class="large-text"><?php echo esc_textarea( $achievements ); ?></textarea>
			<p class="tmc-meta-hint"><?php esc_html_e( 'En merit per rad, t.ex: SM-guld 2023', 'tandstiftet' ); ?></p></td>
		</tr>
	</table>

	<div class="tmc-meta-section"><h4><?php esc_html_e( '👔 Lagledare / Tränare', 'tandstiftet' ); ?></h4></div>
	<table class="form-table">
		<tr>
			<th><label for="tmc_rider_is_leader"><?php esc_html_e( 'Är lagledare eller tränare', 'tandstiftet' ); ?></label></th>
			<td><input type="checkbox" id="tmc_rider_is_leader" name="tmc_rider_is_leader" value="1" <?php checked( 1, $is_leader ); ?>>
			<p class="tmc-meta-hint"><?php esc_html_e( 'Bocka i om personen är lagledare, tränare eller liknande (visas separat)', 'tandstiftet' ); ?></p></td>
		</tr>
		<tr>
			<th><label for="tmc_rider_leader_role"><?php esc_html_e( 'Ledarroll', 'tandstiftet' ); ?></label></th>
			<td><input type="text" id="tmc_rider_leader_role" name="tmc_rider_leader_role" value="<?php echo esc_attr( $leader_role ); ?>" class="regular-text">
			<p class="tmc-meta-hint"><?php esc_html_e( 'T.ex. Lagkapten, Tränare, Mekaniker', 'tandstiftet' ); ?></p></td>
		</tr>
	</table>

	<div class="tmc-meta-section"><h4><?php esc_html_e( '⚙️ Visningsinställningar', 'tandstiftet' ); ?></h4></div>
	<table class="form-table">
		<tr>
			<th><label for="tmc_rider_order"><?php esc_html_e( 'Sorteringsordning', 'tandstiftet' ); ?></label></th>
			<td><input type="number" id="tmc_rider_order" name="tmc_rider_order" value="<?php echo esc_attr( $order ?: 10 ); ?>" class="small-text" min="0" max="999">
			<p class="tmc-meta-hint"><?php esc_html_e( 'Lägre siffra = visas först. Standard: 10', 'tandstiftet' ); ?></p></td>
		</tr>
	</table>
	<?php
}

/* -- Event meta callback -- */
function tmc_event_meta_cb( $post ) {
	wp_nonce_field( 'tmc_event_meta', 'tmc_event_nonce' );
	$fields = [
		'tmc_event_date'     => [ __( 'Datum (YYYY-MM-DD)', 'tandstiftet' ), 'date'   ],
		'tmc_event_time'     => [ __( 'Tid (HH:MM)', 'tandstiftet' ),        'time'   ],
		'tmc_event_location' => [ __( 'Plats / Bana', 'tandstiftet' ),       'text'   ],
		'tmc_event_address'  => [ __( 'Adress', 'tandstiftet' ),             'text'   ],
		'tmc_event_price'    => [ __( 'Inträde / Pris', 'tandstiftet' ),     'text'   ],
		'tmc_event_facebook' => [ __( 'Facebook-evenemangslänk', 'tandstiftet'), 'url'],
	];
	echo '<table class="form-table">';
	foreach ( $fields as $key => [$label, $type] ) {
		$val = get_post_meta( $post->ID, $key, true );
		echo '<tr><th><label for="' . esc_attr($key) . '">' . esc_html($label) . '</label></th><td>';
		echo '<input type="' . esc_attr($type) . '" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" value="' . esc_attr($val) . '" class="regular-text">';
		echo '</td></tr>';
	}
	echo '</table>';
}

/* -- Sponsor meta callback -- */
function tmc_sponsor_meta_cb( $post ) {
	wp_nonce_field( 'tmc_sponsor_meta', 'tmc_sponsor_nonce' );
	$fields = [
		'tmc_sponsor_url'     => [ __( 'Webbplats URL', 'tandstiftet' ), 'url'  ],
		'tmc_sponsor_tagline' => [ __( 'Slogan / Kort text', 'tandstiftet' ), 'text' ],
		'tmc_sponsor_order'   => [ __( 'Visningsordning', 'tandstiftet' ), 'number' ],
	];
	echo '<table class="form-table">';
	foreach ( $fields as $key => [$label, $type] ) {
		$val = get_post_meta( $post->ID, $key, true );
		echo '<tr><th><label for="' . esc_attr($key) . '">' . esc_html($label) . '</label></th><td>';
		echo '<input type="' . esc_attr($type) . '" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" value="' . esc_attr($val) . '" class="regular-text">';
		echo '</td></tr>';
	}
	echo '</table>';
}

/* -- Save meta -- */
function tmc_save_meta( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

	// Rider
	if ( isset( $_POST['tmc_rider_nonce'] ) && wp_verify_nonce( $_POST['tmc_rider_nonce'], 'tmc_rider_meta' ) ) {
		$rider_fields = [ 'tmc_rider_number','tmc_rider_bike','tmc_rider_achievements','tmc_rider_leader_role','tmc_rider_order' ];
		foreach ( $rider_fields as $field ) {
			if ( isset( $_POST[$field] ) ) {
				update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ) );
			}
		}
		// Save division taxonomy
		if ( isset( $_POST['tmc_rider_division'] ) ) {
			$division = sanitize_text_field( $_POST['tmc_rider_division'] );
			if ( $division ) {
				wp_set_post_terms( $post_id, [ $division ], 'tmc_division' );
			} else {
				wp_set_post_terms( $post_id, [], 'tmc_division' );
			}
		}
		// Default order to 10 if empty
		if ( empty( $_POST['tmc_rider_order'] ) ) {
			update_post_meta( $post_id, 'tmc_rider_order', 10 );
		}
		update_post_meta( $post_id, 'tmc_rider_is_leader', isset( $_POST['tmc_rider_is_leader'] ) ? 1 : 0 );
	}

	// Event
	if ( isset( $_POST['tmc_event_nonce'] ) && wp_verify_nonce( $_POST['tmc_event_nonce'], 'tmc_event_meta' ) ) {
		$event_fields = [ 'tmc_event_date','tmc_event_time','tmc_event_location','tmc_event_address','tmc_event_price','tmc_event_buy_url','tmc_event_facebook' ];
		foreach ( $event_fields as $field ) {
			if ( isset( $_POST[$field] ) ) {
				update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ) );
			}
		}
	}

	// Sponsor
	if ( isset( $_POST['tmc_sponsor_nonce'] ) && wp_verify_nonce( $_POST['tmc_sponsor_nonce'], 'tmc_sponsor_meta' ) ) {
		$sponsor_fields = [ 'tmc_sponsor_url','tmc_sponsor_tagline','tmc_sponsor_order' ];
		foreach ( $sponsor_fields as $field ) {
			if ( isset( $_POST[$field] ) ) {
				update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ) );
			}
		}
	}
}
add_action( 'save_post', 'tmc_save_meta' );

/* =========================================================
   6. WIDGETS / SIDEBARS
   ========================================================= */
function tmc_register_sidebars() {
	$args = [
		'before_widget' => '<div class="sidebar-widget" id="%1$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="sidebar-widget__title">',
		'after_title'   => '</h3>',
	];

	register_sidebar( array_merge( $args, [
		'name' => __( 'Sidfält – Sidor', 'tandstiftet' ),
		'id'   => 'sidebar-pages',
	] ) );

	register_sidebar( array_merge( $args, [
		'name' => __( 'Sidfält – Nyheter', 'tandstiftet' ),
		'id'   => 'sidebar-news',
	] ) );

	register_sidebar( array_merge( $args, [
		'name' => __( 'Sidfot – Kolumn 1', 'tandstiftet' ),
		'id'   => 'footer-1',
	] ) );

	register_sidebar( array_merge( $args, [
		'name' => __( 'Sidfot – Kolumn 2', 'tandstiftet' ),
		'id'   => 'footer-2',
	] ) );

	register_sidebar( array_merge( $args, [
		'name' => __( 'Sidfot – Kolumn 3', 'tandstiftet' ),
		'id'   => 'footer-3',
	] ) );
}
add_action( 'widgets_init', 'tmc_register_sidebars' );

/* =========================================================
   7. HELPER FUNCTIONS
   ========================================================= */

/**
 * Output the page/archive header banner.
 */
/* =========================================================
   Sliding — paus-flagga (styrs från Utseende > Anpassa > Sliding — Paus/Aktiv)
   ========================================================= */
function tmc_sliding_enabled() {
	return (bool) get_theme_mod( 'tmc_sliding_enabled', true );
}

/* Separate kill-switch just for Flat Track, since the club may run Sliding
   with Speedway only for a while. When off: no Flat Track card, no partner
   callout, no Flat Track option in the booking form, and no Flat Track slots
   in the calendar — Speedway keeps working as normal. */
function tmc_flattrack_enabled() {
	return (bool) get_theme_mod( 'tmc_flattrack_enabled', true );
}

/* Prova På (barn & ungdom) — säsong öppen/stängd. Styrs från
   Utseende > Anpassa > Prova På — Säsong. Stänger bara boknings-/
   kontaktdelen av sidan, inte hela sidan (till skillnad från Sliding-pausen). */
function tmc_provapa_season_open() {
	return (bool) get_theme_mod( 'tmc_provapa_season_open', true );
}

function tmc_provapa_season_message() {
	$default = __( 'Vi har just nu inga fler Prova På-tillfällen den här säsongen. Vi öppnar upp igen till nästa säsong — håll utkik här eller på våra sociala kanaler!', 'tandstiftet' );
	return get_theme_mod( 'tmc_provapa_season_message', $default );
}

/* Automatically strip any "Sliding" link from every nav menu while paused,
   so no manual menu editing is needed to hide/restore it. */
function tmc_maybe_hide_sliding_menu_items( $items, $args ) {
	if ( tmc_sliding_enabled() ) {
		return $items;
	}
	return array_filter( $items, function( $item ) {
		return strpos( $item->url, 'sliding' ) === false;
	} );
}
add_filter( 'wp_nav_menu_objects', 'tmc_maybe_hide_sliding_menu_items', 10, 2 );

/* =========================================================
   Sliding — Bokningskalender
   ========================================================= */

/* ── Post types ── */
add_action( 'init', function() {

	register_post_type( 'tmc_sliding_booking', [
		'labels' => [
			'name'          => __( 'Sliding-bokningar', 'tandstiftet' ),
			'singular_name' => __( 'Bokning', 'tandstiftet' ),
			'menu_name'     => __( 'Sliding-bokningar', 'tandstiftet' ),
			'add_new_item'  => __( 'Ny bokning', 'tandstiftet' ),
		],
		'public'        => false,
		'show_ui'       => true,
		'menu_icon'     => 'dashicons-calendar-alt',
		'menu_position' => 25,
		'supports'      => [ 'title' ],
	] );

	register_post_type( 'tmc_sliding_availability', [
		'labels' => [
			'name'          => __( 'Sliding — Datum & kapacitet', 'tandstiftet' ),
			'singular_name' => __( 'Datuminställning', 'tandstiftet' ),
			'menu_name'     => __( 'Sliding — Datum', 'tandstiftet' ),
			'add_new_item'  => __( 'Lägg till datuminställning', 'tandstiftet' ),
		],
		'public'        => false,
		'show_ui'       => true,
		'menu_icon'     => 'dashicons-clock',
		'menu_position' => 26,
		'supports'      => [ 'title' ],
	] );
} );

/* ── Meta box: Bokningsinfo (on tmc_sliding_booking) ── */
add_action( 'add_meta_boxes', function() {
	add_meta_box( 'tmc_booking_info', __( 'Bokningsinfo', 'tandstiftet' ), 'tmc_booking_info_cb', 'tmc_sliding_booking', 'normal', 'high' );
} );

function tmc_booking_info_cb( $post ) {
	wp_nonce_field( 'tmc_booking_save', 'tmc_booking_nonce' );
	$fields = [
		'tmc_booking_date'       => 'Datum (ÅÅÅÅ-MM-DD)',
		'tmc_booking_name'       => 'Namn',
		'tmc_booking_email'      => 'E-post',
		'tmc_booking_phone'      => 'Telefon',
		'tmc_booking_age'        => 'Ålder',
		'tmc_booking_shoe'       => 'Skostorlek',
		'tmc_booking_height'     => 'Längd',
		'tmc_booking_discipline' => 'Önskad disciplin',
		'tmc_booking_group_type' => 'Typ av bokning',
		'tmc_booking_group_size' => 'Antal personer',
	];
	echo '<table class="form-table">';
	foreach ( $fields as $key => $label ) {
		$val = get_post_meta( $post->ID, $key, true );
		printf(
			'<tr><th style="width:180px"><label for="%1$s">%2$s</label></th><td><input type="text" id="%1$s" name="%1$s" value="%3$s" style="width:100%%;max-width:400px"></td></tr>',
			esc_attr( $key ), esc_html( $label ), esc_attr( $val )
		);
	}
	$msg = get_post_meta( $post->ID, 'tmc_booking_message', true );
	printf(
		'<tr><th style="width:180px"><label for="tmc_booking_message">Övrigt</label></th><td><textarea id="tmc_booking_message" name="tmc_booking_message" rows="3" style="width:100%%;max-width:400px">%s</textarea></td></tr>',
		esc_textarea( $msg )
	);
	$status = get_post_meta( $post->ID, 'tmc_booking_status', true ) ?: 'confirmed';
	echo '<tr><th>Status</th><td><select name="tmc_booking_status">';
	echo '<option value="confirmed"' . selected( $status, 'confirmed', false ) . '>Bekräftad</option>';
	echo '<option value="cancelled"' . selected( $status, 'cancelled', false ) . '>Avbokad</option>';
	echo '</select></td></tr>';
	echo '</table>';
}

add_action( 'save_post_tmc_sliding_booking', function( $post_id ) {
	if ( ! isset( $_POST['tmc_booking_nonce'] ) || ! wp_verify_nonce( $_POST['tmc_booking_nonce'], 'tmc_booking_save' ) ) return;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	$fields = [ 'tmc_booking_date','tmc_booking_name','tmc_booking_email','tmc_booking_phone','tmc_booking_age','tmc_booking_shoe','tmc_booking_height','tmc_booking_discipline','tmc_booking_group_type','tmc_booking_group_size','tmc_booking_status' ];
	foreach ( $fields as $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, sanitize_text_field( $_POST[ $key ] ) );
		}
	}
	if ( isset( $_POST['tmc_booking_message'] ) ) {
		update_post_meta( $post_id, 'tmc_booking_message', sanitize_textarea_field( $_POST['tmc_booking_message'] ) );
	}
} );

/* Admin list columns for bookings */
add_filter( 'manage_tmc_sliding_booking_posts_columns', function( $cols ) {
	$new = [];
	$new['cb'] = $cols['cb'];
	$new['title'] = __( 'Bokning', 'tandstiftet' );
	$new['tmc_date'] = __( 'Datum', 'tandstiftet' );
	$new['tmc_contact'] = __( 'Kontakt', 'tandstiftet' );
	$new['tmc_group'] = __( 'Personer', 'tandstiftet' );
	$new['tmc_discipline'] = __( 'Disciplin', 'tandstiftet' );
	$new['tmc_status'] = __( 'Status', 'tandstiftet' );
	return $new;
} );
add_action( 'manage_tmc_sliding_booking_posts_custom_column', function( $column, $post_id ) {
	switch ( $column ) {
		case 'tmc_date':
			echo esc_html( get_post_meta( $post_id, 'tmc_booking_date', true ) );
			break;
		case 'tmc_contact':
			echo esc_html( get_post_meta( $post_id, 'tmc_booking_email', true ) );
			$phone = get_post_meta( $post_id, 'tmc_booking_phone', true );
			if ( $phone ) echo '<br>' . esc_html( $phone );
			break;
		case 'tmc_group':
			echo esc_html( get_post_meta( $post_id, 'tmc_booking_group_size', true ) ?: '1' );
			$type = get_post_meta( $post_id, 'tmc_booking_group_type', true );
			if ( $type ) echo '<br><small>' . esc_html( $type ) . '</small>';
			break;
		case 'tmc_discipline':
			$d = get_post_meta( $post_id, 'tmc_booking_discipline', true );
			echo esc_html( $d === 'flattrack' ? 'Flat track' : 'Speedway' );
			break;
		case 'tmc_status':
			$status = get_post_meta( $post_id, 'tmc_booking_status', true ) ?: 'confirmed';
			echo $status === 'cancelled'
				? '<span style="color:#c0392b;font-weight:600">Avbokad</span>'
				: '<span style="color:#2a8a4a;font-weight:600">Bekräftad</span>';
			break;
	}
}, 10, 2 );
add_filter( 'manage_edit-tmc_sliding_booking_sortable_columns', function( $cols ) {
	$cols['tmc_date'] = 'tmc_date';
	return $cols;
} );
add_action( 'pre_get_posts', function( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) return;
	if ( $query->get( 'post_type' ) !== 'tmc_sliding_booking' ) return;
	if ( $query->get( 'orderby' ) === 'tmc_date' ) {
		$query->set( 'meta_key', 'tmc_booking_date' );
		$query->set( 'orderby', 'meta_value' );
	} else {
		// default: soonest date first
		$query->set( 'meta_key', 'tmc_booking_date' );
		$query->set( 'orderby', 'meta_value' );
		$query->set( 'order', 'ASC' );
	}
} );

/* ── Meta box: Datuminställning (on tmc_sliding_availability) ── */
add_action( 'add_meta_boxes', function() {
	add_meta_box( 'tmc_avail_info', __( 'Datuminställning', 'tandstiftet' ), 'tmc_avail_info_cb', 'tmc_sliding_availability', 'normal', 'high' );
} );

function tmc_avail_info_cb( $post ) {
	wp_nonce_field( 'tmc_avail_save', 'tmc_avail_nonce' );
	$date       = get_post_meta( $post->ID, 'tmc_avail_date', true );
	$cap        = get_post_meta( $post->ID, 'tmc_avail_capacity', true );
	$closed     = get_post_meta( $post->ID, 'tmc_avail_closed', true );
	$discipline = get_post_meta( $post->ID, 'tmc_avail_discipline', true ) ?: 'both';
	echo '<table class="form-table">';
	printf( '<tr><th style="width:200px">%s</th><td><input type="date" name="tmc_avail_date" value="%s" required></td></tr>', esc_html__( 'Datum', 'tandstiftet' ), esc_attr( $date ) );
	echo '<tr><th>' . esc_html__( 'Gäller disciplin', 'tandstiftet' ) . '</th><td><select name="tmc_avail_discipline">';
	echo '<option value="both"' . selected( $discipline, 'both', false ) . '>' . esc_html__( 'Båda (Speedway & Flat track)', 'tandstiftet' ) . '</option>';
	echo '<option value="speedway"' . selected( $discipline, 'speedway', false ) . '>' . esc_html__( 'Bara Speedway', 'tandstiftet' ) . '</option>';
	echo '<option value="flattrack"' . selected( $discipline, 'flattrack', false ) . '>' . esc_html__( 'Bara Flat track', 'tandstiftet' ) . '</option>';
	echo '</select></td></tr>';
	printf( '<tr><th>%s</th><td><input type="number" min="0" name="tmc_avail_capacity" value="%s" placeholder="%s"> <span style="color:#888">%s</span></td></tr>',
		esc_html__( 'Egen kapacitet (antal personer)', 'tandstiftet' ), esc_attr( $cap ),
		esc_attr( tmc_sliding_daily_capacity_default() ),
		esc_html__( 'Lämna tomt för att använda standardvärdet ovan', 'tandstiftet' )
	);
	printf( '<tr><th>%s</th><td><label><input type="checkbox" name="tmc_avail_closed" value="1" %s> %s</label></td></tr>',
		esc_html__( 'Stängt', 'tandstiftet' ), checked( $closed, '1', false ), esc_html__( 'Stäng det här datumet (för vald disciplin ovan) helt för bokning, oavsett kapacitet', 'tandstiftet' )
	);
	echo '</table>';
	echo '<p style="color:#888">' . esc_html__( 'Titeln sätts automatiskt till datum + disciplin när du sparar.', 'tandstiftet' ) . '</p>';
}

add_action( 'save_post_tmc_sliding_availability', function( $post_id ) {
	if ( ! isset( $_POST['tmc_avail_nonce'] ) || ! wp_verify_nonce( $_POST['tmc_avail_nonce'], 'tmc_avail_save' ) ) return;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

	$date       = sanitize_text_field( $_POST['tmc_avail_date'] ?? '' );
	$discipline = sanitize_text_field( $_POST['tmc_avail_discipline'] ?? 'both' );
	update_post_meta( $post_id, 'tmc_avail_date', $date );
	update_post_meta( $post_id, 'tmc_avail_discipline', $discipline );
	update_post_meta( $post_id, 'tmc_avail_capacity', sanitize_text_field( $_POST['tmc_avail_capacity'] ?? '' ) );
	update_post_meta( $post_id, 'tmc_avail_closed', isset( $_POST['tmc_avail_closed'] ) ? '1' : '' );

	if ( $date ) {
		$labels = [ 'both' => 'Båda', 'speedway' => 'Speedway', 'flattrack' => 'Flat track' ];
		$title  = $date . ' — ' . ( $labels[ $discipline ] ?? 'Båda' );
		remove_action( 'save_post_tmc_sliding_availability', __FUNCTION__ );
		wp_update_post( [ 'ID' => $post_id, 'post_title' => $title ] );
	}
} );

add_filter( 'manage_tmc_sliding_availability_posts_columns', function( $cols ) {
	return [
		'cb'             => $cols['cb'],
		'title'          => __( 'Datum', 'tandstiftet' ),
		'tmc_discipline' => __( 'Disciplin', 'tandstiftet' ),
		'tmc_cap'        => __( 'Kapacitet', 'tandstiftet' ),
		'tmc_closed'     => __( 'Stängt?', 'tandstiftet' ),
	];
} );
add_action( 'manage_tmc_sliding_availability_posts_custom_column', function( $column, $post_id ) {
	if ( $column === 'tmc_discipline' ) {
		$labels = [ 'both' => 'Båda', 'speedway' => 'Speedway', 'flattrack' => 'Flat track' ];
		$discipline = get_post_meta( $post_id, 'tmc_avail_discipline', true ) ?: 'both';
		echo esc_html( $labels[ $discipline ] ?? 'Båda' );
	}
	if ( $column === 'tmc_cap' ) {
		$cap = get_post_meta( $post_id, 'tmc_avail_capacity', true );
		echo $cap !== '' ? esc_html( $cap ) : '<em>standard (' . esc_html( tmc_sliding_daily_capacity_default() ) . ')</em>';
	}
	if ( $column === 'tmc_closed' ) {
		echo get_post_meta( $post_id, 'tmc_avail_closed', true ) === '1' ? '✅' : '—';
	}
}, 10, 2 );

/* ── Helper functions: capacity / bookings / remaining (per discipline) ── */
function tmc_sliding_daily_capacity_default( $discipline = 'speedway' ) {
	$key = $discipline === 'flattrack' ? 'tmc_sliding_daily_capacity_flattrack' : 'tmc_sliding_daily_capacity_speedway';
	return (int) get_theme_mod( $key, 8 );
}

/* Weekly schedule — which weekdays each discipline is offered on, and at what time.
   Controlled by the board under Utseende → Anpassa → Sliding — Bokningskalender.
   Empty string = that weekday isn't offered for that discipline. */
function tmc_sliding_weekday_time( $date, $discipline = 'speedway' ) {
	$d = DateTime::createFromFormat( 'Y-m-d', $date );
	if ( ! $d ) return '';
	$days = [ 1=>'mon', 2=>'tue', 3=>'wed', 4=>'thu', 5=>'fri', 6=>'sat', 7=>'sun' ];
	$iso = (int) $d->format( 'N' ); // 1 (Mon) .. 7 (Sun)
	$suffix = $discipline === 'flattrack' ? '_flattrack' : '_speedway';
	$key = 'tmc_sliding_hours_' . $days[ $iso ] . $suffix;
	return trim( (string) get_theme_mod( $key, '' ) );
}

function tmc_sliding_is_weekday_open( $date, $discipline = 'speedway' ) {
	return tmc_sliding_weekday_time( $date, $discipline ) !== '';
}

function tmc_sliding_get_availability_post( $date, $discipline = 'speedway' ) {
	$q = new WP_Query( [
		'post_type'      => 'tmc_sliding_availability',
		'posts_per_page' => 1,
		'post_status'    => 'publish',
		'no_found_rows'  => true,
		'meta_query'     => [
			'relation' => 'AND',
			[ 'key' => 'tmc_avail_date', 'value' => $date ],
			[
				'relation' => 'OR',
				[ 'key' => 'tmc_avail_discipline', 'value' => $discipline ],
				[ 'key' => 'tmc_avail_discipline', 'value' => 'both' ],
				[ 'key' => 'tmc_avail_discipline', 'compare' => 'NOT EXISTS' ],
			],
		],
	] );
	return $q->have_posts() ? $q->posts[0] : null;
}

function tmc_sliding_get_capacity( $date, $discipline = 'speedway' ) {
	if ( $discipline === 'flattrack' && ! tmc_flattrack_enabled() ) return 0;

	$avail = tmc_sliding_get_availability_post( $date, $discipline );
	if ( $avail ) {
		// An explicit per-date entry always wins — lets the board open a
		// normally-closed weekday for a one-off event, or close a normally-open one.
		if ( get_post_meta( $avail->ID, 'tmc_avail_closed', true ) === '1' ) return 0;
		$cap = get_post_meta( $avail->ID, 'tmc_avail_capacity', true );
		if ( $cap !== '' ) return (int) $cap;
		return tmc_sliding_daily_capacity_default( $discipline );
	}
	// No override — fall back to the weekly schedule.
	if ( ! tmc_sliding_is_weekday_open( $date, $discipline ) ) return 0;
	return tmc_sliding_daily_capacity_default( $discipline );
}

function tmc_sliding_get_booked_count( $date, $discipline = 'speedway' ) {
	$q = new WP_Query( [
		'post_type'      => 'tmc_sliding_booking',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'fields'         => 'ids',
		'no_found_rows'  => true,
		'meta_query'     => [
			[ 'key' => 'tmc_booking_date', 'value' => $date ],
			[ 'key' => 'tmc_booking_discipline', 'value' => $discipline ],
		],
	] );
	$total = 0;
	foreach ( $q->posts as $pid ) {
		$status = get_post_meta( $pid, 'tmc_booking_status', true ) ?: 'confirmed';
		if ( $status === 'cancelled' ) continue;
		$size = (int) get_post_meta( $pid, 'tmc_booking_group_size', true );
		$total += $size > 0 ? $size : 1;
	}
	return $total;
}

function tmc_sliding_get_remaining( $date, $discipline = 'speedway' ) {
	return max( 0, tmc_sliding_get_capacity( $date, $discipline ) - tmc_sliding_get_booked_count( $date, $discipline ) );
}

/* Minimum lead time before a slot can be booked online. If the date's
   midnight is less than 24h away, visitors are asked to contact the club
   directly instead — gives the trainer/board time to actually prepare. */
function tmc_sliding_is_within_24h( $date, $now = null ) {   // $now: optional fixed time ('Y-m-d H:i:s'), used by the tests
	$slot_start = DateTime::createFromFormat( 'Y-m-d H:i:s', $date . ' 00:00:00' );
	if ( ! $slot_start ) return true;
	$now    = new DateTime( $now ?: 'now' );
	$diff_h = ( $slot_start->getTimestamp() - $now->getTimestamp() ) / 3600;
	return $diff_h < 24;
}

/* Human-readable list of which weekdays+times a discipline is offered on,
   e.g. ["Tisdagar kl 18:00", "Torsdagar kl 18:00"] — used for the on-page summary line. */
function tmc_sliding_schedule_summary( $weekday_labels, $days_map, $discipline_suffix ) {
	$summary = [];
	foreach ( $weekday_labels as $iso => $label ) {
		$time = trim( (string) get_theme_mod( 'tmc_sliding_hours_' . $days_map[ $iso ] . $discipline_suffix, '' ) );
		if ( $time !== '' ) $summary[] = $label . ' kl ' . $time;
	}
	return $summary;
}

/* =========================================================
   Anti-spam helpers — used by every form on the site
   (Kontakt, Bli medlem, Prova På, Sliding).

   Free, no external service required:
   1. Honeypot field  — invisible to humans, bots often fill it in.
   2. Time trap       — real people take more than a couple of seconds
                         to read a form and fill it in; bots submit instantly.
   3. Field validation — sane bounds on age, and a check for spammy
                         link-stuffed text in free-text fields.
   ========================================================= */

/* Render the honeypot field + timestamp — call this inside every <form>. */
function tmc_antispam_fields() {
	?>
	<div style="position:absolute;left:-9999px;top:-9999px;opacity:0;height:0;overflow:hidden" aria-hidden="true">
		<label>Website <input type="text" name="tmc_hp_website" tabindex="-1" autocomplete="off"></label>
	</div>
	<input type="hidden" name="tmc_form_ts" value="<?php echo esc_attr( time() ); ?>">
	<?php
}

/* Blockeringslista — Utseende → Anpassa → Formulär: Blockeringslista.
   One entry per line: a full email ("spam@example.com") or an entire
   domain ("@example.com") to block every address at that domain. */
function tmc_antispam_is_blocked_email( $email ) {
	if ( ! $email ) return false;
	$raw = get_theme_mod( 'tmc_antispam_blocklist', '' );
	if ( trim( $raw ) === '' ) return false;

	$email = strtolower( trim( $email ) );
	$at_pos = strpos( $email, '@' );
	$domain = $at_pos !== false ? substr( $email, $at_pos ) : ''; // includes the "@"

	$lines = preg_split( '/[\r\n,]+/', $raw );
	foreach ( $lines as $line ) {
		$line = strtolower( trim( $line ) );
		if ( $line === '' ) continue;
		if ( $line === $email ) return true;
		if ( $domain && $line === $domain ) return true;
	}
	return false;
}

/* True if the submission looks bot-generated (honeypot filled, submitted
   suspiciously fast, or the e-mail address is on the blocklist).
   Call right after nonce verification. */
function tmc_antispam_is_bot( $min_seconds = 3 ) {
	if ( ! empty( $_POST['tmc_hp_website'] ) ) return true;
	$ts = (int) ( $_POST['tmc_form_ts'] ?? 0 );
	if ( ! $ts || ( time() - $ts ) < $min_seconds ) return true;

	// Self-detect the e-mail field regardless of which form called us.
	foreach ( [ 'pp_email', 'contact_email' ] as $field ) {
		if ( ! empty( $_POST[ $field ] ) ) {
			if ( tmc_antispam_is_blocked_email( sanitize_email( $_POST[ $field ] ) ) ) return true;
			break;
		}
	}
	return false;
}

/* Sane age bounds — rejects bot-garbage like "9999" or "abc123". */
function tmc_antispam_valid_age( $age, $min = 1, $max = 100 ) {
	if ( $age === '' ) return true; // optional field on most forms
	if ( ! ctype_digit( trim( (string) $age ) ) ) return false;
	$n = (int) $age;
	return $n >= $min && $n <= $max;
}

/* For the "Ålder/födelseår" field on the membership form, which accepts either
   a plain age (1–100) or a 4-digit birth year (1920 – this year). */
function tmc_antispam_valid_age_or_year( $value ) {
	if ( $value === '' ) return true;
	$value = trim( (string) $value );
	if ( ! ctype_digit( $value ) ) return false;
	$n = (int) $value;
	if ( strlen( $value ) === 4 ) {
		return $n >= 1920 && $n <= (int) date( 'Y' );
	}
	return $n >= 1 && $n <= 100;
}

/* Flags free-text fields stuffed with links — a very common bot pattern
   ("great post, check out http://... http://... http://...”). */
function tmc_antispam_text_is_spammy( $text, $max_links = 1 ) {
	if ( ! $text ) return false;
	$link_count = preg_match_all( '#https?://|www\.#i', $text );
	if ( $link_count > $max_links ) return true;
	if ( strlen( $text ) > 3000 ) return true; // wall-of-text is another common bot tell
	return false;
}

/* ── CSV export (wp-admin → Sliding-bokningar) ── */
add_action( 'restrict_manage_posts', function( $post_type ) {
	if ( $post_type !== 'tmc_sliding_booking' ) return;
	$url = wp_nonce_url( admin_url( 'admin-post.php?action=tmc_export_sliding_bookings' ), 'tmc_export_bookings' );
	echo '<a href="' . esc_url( $url ) . '" class="button" style="margin-left:6px">⬇ ' . esc_html__( 'Exportera CSV', 'tandstiftet' ) . '</a>';
} );

add_action( 'admin_post_tmc_export_sliding_bookings', function() {
	if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Behörighet saknas.' );
	check_admin_referer( 'tmc_export_bookings' );

	$posts = get_posts( [
		'post_type'      => 'tmc_sliding_booking',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'orderby'        => 'meta_value',
		'meta_key'       => 'tmc_booking_date',
		'order'          => 'ASC',
	] );

	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=sliding-bokningar-' . date( 'Y-m-d' ) . '.csv' );

	$out = fopen( 'php://output', 'w' );
	fwrite( $out, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) ); // UTF-8 BOM so Excel/Sweden renders åäö correctly
	fputcsv( $out, [ 'Datum','Namn','E-post','Telefon','Ålder','Skostorlek','Längd','Disciplin','Typ av bokning','Antal personer','Status','Övrigt' ], ';' );

	foreach ( $posts as $p ) {
		fputcsv( $out, [
			get_post_meta( $p->ID, 'tmc_booking_date', true ),
			get_post_meta( $p->ID, 'tmc_booking_name', true ),
			get_post_meta( $p->ID, 'tmc_booking_email', true ),
			get_post_meta( $p->ID, 'tmc_booking_phone', true ),
			get_post_meta( $p->ID, 'tmc_booking_age', true ),
			get_post_meta( $p->ID, 'tmc_booking_shoe', true ),
			get_post_meta( $p->ID, 'tmc_booking_height', true ),
			get_post_meta( $p->ID, 'tmc_booking_discipline', true ),
			get_post_meta( $p->ID, 'tmc_booking_group_type', true ),
			get_post_meta( $p->ID, 'tmc_booking_group_size', true ),
			get_post_meta( $p->ID, 'tmc_booking_status', true ) ?: 'confirmed',
			get_post_meta( $p->ID, 'tmc_booking_message', true ),
		], ';' );
	}
	fclose( $out );
	exit;
} );

/* Build calendar availability data for the next N days, for one or both disciplines. */
function tmc_sliding_get_calendar_data( $days = 90 ) {
	$disciplines = tmc_flattrack_enabled() ? [ 'speedway', 'flattrack' ] : [ 'speedway' ];
	$data = [];
	$today = new DateTime( 'today' );
	foreach ( $disciplines as $discipline ) {
		$data[ $discipline ] = [];
		for ( $i = 0; $i < $days; $i++ ) {
			$d = ( clone $today )->modify( "+{$i} days" );
			$key = $d->format( 'Y-m-d' );
			$cap = tmc_sliding_get_capacity( $key, $discipline );
			$data[ $discipline ][ $key ] = [
				'open'      => $cap > 0,
				'remaining' => tmc_sliding_get_remaining( $key, $discipline ),
				'within24h' => tmc_sliding_is_within_24h( $key ),
				'time'      => tmc_sliding_weekday_time( $key, $discipline ),
			];
		}
	}
	return $data;
}

/* =========================================================
   Startsida — Viktigt meddelande (notisbanderoll)
   Styrs helt från Utseende → Anpassa → Startsida — Viktigt meddelande.
   ========================================================= */
function tmc_render_notice_banner() {
	if ( ! get_theme_mod( 'tmc_notice_enabled', false ) ) return;

	$title     = get_theme_mod( 'tmc_notice_title', '' );
	$text      = get_theme_mod( 'tmc_notice_text', '' );
	$link_text = get_theme_mod( 'tmc_notice_link_text', '' );
	$link_url  = get_theme_mod( 'tmc_notice_link_url', '' );
	$style     = get_theme_mod( 'tmc_notice_style', 'info' ) === 'urgent' ? 'urgent' : 'info';

	if ( ! $title && ! $text ) return; // nothing to show

	// Dismissal is remembered per message — if the board edits the text,
	// the hash changes and the notice reappears for everyone even if they
	// dismissed an earlier version.
	$dismiss_key = 'tmc_notice_' . substr( md5( $title . '|' . $text ), 0, 10 );
	?>
	<div class="tmc-notice tmc-notice--<?php echo esc_attr( $style ); ?>" id="tmc-notice-banner" data-dismiss-key="<?php echo esc_attr( $dismiss_key ); ?>" hidden>
		<div class="container tmc-notice__inner">
			<div class="tmc-notice__icon" aria-hidden="true"><?php echo $style === 'urgent' ? '⚠' : '📣'; ?></div>
			<div class="tmc-notice__body">
				<?php if ( $title ) : ?><strong class="tmc-notice__title"><?php echo esc_html( $title ); ?></strong><?php endif; ?>
				<?php if ( $text ) : ?><p class="tmc-notice__text"><?php echo nl2br( esc_html( $text ) ); ?></p><?php endif; ?>
			</div>
			<?php if ( $link_text && $link_url ) : ?>
				<a href="<?php echo esc_url( $link_url ); ?>" class="tmc-notice__link"><?php echo esc_html( $link_text ); ?> →</a>
			<?php endif; ?>
			<button type="button" class="tmc-notice__close" id="tmc-notice-close" aria-label="<?php esc_attr_e( 'Stäng meddelandet', 'tandstiftet' ); ?>">✕</button>
		</div>
	</div>
	<script>
	(function(){
		var key = 'tmc_notice_dismissed';
		var el = document.getElementById('tmc-notice-banner');
		if (!el) return;
		var dismissKey = el.dataset.dismissKey;
		try {
			if (localStorage.getItem(key) !== dismissKey) {
				el.hidden = false;
			}
		} catch (e) {
			el.hidden = false; // no localStorage available — just show it
		}
		var closeBtn = document.getElementById('tmc-notice-close');
		if (closeBtn) {
			closeBtn.addEventListener('click', function(){
				el.hidden = true;
				try { localStorage.setItem(key, dismissKey); } catch (e) {}
			});
		}
	})();
	</script>
	<?php
}


function tmc_page_header( $title = '', $subtitle = '', $data_title = '' ) {
	if ( ! $title ) $title = get_the_title();
	if ( ! $data_title ) $data_title = strtoupper( wp_strip_all_tags( html_entity_decode( $title, ENT_QUOTES, 'UTF-8' ) ) );
	?>
	<div class="page-header" data-title="<?php echo esc_attr( $data_title ); ?>">
		<div class="page-header__inner">
			<?php tmc_breadcrumbs(); ?>
			<div class="label" style="margin-bottom:0.5rem"><?php echo esc_html( get_theme_mod( 'tmc_page_header_label', get_bloginfo('name') ) ); ?></div>
			<h1 class="section-title"><?php echo esc_html( $title ); ?></h1>
			<?php if ( $subtitle ) : ?>
				<p class="hero__subtitle"><?php echo esc_html( $subtitle ); ?></p>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

/**
 * Simple breadcrumbs.
 */
function tmc_breadcrumbs() {
	echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Brödsmulor', 'tandstiftet' ) . '">';
	echo '<a href="' . esc_url( home_url('/') ) . '">' . esc_html__( 'Start', 'tandstiftet' ) . '</a>';
	echo '<span>›</span>';

	if ( is_home() ) {
		// Blog posts page
		$posts_page = get_option('page_for_posts');
		if ( $posts_page ) {
			echo '<span>' . esc_html( get_the_title( $posts_page ) ) . '</span>';
		} else {
			echo '<span>' . esc_html__( 'Nyheter', 'tandstiftet' ) . '</span>';
		}
	} elseif ( is_category() ) {
		$posts_page = get_option('page_for_posts');
		if ( $posts_page ) {
			echo '<a href="' . esc_url( get_permalink( $posts_page ) ) . '">' . esc_html( get_the_title( $posts_page ) ) . '</a>';
			echo '<span>›</span>';
		}
		echo '<span>' . esc_html( single_term_title( '', false ) ) . '</span>';
	} elseif ( is_tag() ) {
		echo '<span>' . esc_html__( 'Tagg', 'tandstiftet' ) . ': ' . esc_html( single_term_title( '', false ) ) . '</span>';
	} elseif ( is_singular('post') ) {
		$posts_page = get_option('page_for_posts');
		if ( $posts_page ) {
			echo '<a href="' . esc_url( get_permalink( $posts_page ) ) . '">' . esc_html( get_the_title( $posts_page ) ) . '</a>';
			echo '<span>›</span>';
		}
		echo '<span>' . esc_html( get_the_title() ) . '</span>';
	} elseif ( is_singular() ) {
		echo '<span>' . esc_html( get_the_title() ) . '</span>';
	} elseif ( is_post_type_archive() ) {
		echo '<span>' . esc_html( post_type_archive_title( '', false ) ) . '</span>';
	} elseif ( is_tax() ) {
		echo '<span>' . esc_html( single_term_title( '', false ) ) . '</span>';
	} elseif ( is_date() ) {
		$posts_page = get_option('page_for_posts');
		if ( $posts_page ) {
			echo '<a href="' . esc_url( get_permalink( $posts_page ) ) . '">' . esc_html( get_the_title( $posts_page ) ) . '</a>';
			echo '<span>›</span>';
		}
		if ( is_day() ) {
			echo '<span>' . esc_html( get_the_date('j F Y') ) . '</span>';
		} elseif ( is_month() ) {
			echo '<span>' . esc_html( get_the_date('F Y') ) . '</span>';
		} elseif ( is_year() ) {
			echo '<span>' . esc_html( get_the_date('Y') ) . '</span>';
		}
	} elseif ( is_author() ) {
		echo '<span>' . esc_html__( 'Författare', 'tandstiftet' ) . '</span>';
	} elseif ( is_search() ) {
		echo '<span>' . esc_html__( 'Sökresultat', 'tandstiftet' ) . '</span>';
	} elseif ( is_404() ) {
		echo '<span>404</span>';
	}

	echo '</nav>';
}

/**
 * Get upcoming events, optionally limited.
 */
function tmc_get_upcoming_events( $count = 5 ) {
	return new WP_Query( [
		'post_type'      => 'tmc_event',
		'posts_per_page' => $count,
		'meta_key'       => 'tmc_event_date',
		'orderby'        => 'meta_value',
		'order'          => 'ASC',
		'meta_query'     => [ [
			'key'     => 'tmc_event_date',
			'value'   => date( 'Y-m-d' ),
			'compare' => '>=',
			'type'    => 'DATE',
		] ],
	] );
}

/**
 * Get riders, optionally filtered by division slug.
 */
function tmc_get_riders( $division_slug = '', $count = -1 ) {
	$args = [
		'post_type'      => 'tmc_rider',
		'posts_per_page' => $count,
		'orderby'        => [ 'meta_value_num' => 'ASC', 'title' => 'ASC' ],
		'meta_query'     => [
			'relation' => 'OR',
			[
				'key'     => 'tmc_rider_order',
				'compare' => 'EXISTS',
			],
			[
				'key'     => 'tmc_rider_order',
				'compare' => 'NOT EXISTS',
			],
		],
	];
	if ( $division_slug ) {
		$args['tax_query'] = [ [
			'taxonomy' => 'tmc_division',
			'field'    => 'slug',
			'terms'    => $division_slug,
		] ];
	}
	return new WP_Query( $args );
}

/**
 * Render a single rider card.
 */
function tmc_rider_card( $post_id ) {
	$post = get_post( $post_id );
	if ( ! $post ) return;

	$number   = get_post_meta( $post_id, 'tmc_rider_number', true );
	$bike     = get_post_meta( $post_id, 'tmc_rider_bike', true );
	$merits   = get_post_meta( $post_id, 'tmc_rider_achievements', true );
	$is_leader= get_post_meta( $post_id, 'tmc_rider_is_leader', true );

	$divisions = get_the_terms( $post_id, 'tmc_division' );
	$div_slug  = ( $divisions && ! is_wp_error($divisions) ) ? $divisions[0]->slug : '';
	$div_name  = ( $divisions && ! is_wp_error($divisions) ) ? $divisions[0]->name : '';

	$tag_class = 'rider-card__division-tag';
	if ( strpos( $div_slug, 'div' ) !== false )        $tag_class .= ' rider-card__division-tag--div1';
	elseif ( strpos( $div_slug, 'ungdom' ) !== false ) $tag_class .= ' rider-card__division-tag--ungdom';
	else                                               $tag_class .= ' rider-card__division-tag--vuxen';
	?>
	<article class="rider-card" id="rider-<?php echo $post_id; ?>">
		<div class="rider-card__photo-wrap">
			<?php if ( has_post_thumbnail( $post_id ) ) : ?>
				<?php echo get_the_post_thumbnail( $post_id, 'rider-card', [ 'class' => 'rider-card__photo', 'loading' => 'lazy' ] ); ?>
			<?php else : ?>
				<div class="rider-card__photo" style="background:var(--color-asphalt);display:flex;align-items:center;justify-content:center;height:100%;">
					<svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="1.5"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
				</div>
			<?php endif; ?>
			<?php if ( $number ) : ?>
				<span class="rider-card__number"><?php echo esc_html( $number ); ?></span>
			<?php endif; ?>
		</div>
		<div class="rider-card__body">
			<?php if ( $div_name ) : ?>
				<span class="<?php echo esc_attr($tag_class); ?>"><?php echo esc_html($div_name); ?></span>
			<?php endif; ?>
			<h3 class="rider-card__name"><?php echo esc_html( $post->post_title ); ?></h3>
			<?php if ( $bike ) : ?>
				<p class="rider-card__meta">🏍 <?php echo esc_html( $bike ); ?></p>
			<?php endif; ?>
			<?php if ( $post->post_content ) : ?>
				<p class="rider-card__bio"><?php echo esc_html( wp_trim_words( $post->post_content, 30 ) ); ?></p>
			<?php endif; ?>
			<?php if ( $merits ) : ?>
				<p class="rider-card__achievements">🏆 <?php echo nl2br( esc_html( $merits ) ); ?></p>
			<?php endif; ?>
		</div>
	</article>
	<?php
}

/**
 * Render a single event card (compact).
 */
function tmc_event_card( $post_id ) {
	$post     = get_post( $post_id );
	$date_raw = get_post_meta( $post_id, 'tmc_event_date', true );
	$time     = get_post_meta( $post_id, 'tmc_event_time', true );
	$location = get_post_meta( $post_id, 'tmc_event_location', true );

	$day   = $date_raw ? date_i18n( 'j', strtotime( $date_raw ) ) : '?';
	$month = $date_raw ? date_i18n( 'M', strtotime( $date_raw ) ) : '';
	?>
	<article class="event-card">
		<div class="event-card__date-box">
			<span class="event-card__day"><?php echo esc_html($day); ?></span>
			<span class="event-card__month"><?php echo esc_html($month); ?></span>
		</div>
		<div class="event-card__info">
			<h3 class="event-card__title">
				<a href="<?php echo esc_url( get_permalink($post_id) ); ?>"><?php echo esc_html( $post->post_title ); ?></a>
			</h3>
			<p class="event-card__meta">
				<?php if ( $location ) echo esc_html( $location ); ?>
				<?php if ( $time ) echo ' · ' . esc_html( $time ); ?>
			</p>
			<?php if ( $post->post_excerpt ) : ?>
				<p class="event-card__desc"><?php echo esc_html( $post->post_excerpt ); ?></p>
			<?php endif; ?>
		</div>
	</article>
	<?php
}

/* =========================================================
   8. FLUSH REWRITE ON ACTIVATION
   ========================================================= */
function tmc_flush_rewrite_rules() {
	tmc_register_cpt_event();
	tmc_register_cpt_rider();
	tmc_register_cpt_sponsor();
	tmc_register_cpt_gallery();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'tmc_flush_rewrite_rules' );

/* =========================================================
   9. EXCERPT LENGTH
   ========================================================= */
add_filter( 'excerpt_length', fn() => 25 );
add_filter( 'excerpt_more',   fn() => '…' );

/* =========================================================
   10. CUSTOM WALKER FOR PRIMARY NAV
   ========================================================= */
class TMC_Walker_Nav extends Walker_Nav_Menu {
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$classes   = empty( $item->classes ) ? [] : (array) $item->classes;
		$class_str = implode( ' ', array_filter( $classes ) );
		$atts      = [];
		$atts['href']   = ! empty( $item->url ) ? $item->url : '#';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( $value ) $attributes .= ' ' . $attr . '="' . esc_attr( $value ) . '"';
		}

		$output .= '<li class="' . esc_attr( $class_str ) . '">';
		$output .= '<a' . $attributes . '>' . esc_html( $item->title ) . '</a>';
	}
}
/* Hero title with the last word highlighted in gold, e.g.
   "Motorklubben Tändstiftet" → "Motorklubben <span>Tändstiftet</span>".
   A single word is left as-is. Output is escaped HTML. */
function tmc_hero_title_html( $raw ) {
	$words = preg_split( '/\s+/', trim( (string) $raw ) );
	if ( count( $words ) > 1 ) {
		$last = array_pop( $words );
		return esc_html( implode( ' ', $words ) ) . ' <span>' . esc_html( $last ) . '</span>';
	}
	return esc_html( (string) $raw );
}

/* =========================================================
   v2 — Carry Customizer settings over from the previous theme
   ---------------------------------------------------------
   WordPress stores Customizer settings (logo, contact info, hero images, menu
   assignments, Sliding/Prova På switches, notice banner …) per theme folder.
   Activating "v2" would therefore look empty. On first activation we copy the
   previous Tändstiftet theme's settings into v2 — the old theme is left
   untouched, so switching back is always safe. Never overwrites existing v2
   settings.
   ========================================================= */
function tmc_import_previous_theme_mods() {
	$current  = get_option( 'stylesheet' );
	$old_slug = '';
	foreach ( wp_get_themes() as $slug => $theme ) {
		if ( $slug !== $current && $theme->get( 'TextDomain' ) === 'tandstiftet' ) { $old_slug = $slug; break; }
	}
	if ( ! $old_slug ) return;

	$old = get_option( "theme_mods_{$old_slug}" );
	if ( ! is_array( $old ) || empty( $old ) ) return;

	$new  = get_option( "theme_mods_{$current}" );
	$new  = is_array( $new ) ? $new : [];
	$real = array_diff_key( $new, [ 'nav_menu_locations' => 1, 0 => 1 ] );
	if ( ! empty( $real ) ) return; // v2 already has its own settings — leave them alone.

	$merged = $old;
	if ( ! empty( $new['nav_menu_locations'] ) ) {
		$merged['nav_menu_locations'] = $new['nav_menu_locations'];
	}
	update_option( "theme_mods_{$current}", $merged );
}
add_action( 'after_switch_theme', 'tmc_import_previous_theme_mods' );

/* =========================================================
   v2 — "Tävlingar" is reachable via the "Nästa match" strip only.
   It is hidden from the main menu unless switched on under
   Utseende → Anpassa → Header & meny. No manual menu editing needed.
   ========================================================= */
function tmc_maybe_hide_events_menu_item( $items, $args ) {
	if ( empty( $args->theme_location ) || $args->theme_location !== 'primary' ) return $items;
	if ( get_theme_mod( 'tmc_nav_show_events', false ) ) return $items;
	return array_values( array_filter( $items, function( $item ) {
		$url   = strtolower( (string) $item->url );
		$title = strtolower( str_replace( [ 'Å', 'Ä', 'Ö' ], [ 'å', 'ä', 'ö' ], trim( wp_strip_all_tags( (string) $item->title ) ) ) ); // no mbstring dependency
		if ( $title === 'tävlingar' ) return false;
		return strpos( $url, '/tavlingar' ) === false && strpos( $url, '/evenemang' ) === false;
	} ) );
}
add_filter( 'wp_nav_menu_objects', 'tmc_maybe_hide_events_menu_item', 10, 2 );

require_once TMC_DIR . '/inc/customizer.php';

/* =========================================================
   11. STYRELSE — Custom Post Type + Meta
   ========================================================= */

function tmc_register_cpt_board() {
	register_post_type( 'tmc_board', [
		'labels' => [
			'name'          => __( 'Styrelse',              'tandstiftet' ),
			'singular_name' => __( 'Styrelsemedlem',        'tandstiftet' ),
			'add_new_item'  => __( 'Lägg till medlem',      'tandstiftet' ),
			'edit_item'     => __( 'Redigera medlem',       'tandstiftet' ),
			'menu_name'     => __( 'Styrelse',              'tandstiftet' ),
		],
		'public'        => false,
		'show_ui'       => true,
		'menu_icon'     => 'dashicons-id',
		'menu_position' => 9,
		'supports'      => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
		'show_in_rest'  => true,
	] );
}
add_action( 'init', 'tmc_register_cpt_board' );

function tmc_board_meta_box() {
	add_meta_box(
		'tmc_board_details',
		__( 'Styrelseinfo', 'tandstiftet' ),
		'tmc_board_meta_cb',
		'tmc_board',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'tmc_board_meta_box' );

function tmc_board_meta_cb( $post ) {
	wp_nonce_field( 'tmc_board_meta', 'tmc_board_nonce' );
	$fields = [
		'tmc_board_role'     => [ __( 'Roll / Titel (t.ex. Ordförande)', 'tandstiftet' ), 'text'   ],
		'tmc_board_email'    => [ __( 'E-post (valfri)',                  'tandstiftet' ), 'email'  ],
		'tmc_board_phone'    => [ __( 'Telefon (valfri)',                 'tandstiftet' ), 'text'   ],
		'tmc_board_type'     => [ __( 'Typ',                             'tandstiftet' ), 'select' ],
		'tmc_board_order'    => [ __( 'Visningsordning (lägre = först)', 'tandstiftet' ), 'number' ],
	];
	$type_options = [
		'featured'    => __( 'Ordförande / Vice (stor bild)', 'tandstiftet' ),
		'board'       => __( 'Övrig styrelse (liten bild)',   'tandstiftet' ),
		'nomination'  => __( 'Valberedning (ingen bild)',     'tandstiftet' ),
	];
	echo '<table class="form-table">';
	foreach ( $fields as $key => [$label, $type] ) {
		$val = get_post_meta( $post->ID, $key, true );
		echo '<tr><th><label for="' . esc_attr($key) . '">' . esc_html($label) . '</label></th><td>';
		if ( $type === 'select' ) {
			echo '<select id="' . esc_attr($key) . '" name="' . esc_attr($key) . '">';
			foreach ( $type_options as $v => $l ) {
				echo '<option value="' . esc_attr($v) . '"' . selected($val, $v, false) . '>' . esc_html($l) . '</option>';
			}
			echo '</select>';
		} else {
			echo '<input type="' . esc_attr($type) . '" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" value="' . esc_attr($val) . '" class="regular-text">';
		}
		echo '</td></tr>';
	}
	echo '</table>';
}

function tmc_save_board_meta( $post_id ) {
	if ( ! isset( $_POST['tmc_board_nonce'] ) ) return;
	if ( ! wp_verify_nonce( $_POST['tmc_board_nonce'], 'tmc_board_meta' ) ) return;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	foreach ( [ 'tmc_board_role','tmc_board_email','tmc_board_phone','tmc_board_type','tmc_board_order' ] as $field ) {
		if ( isset( $_POST[$field] ) ) {
			update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ) );
		}
	}
}
add_action( 'save_post_tmc_board', 'tmc_save_board_meta' );

/* =========================================================
   12. LÅNG INFORMATIONSSIDA — Sektionsfält
   ========================================================= */
function tmc_longpage_meta_box() {
	add_meta_box(
		'tmc_longpage_sections',
		__( 'Informationssektioner (ankar-sektioner)', 'tandstiftet' ),
		'tmc_longpage_meta_cb',
		'page',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'tmc_longpage_meta_box' );

/* ── Prova På (Barn & Ungdom) — Utrustningsikoner ── */
function tmc_provapa_meta_box() {
	add_meta_box(
		'tmc_provapa_gear',
		__( 'Prova På — Utrustningsikoner', 'tandstiftet' ),
		'tmc_provapa_gear_cb',
		'page',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'tmc_provapa_meta_box' );

function tmc_provapa_gear_cb( $post ) {
	$template = get_post_meta( $post->ID, '_wp_page_template', true );
	if ( $template !== 'templates/page-prova-pa.php' ) {
		echo '<p style="color:#aaa">' . esc_html__( 'Välj mallen "Prova På (Barn & Ungdom)" under Sidattribut för att aktivera ikonsfälten. (Sliding-sidan använder en förenklad punktlista utan bildfält.)', 'tandstiftet' ) . '</p>';
		return;
	}
	wp_nonce_field( 'tmc_provapa_gear', 'tmc_provapa_gear_nonce' );

	$defaults = [
		1 => 'Hjälm',
		2 => 'Cross-jacka & byxor',
		3 => 'Ryggskydd',
		4 => 'Nackskydd',
		5 => 'Armbågsskydd',
		6 => 'Stövlar',
		7 => 'Handskar',
		8 => 'Motorcykel',
	];

	$count = (int) get_post_meta( $post->ID, 'tmc_gear_count', true ) ?: 8;
	?>
	<table class="form-table">
		<tr>
			<th><label><?php esc_html_e( 'Antal utrustningskort', 'tandstiftet' ); ?></label></th>
			<td><input type="number" name="tmc_gear_count" value="<?php echo esc_attr( $count ); ?>" min="1" max="12" style="width:80px"></td>
		</tr>
	</table>
	<hr>
	<?php for ( $i = 1; $i <= $count; $i++ ) :
		$icon = get_post_meta( $post->ID, "tmc_gear_{$i}_icon", true );
		$name = get_post_meta( $post->ID, "tmc_gear_{$i}_name", true );
		$desc = get_post_meta( $post->ID, "tmc_gear_{$i}_desc", true );
	?>
	<div style="margin-bottom:16px;padding:12px;background:#f9f9f9;border:1px solid #ddd">
		<strong><?php echo esc_html( $defaults[$i] ?? "Kort $i" ); ?></strong>
		<table class="form-table" style="margin-top:8px">
			<tr>
				<th style="width:120px"><label><?php esc_html_e( 'Ikon URL', 'tandstiftet' ); ?></label></th>
				<td>
					<input type="url" name="tmc_gear_<?php echo $i; ?>_icon" value="<?php echo esc_attr( $icon ); ?>" style="width:100%;max-width:500px" placeholder="https://...">
					<?php if ( $icon ) : ?>
						<br><img src="<?php echo esc_url( $icon ); ?>" style="width:60px;height:60px;object-fit:contain;margin-top:4px;background:#132033;padding:4px">
					<?php endif; ?>
					<button type="button" class="button" onclick="
						var frame = wp.media({ title: 'Välj ikon', button: { text: 'Välj' }, multiple: false });
						frame.on('select', function() {
							var att = frame.state().get('selection').first().toJSON();
							document.querySelector('[name=tmc_gear_<?php echo $i; ?>_icon]').value = att.url;
						});
						frame.open();
					" style="margin-left:8px"><?php esc_html_e( 'Välj bild', 'tandstiftet' ); ?></button>
				</td>
			</tr>
			<tr>
				<th><label><?php esc_html_e( 'Namn (valfritt)', 'tandstiftet' ); ?></label></th>
				<td><input type="text" name="tmc_gear_<?php echo $i; ?>_name" value="<?php echo esc_attr( $name ); ?>" style="width:300px" placeholder="<?php echo esc_attr( $defaults[$i] ?? '' ); ?>"></td>
			</tr>
			<tr>
				<th><label><?php esc_html_e( 'Beskrivning (valfritt)', 'tandstiftet' ); ?></label></th>
				<td><textarea name="tmc_gear_<?php echo $i; ?>_desc" style="width:100%;max-width:500px;height:60px"><?php echo esc_textarea( $desc ); ?></textarea></td>
			</tr>
		</table>
	</div>
	<?php endfor;
}

function tmc_provapa_gear_save( $post_id ) {
	if ( ! isset( $_POST['tmc_provapa_gear_nonce'] ) ) return;
	if ( ! wp_verify_nonce( $_POST['tmc_provapa_gear_nonce'], 'tmc_provapa_gear' ) ) return;
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['tmc_gear_count'] ) ) {
		update_post_meta( $post_id, 'tmc_gear_count', absint( $_POST['tmc_gear_count'] ) );
	}
	$count = absint( $_POST['tmc_gear_count'] ?? 8 );
	for ( $i = 1; $i <= $count; $i++ ) {
		foreach ( ['icon', 'name', 'desc'] as $field ) {
			$key = "tmc_gear_{$i}_{$field}";
			if ( isset( $_POST[$key] ) ) {
				update_post_meta( $post_id, $key, sanitize_textarea_field( $_POST[$key] ) );
			}
		}
	}
}
add_action( 'save_post', 'tmc_provapa_gear_save' );

function tmc_longpage_meta_cb( $post ) {
	// Only show on pages using this template
	$template = get_post_meta( $post->ID, '_wp_page_template', true );
	if ( $template !== 'templates/page-longform.php' ) {
		echo '<p style="color:#aaa">' . esc_html__( 'Välj mallen "Lång informationssida" under Sidattribut för att aktivera sektionsfälten.', 'tandstiftet' ) . '</p>';
		return;
	}
	wp_nonce_field( 'tmc_longpage_meta', 'tmc_longpage_nonce' );
	$count = (int) get_post_meta( $post->ID, 'tmc_section_count', true );
	$intro = get_post_meta( $post->ID, 'tmc_page_intro', true );
	?>
	<table class="form-table">
		<tr>
			<th><label for="tmc_page_intro"><?php esc_html_e( 'Introduktionstext (under sidrubriken)', 'tandstiftet' ); ?></label></th>
			<td><input type="text" id="tmc_page_intro" name="tmc_page_intro" value="<?php echo esc_attr($intro); ?>" class="large-text"></td>
		</tr>
		<tr>
			<th><label for="tmc_section_count"><?php esc_html_e( 'Antal sektioner', 'tandstiftet' ); ?></label></th>
			<td>
				<input type="number" id="tmc_section_count" name="tmc_section_count" value="<?php echo esc_attr($count); ?>" min="0" max="20" style="width:80px">
				<p class="description"><?php esc_html_e( 'Ange ett antal och spara — fälten för varje sektion visas nedan.', 'tandstiftet' ); ?></p>
			</td>
		</tr>
	</table>
	<?php if ( $count > 0 ) : ?>
	<hr>
	<table class="form-table">
	<?php for ( $i = 1; $i <= $count; $i++ ) :
		$t = get_post_meta( $post->ID, "tmc_section_{$i}_title",   true );
		$s = get_post_meta( $post->ID, "tmc_section_{$i}_slug",    true );
		$c = get_post_meta( $post->ID, "tmc_section_{$i}_content", true );
		$m = get_post_meta( $post->ID, "tmc_section_{$i}_image",   true );
		$img_url = $m ? wp_get_attachment_image_url( $m, 'thumbnail' ) : '';
	?>
		<tr style="border-top:2px solid #d4a017">
			<th colspan="2" style="background:#1a1a18;color:#d4a017;font-family:sans-serif">
				<?php echo sprintf( esc_html__( 'Sektion %d', 'tandstiftet' ), $i ); ?>
			</th>
		</tr>
		<tr>
			<th><label><?php esc_html_e( 'Rubrik', 'tandstiftet' ); ?> *</label></th>
			<td><input type="text" name="tmc_section_<?php echo $i; ?>_title" value="<?php echo esc_attr($t); ?>" class="regular-text" placeholder="<?php esc_attr_e('t.ex. Banan','tandstiftet'); ?>"></td>
		</tr>
		<tr>
			<th><label><?php esc_html_e( 'Ankar-ID (slug)', 'tandstiftet' ); ?> *</label></th>
			<td>
				<input type="text" name="tmc_section_<?php echo $i; ?>_slug" value="<?php echo esc_attr($s); ?>" class="regular-text" placeholder="<?php esc_attr_e('t.ex. banan','tandstiftet'); ?>">
				<p class="description"><?php esc_html_e( 'Inga mellanslag, gemener, inga å/ä/ö. Används som länk: #banan', 'tandstiftet' ); ?></p>
			</td>
		</tr>
		<tr>
			<th><label><?php esc_html_e( 'Innehåll', 'tandstiftet' ); ?></label></th>
			<td><textarea name="tmc_section_<?php echo $i; ?>_content" rows="6" class="large-text"><?php echo esc_textarea($c); ?></textarea></td>
		</tr>
		<tr>
			<th><label><?php esc_html_e( 'Bannerbild (valfri)', 'tandstiftet' ); ?></label></th>
			<td>
				<input type="hidden" name="tmc_section_<?php echo $i; ?>_image" id="tmc_section_<?php echo $i; ?>_image" value="<?php echo esc_attr($m); ?>">
				<div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
					<?php if ( $img_url ) : ?>
						<img src="<?php echo esc_url($img_url); ?>" style="height:60px;width:auto;border:1px solid #444">
					<?php endif; ?>
					<button type="button" class="button tmc-img-picker" data-target="tmc_section_<?php echo $i; ?>_image">
						<?php echo $m ? esc_html__('Byt bild','tandstiftet') : esc_html__('Välj bild','tandstiftet'); ?>
					</button>
					<?php if ( $m ) : ?>
						<button type="button" class="button tmc-img-remove" data-target="tmc_section_<?php echo $i; ?>_image"><?php esc_html_e('Ta bort','tandstiftet'); ?></button>
					<?php endif; ?>
				</div>
			</td>
		</tr>
	<?php endfor; ?>
	</table>
	<?php endif; ?>
	<script>
	jQuery(function($){
		$('.tmc-img-picker').on('click', function(){
			var targetId = $(this).data('target');
			var frame = wp.media({ title: '<?php esc_html_e("Välj bild","tandstiftet"); ?>', multiple: false });
			frame.on('select', function(){
				var att = frame.state().get('selection').first().toJSON();
				$('#' + targetId).val(att.id);
				location.reload();
			});
			frame.open();
		});
		$('.tmc-img-remove').on('click', function(){
			var targetId = $(this).data('target');
			$('#' + targetId).val('');
			$(this).closest('tr').find('img').remove();
			$(this).hide();
		});
	});
	</script>
	<?php
}

function tmc_save_longpage_meta( $post_id ) {
	if ( ! isset( $_POST['tmc_longpage_nonce'] ) ) return;
	if ( ! wp_verify_nonce( $_POST['tmc_longpage_nonce'], 'tmc_longpage_meta' ) ) return;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

	if ( isset( $_POST['tmc_page_intro'] ) )
		update_post_meta( $post_id, 'tmc_page_intro', sanitize_text_field( $_POST['tmc_page_intro'] ) );

	$count = isset( $_POST['tmc_section_count'] ) ? absint( $_POST['tmc_section_count'] ) : 0;
	update_post_meta( $post_id, 'tmc_section_count', $count );

	for ( $i = 1; $i <= $count; $i++ ) {
		foreach ( [ 'title', 'slug', 'image' ] as $f ) {
			$key = "tmc_section_{$i}_{$f}";
			if ( isset( $_POST[$key] ) )
				update_post_meta( $post_id, $key, sanitize_text_field( $_POST[$key] ) );
		}
		$key = "tmc_section_{$i}_content";
		if ( isset( $_POST[$key] ) )
			update_post_meta( $post_id, $key, wp_kses_post( $_POST[$key] ) );
	}
}
add_action( 'save_post_page', 'tmc_save_longpage_meta' );
