<?php
/**
 * Template Name: Sliding (Prova På Vuxna)
 * Description: Sliding — Prova På för vuxna, mot en avgift. Flat track eller speedway beroende på lämplighet/tillgänglighet/önskemål.
 *
 * v2-layout enligt design/concepts/koncept-sliding.html. Alla texter ändras i
 * Utseende → Anpassa → Sida: Sliding (tmc_sliding_fields() i functions.php);
 * fotot i toppen är sidans Utvald bild. Delar komponenter (faktaremsa, steg,
 * frågor, formulär) med Prova På — därför klasserna "provapa-".
 *
 * @package tandstiftet
 */
get_header();

$page_id = get_the_ID();
$pp_page = get_page_by_path( 'prova-pa' );
$pp_url  = $pp_page ? get_permalink( $pp_page->ID ) : home_url( '/prova-pa/' );
$t       = 'tmc_sliding_text';
$ft_on   = tmc_flattrack_enabled();
$signup  = tmc_signup_state( 'sliding' );   // Anpassa → Sida: Sliding → Anmälan och säsong
$show    = function ( $part ) { return tmc_page_section_visible( 'sliding', $part ); };
$contact_email = get_theme_mod( 'tmc_contact_email' );
$contact_phone = get_theme_mod( 'tmc_contact_phone' );

if ( ! tmc_sliding_enabled() ) : ?>

	<?php tmc_page_header( __( 'Sliding', 'tandstiftet' ), '', 'SLIDING' ); ?>

	<main class="section section--beige sliding-paused" id="main">
		<div class="container sliding-paused__inner">
			<?php if ( $t( 'paused_title' ) ) : ?><h2 class="provapa-title"><?php echo esc_html( $t( 'paused_title' ) ); ?></h2><?php endif; ?>
			<?php if ( $t( 'paused_text' ) ) : ?><p class="provapa-read__lead"><?php echo esc_html( $t( 'paused_text' ) ); ?></p><?php endif; ?>
			<div class="sliding-paused__actions">
				<?php if ( $t( 'paused_btn' ) ) : ?><a href="<?php echo esc_url( $pp_url ); ?>" class="btn btn--primary"><?php echo esc_html( $t( 'paused_btn' ) ); ?></a><?php endif; ?>
				<?php if ( $contact_phone ) : ?><a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $contact_phone ) ); ?>" class="btn btn--outline"><?php echo esc_html( $contact_phone ); ?></a><?php endif; ?>
				<?php if ( $contact_email ) : ?><a href="mailto:<?php echo esc_attr( $contact_email ); ?>" class="btn btn--outline"><?php echo esc_html( $contact_email ); ?></a><?php endif; ?>
			</div>
		</div>
	</main>

	<?php get_footer(); return; endif; ?>

<?php
$photo = has_post_thumbnail( $page_id ) ? get_the_post_thumbnail_url( $page_id, 'full' ) : '';
$torn  = $photo && get_theme_mod( 'tmc_hero_torn_edge', true );
?>
<!-- ═══ HERO ═══ -->
<section class="hero hero--page<?php echo $torn ? ' hero--torn' : ''; ?><?php echo $photo ? '' : ' hero--no-photo'; ?>">
	<?php if ( $photo ) : ?>
		<div class="hero__bg" style="background-image:url('<?php echo esc_url( $photo ); ?>')" aria-hidden="true"></div>
	<?php endif; ?>
	<div class="hero__overlay"></div>
	<div class="hero__content">
		<?php if ( $t( 'hero_eyebrow' ) ) : ?><p class="hero__kicker"><?php echo esc_html( $t( 'hero_eyebrow' ) ); ?></p><?php endif; ?>
		<?php if ( $t( 'hero_title' ) ) : ?><h1 class="hero__title"><?php echo tmc_hero_title_html( $t( 'hero_title' ) ); // escaped inside ?></h1><?php endif; ?>
		<?php if ( $t( 'hero_text' ) ) : ?><p class="hero__subtitle"><?php echo esc_html( $t( 'hero_text' ) ); ?></p><?php endif; ?>
		<div class="hero__actions">
			<?php if ( $signup['open'] && $t( 'hero_btn_1' ) ) : ?><a href="#boka" class="btn btn--primary"><?php echo esc_html( $t( 'hero_btn_1' ) ); ?></a><?php endif; ?>
			<?php if ( $t( 'hero_btn_2' ) ) : ?><a href="<?php echo $ft_on ? '#disciplin' : '#vad-hander'; ?>" class="btn btn--outline provapa-btn--light"><?php echo esc_html( $t( 'hero_btn_2' ) ); ?></a><?php endif; ?>
		</div>
	</div>
</section>

<?php if ( $show( 'facts' ) ) : ?>
<!-- ═══ FAKTAREMSA ═══ -->
<section class="provapa-facts" aria-label="<?php esc_attr_e( 'Snabbfakta', 'tandstiftet' ); ?>">
	<div class="container provapa-facts__inner">
		<?php foreach ( tmc_sliding_facts() as $fact ) : ?>
			<div class="provapa-fact">
				<span class="provapa-fact__num"><?php echo esc_html( $fact['num'] ); ?></span>
				<?php if ( $fact['label'] ) : ?><span class="provapa-fact__label"><?php echo esc_html( $fact['label'] ); ?></span><?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</section>
<?php endif; // facts ?>

<?php if ( $show( 'kids' ) && $t( 'kids_link' ) ) : ?>
<!-- ═══ BARN-HÄNVISNING ═══ -->
<div class="provapa-adult">
	<div class="container provapa-adult__inner">
		<?php if ( $t( 'kids_text' ) ) : ?><span><?php echo esc_html( $t( 'kids_text' ) ); ?></span><?php endif; ?>
		<a href="<?php echo esc_url( $pp_url ); ?>"><?php echo esc_html( $t( 'kids_link' ) ); ?></a>
	</div>
</div>
<?php endif; ?>

<?php if ( $show( 'price' ) || $show( 'groups' ) ) : ?>
<!-- ═══ PRIS + GRUPPER (beige) ═══ -->
<section class="section section--beige sliding-intro" id="pris">
	<div class="container">
		<?php if ( $show( 'price' ) ) : ?>
		<div class="sliding-price">
			<div class="sliding-price__head">
				<?php if ( $t( 'price_eyebrow' ) ) : ?><p class="label"><?php echo esc_html( $t( 'price_eyebrow' ) ); ?></p><?php endif; ?>
				<?php if ( $t( 'price_title' ) ) : ?><h2 class="provapa-title"><?php echo esc_html( $t( 'price_title' ) ); ?></h2><?php endif; ?>
			</div>
			<div class="sliding-price__boxes">
				<?php if ( $t( 'price_club' ) ) : ?>
					<div class="sliding-price__box"><span class="sliding-price__amount"><?php echo esc_html( $t( 'price_club' ) ); ?></span><span class="sliding-price__what"><?php echo esc_html( $t( 'price_club_text' ) ); ?></span></div>
				<?php endif; ?>
				<?php if ( $t( 'price_club' ) && $t( 'price_license' ) ) : ?><span class="sliding-price__plus" aria-hidden="true">+</span><?php endif; ?>
				<?php if ( $t( 'price_license' ) ) : ?>
					<div class="sliding-price__box sliding-price__box--license"><span class="sliding-price__amount"><?php echo esc_html( $t( 'price_license' ) ); ?></span><span class="sliding-price__what"><?php echo esc_html( $t( 'price_license_text' ) ); ?></span></div>
				<?php endif; ?>
			</div>
			<?php if ( $t( 'price_note' ) || ( $t( 'license_link' ) && $t( 'license_url' ) ) ) : ?>
				<p class="sliding-price__note">
					<?php echo esc_html( $t( 'price_note' ) ); ?>
					<?php if ( $t( 'license_link' ) && $t( 'license_url' ) ) : ?>
						<a class="provapa-note__link" href="<?php echo esc_url( $t( 'license_url' ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $t( 'license_link' ) ); ?></a>
					<?php endif; ?>
				</p>
			<?php endif; ?>
		</div>

		<?php endif; // price ?>

		<?php $groups = $show( 'groups' ) ? tmc_sliding_items( 'group_%d_', [ 'title', 'text' ], 3 ) : []; ?>
		<?php if ( $groups ) : ?>
		<div class="sliding-groups" id="grupper-kalas">
			<?php if ( $t( 'groups_eyebrow' ) ) : ?><p class="label"><?php echo esc_html( $t( 'groups_eyebrow' ) ); ?></p><?php endif; ?>
			<?php if ( $t( 'groups_title' ) ) : ?><h2 class="provapa-title"><?php echo esc_html( $t( 'groups_title' ) ); ?></h2><?php endif; ?>
			<?php if ( $t( 'groups_text' ) ) : ?><p class="provapa-read__lead sliding-groups__lead"><?php echo esc_html( $t( 'groups_text' ) ); ?></p><?php endif; ?>
			<div class="sliding-cards">
				<?php foreach ( $groups as $g ) : ?>
					<div class="sliding-card">
						<h3 class="sliding-card__title"><?php echo esc_html( $g['title'] ); ?></h3>
						<?php if ( $g['text'] ) : ?><p class="sliding-card__text"><?php echo esc_html( $g['text'] ); ?></p><?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php endif; ?>
	</div>
</section>

<?php endif; // price/groups ?>

<?php if ( $show( 'disc' ) || ( $ft_on && $show( 'partner' ) ) ) : ?>
<!-- ═══ DISCIPLINER ═══ -->
<section class="section sliding-disc" id="disciplin">
	<div class="container">
		<?php if ( $show( 'disc' ) ) : ?>
		<div class="sliding-disc__head">
			<?php if ( $t( 'disc_eyebrow' ) ) : ?><p class="provapa-eyebrow"><?php echo esc_html( $t( 'disc_eyebrow' ) ); ?></p><?php endif; ?>
			<?php if ( $t( 'disc_title' ) ) : ?><h2 class="provapa-title"><?php echo esc_html( $t( 'disc_title' ) ); ?></h2><?php endif; ?>
			<?php if ( $t( 'disc_text' ) ) : ?><p class="sliding-disc__text"><?php echo esc_html( $t( 'disc_text' ) ); ?></p><?php endif; ?>
		</div>
		<div class="sliding-disc__grid<?php echo $ft_on ? '' : ' sliding-disc__grid--one'; ?>">
			<?php if ( $ft_on && $t( 'ft_title' ) ) : ?>
				<div class="sliding-disc__card">
					<h3 class="sliding-disc__name"><?php echo esc_html( $t( 'ft_title' ) ); ?></h3>
					<p><?php echo esc_html( $t( 'ft_text' ) ); ?></p>
				</div>
			<?php endif; ?>
			<?php if ( $t( 'sw_title' ) ) : ?>
				<div class="sliding-disc__card">
					<h3 class="sliding-disc__name"><?php echo esc_html( $t( 'sw_title' ) ); ?></h3>
					<p><?php echo esc_html( $t( 'sw_text' ) ); ?></p>
				</div>
			<?php endif; ?>
		</div>

		<?php endif; // disc ?>

		<?php if ( $ft_on && $show( 'partner' ) && $t( 'partner_title' ) ) : ?>
		<!-- Samarbetsruta (redigerbar; töm rubriken i Anpassa för att dölja) -->
		<div class="sliding-partner">
			<div class="sliding-partner__body">
				<?php if ( $t( 'partner_label' ) ) : ?><p class="sliding-partner__label"><?php echo esc_html( $t( 'partner_label' ) ); ?></p><?php endif; ?>
				<p class="sliding-partner__title"><?php echo esc_html( $t( 'partner_title' ) ); ?></p>
				<?php if ( $t( 'partner_text' ) ) : ?><p class="sliding-partner__text"><?php echo esc_html( $t( 'partner_text' ) ); ?></p><?php endif; ?>
				<?php if ( $t( 'partner_url' ) && $t( 'partner_link' ) ) : ?>
					<a class="sliding-partner__link" href="<?php echo esc_url( $t( 'partner_url' ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $t( 'partner_link' ) ); ?></a>
				<?php endif; ?>
			</div>
			<?php if ( $t( 'partner_contact_name' ) ) : ?>
				<div class="sliding-partner__contact">
					<span class="sliding-partner__name"><?php echo esc_html( $t( 'partner_contact_name' ) ); ?></span>
					<?php if ( $t( 'partner_contact_role' ) ) : ?><span class="sliding-partner__role"><?php echo esc_html( $t( 'partner_contact_role' ) ); ?></span><?php endif; ?>
					<?php if ( $t( 'partner_contact_phone' ) ) : ?><a class="sliding-partner__phone" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $t( 'partner_contact_phone' ) ) ); ?>"><?php echo esc_html( $t( 'partner_contact_phone' ) ); ?></a><?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
</section>
<?php endif; // disc/partner ?>

<?php $steps = $show( 'steps' ) ? tmc_sliding_items( 'step_%d_', [ 'title', 'text' ], 4 ) : []; if ( $steps ) : ?>
<!-- ═══ SÅ GÅR DAGEN TILL (mörk) ═══ -->
<section class="section provapa-steps sliding-steps" id="vad-hander">
	<div class="container">
		<div class="provapa-steps__head">
			<?php if ( $t( 'steps_eyebrow' ) ) : ?><p class="provapa-eyebrow"><?php echo esc_html( $t( 'steps_eyebrow' ) ); ?></p><?php endif; ?>
			<?php if ( $t( 'steps_title' ) ) : ?><h2 class="provapa-title"><?php echo esc_html( $t( 'steps_title' ) ); ?></h2><?php endif; ?>
		</div>
		<ol class="provapa-steps__grid">
			<?php foreach ( $steps as $step ) : ?>
				<li class="provapa-step">
					<h3 class="provapa-step__title"><?php echo esc_html( $step['title'] ); ?></h3>
					<?php if ( $step['text'] ) : ?><p class="provapa-step__text"><?php echo esc_html( $step['text'] ); ?></p><?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
<?php endif; ?>

<?php $faqs = $show( 'faq' ) ? tmc_sliding_items( 'faq_%d_', [ 'q', 'a' ], 6 ) : []; if ( $faqs ) : ?>
<!-- ═══ BRA ATT VETA (beige) ═══ -->
<section class="section section--beige provapa-faq" id="bra-att-veta">
	<div class="container provapa-faq__inner">
		<?php if ( $t( 'faq_eyebrow' ) ) : ?><p class="label"><?php echo esc_html( $t( 'faq_eyebrow' ) ); ?></p><?php endif; ?>
		<?php if ( $t( 'faq_title' ) ) : ?><h2 class="provapa-title"><?php echo esc_html( $t( 'faq_title' ) ); ?></h2><?php endif; ?>
		<div class="provapa-faq__list">
			<?php foreach ( $faqs as $faq ) : ?>
				<details class="provapa-faq__item">
					<summary class="provapa-faq__q"><?php echo esc_html( $faq['q'] ); ?></summary>
					<div class="provapa-faq__a">
						<?php foreach ( tmc_provapa_lines( $faq['a'] ) as $para ) : ?><p><?php echo esc_html( $para ); ?></p><?php endforeach; ?>
					</div>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- ═══ BOKA (beige, kalender + formulär) ═══ -->
<section class="section provapa-book sliding-book" id="boka">
	<div class="container provapa-book__grid sliding-book__grid">
		<div class="provapa-book__text">
			<?php if ( ! $signup['open'] && $_SERVER['REQUEST_METHOD'] !== 'POST' ) : ?>
				<?php if ( $t( 'closed_book_title' ) ) : ?><h2 class="provapa-title"><?php echo esc_html( $t( 'closed_book_title' ) ); ?></h2><?php endif; ?>
				<p class="provapa-read__lead"><?php echo nl2br( esc_html( $signup['message'] ) ); ?></p>
			<?php else : ?>
			<?php if ( $t( 'book_eyebrow' ) ) : ?><p class="label"><?php echo esc_html( $t( 'book_eyebrow' ) ); ?></p><?php endif; ?>
			<?php if ( $t( 'book_title' ) ) : ?><h2 class="provapa-title"><?php echo esc_html( $t( 'book_title' ) ); ?></h2><?php endif; ?>
			<?php if ( $t( 'book_text' ) ) : ?><p class="provapa-read__lead"><?php echo esc_html( $t( 'book_text' ) ); ?></p><?php endif; ?>
			<?php $needs = tmc_provapa_lines( $t( 'book_needs' ) ); if ( $needs ) : ?>
				<div class="provapa-book__needs">
					<?php if ( $t( 'book_needs_title' ) ) : ?><p class="provapa-book__needs-title"><?php echo esc_html( $t( 'book_needs_title' ) ); ?></p><?php endif; ?>
					<ul><?php foreach ( $needs as $need ) : ?><li><?php echo esc_html( $need ); ?></li><?php endforeach; ?></ul>
				</div>
			<?php endif; ?>
			<?php endif; // signup open ?>
		</div>
		<div class="provapa-book__form">
			<?php
			// Handle form
			$success = false;
			$mail_error = false;
			$date_full_error = false;
			$date_invalid_error = false;
			$within_24h_error = false;
			$spam_error = false;

			if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pp_nonce']) && wp_verify_nonce($_POST['pp_nonce'], 'pp_booking') && ! tmc_antispam_is_bot() ) {
				$name       = sanitize_text_field($_POST['pp_name'] ?? '');
				$email      = sanitize_email($_POST['pp_email'] ?? '');
				$phone      = sanitize_text_field($_POST['pp_phone'] ?? '');
				$age        = sanitize_text_field($_POST['pp_age'] ?? '');
				$shoe       = sanitize_text_field($_POST['pp_shoe'] ?? '');
				$height     = sanitize_text_field($_POST['pp_height'] ?? '');
				$discipline = sanitize_text_field($_POST['pp_discipline'] ?? 'speedway');
				if ( $discipline !== 'flattrack' || ! $ft_on ) $discipline = 'speedway';
				$discipline_label = $discipline === 'flattrack' ? 'Flat track' : 'Speedway';
				$date       = sanitize_text_field($_POST['pp_date'] ?? '');
				$group_size = max( 1, (int) ($_POST['pp_group_size'] ?? 1) );
				$group_type = sanitize_text_field($_POST['pp_group_type'] ?? '');
				$msg        = sanitize_textarea_field($_POST['pp_message'] ?? '');

				// Validate date format (Y-m-d) and that it isn't in the past
				$date_obj = DateTime::createFromFormat('Y-m-d', $date);
				$is_valid_date = $date_obj && $date_obj->format('Y-m-d') === $date && $date_obj >= new DateTime('today');

				if ( ! tmc_antispam_valid_age( $age, 1, 100 ) ) {
					$spam_error = true;
				} elseif ( tmc_antispam_text_is_spammy( $msg ) || tmc_antispam_text_is_spammy( $name, 0 ) ) {
					$spam_error = true;
				} elseif ( ! $is_valid_date ) {
					$date_invalid_error = true;
				} elseif ( ! tmc_signup_open_on( 'sliding', $date ) ) {
					// Booking closed, or the date is outside the season (Anpassa → Anmälan och säsong).
					$date_full_error = true;
				} elseif ( tmc_sliding_is_within_24h( $date ) ) {
					// Too last-minute for online booking — the trainer needs more lead time.
					$within_24h_error = true;
				} elseif ( tmc_sliding_get_remaining( $date, $discipline ) < $group_size ) {
					// Re-checked server-side — protects against double-booking even if the
					// visitor's calendar view was stale or the JS was bypassed.
					$date_full_error = true;
				} else {
					// Save the booking as its own post so it counts against capacity
					// and shows up in wp-admin under "Sliding-bokningar".
					$booking_id = wp_insert_post( [
						'post_type'   => 'tmc_sliding_booking',
						'post_title'  => sprintf( '%s — %s (%s)', $date, $name ?: 'Namnlös', $discipline_label ),
						'post_status' => 'publish',
					] );

					if ( $booking_id && ! is_wp_error( $booking_id ) ) {
						update_post_meta( $booking_id, 'tmc_booking_date', $date );
						update_post_meta( $booking_id, 'tmc_booking_name', $name );
						update_post_meta( $booking_id, 'tmc_booking_email', $email );
						update_post_meta( $booking_id, 'tmc_booking_phone', $phone );
						update_post_meta( $booking_id, 'tmc_booking_age', $age );
						update_post_meta( $booking_id, 'tmc_booking_shoe', $shoe );
						update_post_meta( $booking_id, 'tmc_booking_height', $height );
						update_post_meta( $booking_id, 'tmc_booking_discipline', $discipline );
						update_post_meta( $booking_id, 'tmc_booking_group_type', $group_type );
						update_post_meta( $booking_id, 'tmc_booking_group_size', $group_size );
						update_post_meta( $booking_id, 'tmc_booking_message', $msg );
						update_post_meta( $booking_id, 'tmc_booking_status', 'confirmed' );
					}

					$body = "SLIDING-BOKNING (Vuxna, 800 kr/person + 225 kr Svemo-licens)\n\n";
					$body .= "Datum: $date\n";
					$body .= "Disciplin: $discipline_label\n";
					$body .= "Namn: $name\nE-post: $email\nTelefon: $phone\n";
					$body .= "Ålder: $age\nSkostorlek: $shoe\nLängd: $height\n";
					$body .= "Typ av bokning: $group_type\nAntal personer: $group_size\n";
					$body .= "\nÖvrigt:\n$msg";
					$body .= "\n\n(Se även Sliding-bokningar i wp-admin för fullständig lista.)";

					$mail_sent = wp_mail(
						get_option('admin_email'),
						sprintf('[%s] Sliding-bokning %s (%s): %s', get_bloginfo('name'), $date, $discipline_label, $name),
						$body,
						["Reply-To: $email"]
					);

					if ( $mail_sent ) {
						$success = true;
					} else {
						error_log('Tändstiftet: wp_mail() misslyckades för Sliding-bokning, avsändare: ' . $email);
						$mail_error = true;
					}
				}
			}
			?>
			<?php if ( $success ) : ?>
				<div class="pp-form-success" role="status">
					<p class="provapa-book__icon" aria-hidden="true">✓</p>
					<h3><?php echo esc_html( $t( 'book_success_title' ) ); ?></h3>
					<p><?php echo esc_html( str_replace( '{datum}', $date, $t( 'book_success_text' ) ) ); ?></p>
				</div>
			<?php elseif ( ! $signup['open'] && $_SERVER['REQUEST_METHOD'] !== 'POST' ) : ?>
				<?php /* Booking closed / off-season: the message stands in the left column; only the contact details below. */ ?>
			<?php elseif ( $mail_error ) : ?>
				<div class="pp-form-error" role="alert">
					<h3><?php esc_html_e( 'Något gick fel', 'tandstiftet' ); ?></h3>
					<p><?php esc_html_e( 'Bokningen kunde inte skickas just nu. Testa gärna igen om en stund, eller kontakta oss direkt nedan.', 'tandstiftet' ); ?></p>
				</div>
			<?php else : ?>

				<?php if ( $spam_error ) : ?>
					<div class="pp-form-error" role="alert">
						<h3><?php esc_html_e( 'Kunde inte tolka uppgifterna', 'tandstiftet' ); ?></h3>
						<p><?php esc_html_e( 'Kontrollera att åldern är ett rimligt tal och att meddelandet inte innehåller länkar, och skicka igen.', 'tandstiftet' ); ?></p>
					</div>
				<?php elseif ( $date_full_error ) : ?>
					<div class="pp-form-error" role="alert">
						<h3><?php esc_html_e( 'Det datumet går inte att boka', 'tandstiftet' ); ?></h3>
						<p><?php esc_html_e( 'Antingen blev det precis fullbokat, eller så erbjuder vi inte Sliding den dagen. Välj ett annat datum i kalendern och skicka igen.', 'tandstiftet' ); ?></p>
					</div>
				<?php elseif ( $date_invalid_error ) : ?>
					<div class="pp-form-error" role="alert">
						<h3><?php esc_html_e( 'Välj ett datum', 'tandstiftet' ); ?></h3>
						<p><?php esc_html_e( 'Klicka på ett ledigt datum i kalendern innan du skickar formuläret.', 'tandstiftet' ); ?></p>
					</div>
				<?php elseif ( $within_24h_error ) : ?>
					<div class="pp-form-error" role="alert">
						<h3><?php esc_html_e( 'För sent för bokning online', 'tandstiftet' ); ?></h3>
						<p><?php esc_html_e( 'Det här datumet ligger mindre än 24 timmar bort. Hör av er direkt via telefon eller mejl nedan så löser vi det tillsammans.', 'tandstiftet' ); ?></p>
					</div>
				<?php endif; ?>

				<?php
				$weekday_labels = [
					1 => 'Måndagar', 2 => 'Tisdagar', 3 => 'Onsdagar', 4 => 'Torsdagar',
					5 => 'Fredagar', 6 => 'Lördagar', 7 => 'Söndagar',
				];
				$days_map = [1=>'mon',2=>'tue',3=>'wed',4=>'thu',5=>'fri',6=>'sat',7=>'sun'];
				$speedway_summary  = tmc_sliding_schedule_summary( $weekday_labels, $days_map, '_speedway' );
				$flattrack_summary = $ft_on ? tmc_sliding_schedule_summary( $weekday_labels, $days_map, '_flattrack' ) : [];
				$is_ft = isset( $discipline ) && $discipline === 'flattrack';
				?>
				<?php if ( $ft_on ) : ?>
					<div class="pp-discipline-tabs" id="pp-discipline-tabs" role="group" aria-label="<?php esc_attr_e( 'Välj disciplin', 'tandstiftet' ); ?>">
						<button type="button" class="pp-discipline-tab<?php echo $is_ft ? '' : ' is-active'; ?>" data-discipline="speedway" aria-pressed="<?php echo $is_ft ? 'false' : 'true'; ?>"><?php esc_html_e( 'Speedway', 'tandstiftet' ); ?></button>
						<button type="button" class="pp-discipline-tab<?php echo $is_ft ? ' is-active' : ''; ?>" data-discipline="flattrack" aria-pressed="<?php echo $is_ft ? 'true' : 'false'; ?>"><?php esc_html_e( 'Flat track', 'tandstiftet' ); ?></button>
					</div>
					<?php if ( $speedway_summary ) : ?>
						<p class="pp-schedule-summary" data-discipline="speedway"<?php echo $is_ft ? ' hidden' : ''; ?>><?php echo esc_html__( 'Speedway erbjuds normalt: ', 'tandstiftet' ) . esc_html( implode( ', ', $speedway_summary ) ); ?></p>
					<?php endif; ?>
					<?php if ( $flattrack_summary ) : ?>
						<p class="pp-schedule-summary" data-discipline="flattrack"<?php echo $is_ft ? '' : ' hidden'; ?>><?php echo esc_html__( 'Flat track erbjuds normalt: ', 'tandstiftet' ) . esc_html( implode( ', ', $flattrack_summary ) ); ?></p>
					<?php endif; ?>
				<?php elseif ( $speedway_summary ) : ?>
					<p class="pp-schedule-summary"><?php echo esc_html__( 'Sliding erbjuds normalt: ', 'tandstiftet' ) . esc_html( implode( ', ', $speedway_summary ) ); ?></p>
				<?php endif; ?>

				<div class="pp-calendar" id="pp-calendar" data-availability="<?php echo esc_attr( wp_json_encode( tmc_sliding_get_calendar_data( 90 ) ) ); ?>" data-flattrack-enabled="<?php echo $ft_on ? '1' : '0'; ?>">
					<div class="pp-calendar__nav">
						<button type="button" class="pp-calendar__nav-btn" id="pp-cal-prev" aria-label="<?php esc_attr_e( 'Föregående månad', 'tandstiftet' ); ?>">←</button>
						<span class="pp-calendar__month-label" id="pp-cal-label" aria-live="polite"></span>
						<button type="button" class="pp-calendar__nav-btn" id="pp-cal-next" aria-label="<?php esc_attr_e( 'Nästa månad', 'tandstiftet' ); ?>">→</button>
					</div>
					<div class="pp-calendar__weekdays" aria-hidden="true">
						<span>Mån</span><span>Tis</span><span>Ons</span><span>Tor</span><span>Fre</span><span>Lör</span><span>Sön</span>
					</div>
					<div class="pp-calendar__grid" id="pp-cal-grid"></div>
					<div class="pp-calendar__legend">
						<span><i class="i-avail"></i> <?php esc_html_e( 'Ledigt', 'tandstiftet' ); ?></span>
						<span><i class="i-low"></i> <?php esc_html_e( 'Få platser kvar', 'tandstiftet' ); ?></span>
						<span><i class="i-contact"></i> <?php esc_html_e( 'Inom 24 h — ring oss', 'tandstiftet' ); ?></span>
						<span><i class="i-full"></i> <?php esc_html_e( 'Fullt eller stängt', 'tandstiftet' ); ?></span>
					</div>
					<p class="pp-calendar__selected-info" id="pp-cal-selected-info" aria-live="polite" hidden></p>
				</div>

				<form method="post" class="pp-form provapa-form" action="#boka" id="pp-sliding-form">
					<?php wp_nonce_field('pp_booking','pp_nonce'); ?>
					<?php tmc_antispam_fields(); ?>
					<input type="hidden" name="pp_date" id="pp_date_input" value="<?php echo esc_attr($date ?? ''); ?>">
					<p class="sliding-form__error" id="pp-date-missing" role="alert" hidden><?php esc_html_e( 'Välj ett ledigt datum i kalendern ovan innan du skickar bokningen.', 'tandstiftet' ); ?></p>
					<div class="pp-form-row">
						<div class="tmc-form-field">
							<label for="pp_name"><?php esc_html_e('Namn *','tandstiftet'); ?></label>
							<input type="text" id="pp_name" name="pp_name" required autocomplete="name" value="<?php echo esc_attr($name ?? ''); ?>">
						</div>
						<div class="tmc-form-field">
							<label for="pp_age"><?php esc_html_e('Ålder','tandstiftet'); ?></label>
							<input type="text" id="pp_age" name="pp_age" inputmode="numeric" value="<?php echo esc_attr($age ?? ''); ?>">
						</div>
					</div>
					<div class="pp-form-row">
						<div class="tmc-form-field">
							<label for="pp_email"><?php esc_html_e('E-post *','tandstiftet'); ?></label>
							<input type="email" id="pp_email" name="pp_email" required autocomplete="email" value="<?php echo esc_attr($email ?? ''); ?>">
						</div>
						<div class="tmc-form-field">
							<label for="pp_phone"><?php esc_html_e('Telefon','tandstiftet'); ?></label>
							<input type="tel" id="pp_phone" name="pp_phone" autocomplete="tel" value="<?php echo esc_attr($phone ?? ''); ?>">
						</div>
					</div>
					<div class="pp-form-row">
						<div class="tmc-form-field">
							<label for="pp_shoe"><?php esc_html_e('Skostorlek','tandstiftet'); ?></label>
							<input type="text" id="pp_shoe" name="pp_shoe" inputmode="numeric" placeholder="<?php esc_attr_e( 't.ex. 42', 'tandstiftet' ); ?>" value="<?php echo esc_attr($shoe ?? ''); ?>">
						</div>
						<div class="tmc-form-field">
							<label for="pp_height"><?php esc_html_e('Längd (cm)','tandstiftet'); ?></label>
							<input type="text" id="pp_height" name="pp_height" inputmode="numeric" placeholder="<?php esc_attr_e( 't.ex. 175', 'tandstiftet' ); ?>" value="<?php echo esc_attr($height ?? ''); ?>">
						</div>
					</div>
					<div class="pp-form-row">
						<div class="tmc-form-field">
							<label for="pp_group_type"><?php esc_html_e('Typ av bokning','tandstiftet'); ?></label>
							<select id="pp_group_type" name="pp_group_type">
								<?php $gt = $group_type ?? 'Enskild'; ?>
								<option value="Enskild" <?php selected($gt, 'Enskild'); ?>><?php esc_html_e('Enskild — bara jag','tandstiftet'); ?></option>
								<option value="Sällskap/vänner" <?php selected($gt, 'Sällskap/vänner'); ?>><?php esc_html_e('Sällskap / vänner','tandstiftet'); ?></option>
								<option value="Kalas/möhippa/svensexa" <?php selected($gt, 'Kalas/möhippa/svensexa'); ?>><?php esc_html_e('Kalas / möhippa / svensexa','tandstiftet'); ?></option>
								<option value="Företag/personalaktivitet" <?php selected($gt, 'Företag/personalaktivitet'); ?>><?php esc_html_e('Företag / personalaktivitet','tandstiftet'); ?></option>
							</select>
						</div>
						<div class="tmc-form-field">
							<label for="pp_group_size"><?php esc_html_e('Antal personer','tandstiftet'); ?></label>
							<input type="number" id="pp_group_size" min="1" name="pp_group_size" value="<?php echo esc_attr($group_size ?? 1); ?>">
						</div>
					</div>
					<div class="pp-form-row">
						<div class="tmc-form-field">
							<label for="pp_discipline_display"><?php esc_html_e('Disciplin','tandstiftet'); ?></label>
							<?php
							$repop_discipline = $is_ft ? 'flattrack' : ( $ft_on ? '' : 'speedway' );
							$repop_discipline_label = $repop_discipline === 'flattrack' ? 'Flat track' : ( $repop_discipline === 'speedway' ? 'Speedway' : '' );
							?>
							<input type="text" id="pp_discipline_display" readonly value="<?php echo esc_attr($repop_discipline_label); ?>">
							<input type="hidden" name="pp_discipline" id="pp_discipline_input" value="<?php echo esc_attr($repop_discipline ?: 'speedway'); ?>">
						</div>
						<div class="tmc-form-field">
							<label for="pp_date_display"><?php esc_html_e('Valt datum','tandstiftet'); ?></label>
							<?php
							$repop_date_label = '';
							if ( ! empty( $date ) ) {
								$d2 = DateTime::createFromFormat('Y-m-d', $date);
								if ( $d2 ) {
									$dagar = ['Söndag','Måndag','Tisdag','Onsdag','Torsdag','Fredag','Lördag'];
									$manader = ['','januari','februari','mars','april','maj','juni','juli','augusti','september','oktober','november','december'];
									$repop_date_label = $dagar[(int)$d2->format('w')] . ' ' . (int)$d2->format('j') . ' ' . $manader[(int)$d2->format('n')];
								}
							}
							?>
							<input type="text" id="pp_date_display" readonly value="<?php echo esc_attr($repop_date_label); ?>" placeholder="<?php esc_attr_e('Välj i kalendern','tandstiftet'); ?>">
						</div>
					</div>
					<div class="tmc-form-field">
						<label for="pp_message"><?php esc_html_e('Övrigt (frågor, tidigare erfarenhet …)','tandstiftet'); ?></label>
						<textarea id="pp_message" name="pp_message" rows="3"><?php echo esc_textarea($msg ?? ''); ?></textarea>
					</div>
					<button type="submit" class="btn btn--primary provapa-form__submit" id="pp_submit_btn"><?php esc_html_e('Skicka bokning','tandstiftet'); ?></button>
				</form>
			<?php endif; ?>

			<?php if ( $contact_email || $contact_phone ) : ?>
			<div class="provapa-book__direct">
				<?php if ( $t( 'book_direct' ) ) : ?><span><?php echo esc_html( $t( 'book_direct' ) ); ?></span><?php endif; ?>
				<?php if ( $contact_email ) : ?><a href="mailto:<?php echo esc_attr( $contact_email ); ?>"><?php echo esc_html( $contact_email ); ?></a><?php endif; ?>
				<?php if ( $contact_phone ) : ?><a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $contact_phone ) ); ?>"><?php echo esc_html( $contact_phone ); ?></a><?php endif; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<script>
(function(){
	var calEl = document.getElementById('pp-calendar');
	if (!calEl) return;
	var availabilityByDiscipline = JSON.parse(calEl.dataset.availability || '{}');
	var flattrackEnabled = calEl.dataset.flattrackEnabled === '1';

	var dateInput = document.getElementById('pp_date_input');
	var dateDisplay = document.getElementById('pp_date_display');
	var disciplineInput = document.getElementById('pp_discipline_input');
	var disciplineDisplay = document.getElementById('pp_discipline_display');
	var selectedInfo = document.getElementById('pp-cal-selected-info');
	var missing = document.getElementById('pp-date-missing');
	var grid = document.getElementById('pp-cal-grid');
	var label = document.getElementById('pp-cal-label');
	var prevBtn = document.getElementById('pp-cal-prev');
	var nextBtn = document.getElementById('pp-cal-next');

	// Carries over from a failed submission (e.g. spam/age validation error)
	// so the visitor doesn't have to re-pick their date/discipline.
	var currentDiscipline = disciplineInput.value || 'speedway';

	var monthNames = ['januari','februari','mars','april','maj','juni','juli','augusti','september','oktober','november','december'];
	var dayNames = ['Söndag','Måndag','Tisdag','Onsdag','Torsdag','Fredag','Lördag'];
	var today = new Date(); today.setHours(0,0,0,0);
	var viewYear = today.getFullYear();
	var viewMonth = today.getMonth();

	if (dateInput.value) {
		var preDate = new Date(dateInput.value + 'T00:00:00');
		if (!isNaN(preDate.getTime())) {
			viewYear = preDate.getFullYear();
			viewMonth = preDate.getMonth();
			if (dateDisplay.value) {
				selectedInfo.hidden = false;
				selectedInfo.textContent = disciplineLabel(currentDiscipline) + ' — valt: ' + dateDisplay.value;
			}
		}
	}

	var minDate = new Date(today);
	var maxDate = new Date(today); maxDate.setDate(maxDate.getDate() + 89);

	function fmt(y,m,d){
		return y + '-' + String(m+1).padStart(2,'0') + '-' + String(d).padStart(2,'0');
	}
	function disciplineLabel(key){
		return key === 'flattrack' ? 'Flat track' : 'Speedway';
	}
	function cap(s){ return s.charAt(0).toUpperCase() + s.slice(1); }

	function render(){
		var availability = availabilityByDiscipline[currentDiscipline] || {};
		grid.innerHTML = '';
		label.textContent = cap(monthNames[viewMonth]) + ' ' + viewYear;

		var firstDay = new Date(viewYear, viewMonth, 1);
		var startOffset = (firstDay.getDay() + 6) % 7; // Monday-first
		var daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();

		for (var i = 0; i < startOffset; i++) {
			var empty = document.createElement('div');
			empty.className = 'pp-calendar__day is-empty';
			grid.appendChild(empty);
		}

		for (var d = 1; d <= daysInMonth; d++) {
			var key = fmt(viewYear, viewMonth, d);
			var cellDate = new Date(viewYear, viewMonth, d);
			var info = availability.hasOwnProperty(key) ? availability[key] : null;
			var open = info ? info.open : false;
			var remaining = info ? info.remaining : 0;
			var within24h = info ? info.within24h : true;
			var slotTime = info ? info.time : '';
			var bookable = !(cellDate < minDate || cellDate > maxDate || !info || !open) && remaining > 0 && !within24h;

			// Bookable days are real buttons (keyboard + screen readers); the rest are plain cells.
			var cell = document.createElement(bookable ? 'button' : 'div');
			if (bookable) cell.type = 'button';
			cell.className = 'pp-calendar__day';

			var numEl = document.createElement('span');
			numEl.className = 'pp-calendar__day-num';
			numEl.textContent = d;
			cell.appendChild(numEl);

			var spoken = d + ' ' + monthNames[viewMonth];
			if (cellDate < minDate || cellDate > maxDate || !info || !open) {
				// Either outside the bookable window, or simply not a day this
				// discipline runs on this week — no "Fullt" tag, it just isn't a service day.
				cell.classList.add('is-disabled');
			} else if (remaining <= 0) {
				cell.classList.add('is-disabled', 'is-full');
				addTag(cell, 'Fullt');
			} else if (within24h) {
				cell.classList.add('is-contact');
				addTag(cell, 'Ring');
			} else {
				if (remaining <= 2) cell.classList.add('is-low');
				if (dateInput.value === key && disciplineInput.value === currentDiscipline) {
					cell.classList.add('is-selected');
					cell.setAttribute('aria-pressed', 'true');
				}
				cell.setAttribute('aria-label', spoken + ', ' + remaining + ' platser kvar' + (slotTime ? ', kl ' + slotTime : ''));
				cell.addEventListener('click', (function(key, remaining, slotTime, discipline){
					return function(){
						dateInput.value = key;
						disciplineInput.value = discipline;
						disciplineDisplay.value = disciplineLabel(discipline);
						var d2 = new Date(key + 'T00:00:00');
						dateDisplay.value = dayNames[d2.getDay()] + ' ' + d2.getDate() + ' ' + monthNames[d2.getMonth()] + (slotTime ? ' kl ' + slotTime : '');
						selectedInfo.hidden = false;
						selectedInfo.textContent = disciplineLabel(discipline) + ' — valt: ' + dateDisplay.value + ' — ' + remaining + ' platser kvar';
						if (missing) missing.hidden = true;
						render();
					};
				})(key, remaining, slotTime, currentDiscipline));
			}
			grid.appendChild(cell);
		}

		prevBtn.disabled = (viewYear === minDate.getFullYear() && viewMonth === minDate.getMonth());
		var maxMonthIndex = maxDate.getFullYear()*12 + maxDate.getMonth();
		var curMonthIndex = viewYear*12 + viewMonth;
		nextBtn.disabled = curMonthIndex >= maxMonthIndex;
	}
	function addTag(cell, text){
		var tag = document.createElement('span');
		tag.className = 'pp-calendar__day-tag';
		tag.textContent = text;
		cell.appendChild(tag);
	}

	prevBtn.addEventListener('click', function(){
		viewMonth--; if (viewMonth < 0) { viewMonth = 11; viewYear--; }
		render();
	});
	nextBtn.addEventListener('click', function(){
		viewMonth++; if (viewMonth > 11) { viewMonth = 0; viewYear++; }
		render();
	});

	if (flattrackEnabled) {
		var tabs = document.querySelectorAll('.pp-discipline-tab');
		tabs.forEach(function(tab){
			tab.addEventListener('click', function(){
				currentDiscipline = tab.dataset.discipline;
				tabs.forEach(function(t){
					t.classList.toggle('is-active', t === tab);
					t.setAttribute('aria-pressed', t === tab ? 'true' : 'false');
				});
				document.querySelectorAll('.pp-schedule-summary[data-discipline]').forEach(function(el){
					el.hidden = el.dataset.discipline !== currentDiscipline;
				});
				// switching discipline invalidates any previously chosen date
				dateInput.value = '';
				disciplineInput.value = currentDiscipline;
				disciplineDisplay.value = disciplineLabel(currentDiscipline);
				dateDisplay.value = '';
				selectedInfo.hidden = true;
				render();
			});
		});
	} else {
		disciplineInput.value = 'speedway';
		disciplineDisplay.value = 'Speedway';
	}

	var form = document.getElementById('pp-sliding-form');
	if (form) form.addEventListener('submit', function(e){
		if (!dateInput.value) {
			e.preventDefault();
			if (missing) { missing.hidden = false; }
			calEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
		}
	});

	render();
})();
</script>

<?php get_footer(); ?>
