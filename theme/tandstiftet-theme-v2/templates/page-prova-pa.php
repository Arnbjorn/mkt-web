<?php
/**
 * Template Name: Prova På (Barn & Ungdom)
 * Description: Prova På-sida för barn & ungdomar — gratis, på ordinarie barnträning.
 *
 * v2-layout enligt design/concepts/koncept-provapa.html med hero "D"
 * (affischrubrik + foto i ram med klistermärke). Alla texter ändras i
 * Utseende → Anpassa → Prova På — Sidan (tmc_provapa_fields() i functions.php).
 * Fotot i ramen är sidans Utvald bild.
 *
 * @package tandstiftet
 */
get_header();

$page_id      = get_the_ID();
$season_open  = tmc_provapa_season_open();
$sliding_page = get_page_by_path( 'sliding' );
$sliding_url  = $sliding_page ? get_permalink( $sliding_page->ID ) : home_url( '/sliding/' );
$t            = 'tmc_provapa_text';
$slots        = tmc_provapa_slots();
?>

<!-- ═══ HERO (D: affischrubrik + foto i ram) ═══ -->
<section class="provapa-hero">
	<div class="provapa-hero__speckle" aria-hidden="true"></div>
	<div class="container provapa-hero__inner">
		<div class="provapa-hero__text">
			<?php if ( $t( 'hero_eyebrow' ) ) : ?>
				<p class="provapa-hero__eyebrow"><?php echo esc_html( $t( 'hero_eyebrow' ) ); ?></p>
			<?php endif; ?>
			<h1 class="provapa-hero__title">
				<?php if ( $t( 'hero_title_1' ) ) : ?>
					<span class="provapa-hero__line provapa-hero__line--gold"><?php echo esc_html( $t( 'hero_title_1' ) ); ?></span>
				<?php endif; ?>
				<?php if ( $t( 'hero_title_2' ) ) : ?>
					<span class="provapa-hero__line"><?php echo esc_html( $t( 'hero_title_2' ) ); ?><svg class="provapa-hero__stroke" viewBox="0 0 400 40" preserveAspectRatio="none" aria-hidden="true" focusable="false"><defs><filter id="provapa-rough" x="-5%" y="-40%" width="110%" height="180%"><feTurbulence type="fractalNoise" baseFrequency="0.035 0.25" numOctaves="2" seed="4" result="n"/><feDisplacementMap in="SourceGraphic" in2="n" scale="9"/></filter></defs><path filter="url(#provapa-rough)" d="M4 24 C70 10 150 30 230 17 S360 8 396 21 L392 31 C330 37 262 26 192 34 S62 40 6 33 Z"/></svg></span>
				<?php endif; ?>
			</h1>
			<?php if ( $t( 'hero_text' ) ) : ?>
				<p class="provapa-hero__sub"><?php echo esc_html( $t( 'hero_text' ) ); ?></p>
			<?php endif; ?>
			<div class="provapa-hero__actions">
				<?php if ( $t( 'hero_btn_1' ) ) : ?>
					<a href="#traningstider" class="btn btn--primary"><?php echo esc_html( $t( 'hero_btn_1' ) ); ?></a>
				<?php endif; ?>
				<?php if ( $season_open && $t( 'hero_btn_2' ) ) : ?>
					<a href="#boka" class="btn btn--outline provapa-btn--light"><?php echo esc_html( $t( 'hero_btn_2' ) ); ?></a>
				<?php endif; ?>
			</div>
		</div>

		<?php
		$has_sticker = $t( 'hero_sticker_big' ) || $t( 'hero_sticker_small' );
		ob_start();
		if ( $has_sticker ) : ?>
			<div class="provapa-sticker" aria-hidden="true"><div>
				<?php if ( $t( 'hero_sticker_big' ) ) : ?><span class="provapa-sticker__big"><?php echo esc_html( $t( 'hero_sticker_big' ) ); ?></span><?php endif; ?>
				<?php if ( $t( 'hero_sticker_small' ) ) : ?><span class="provapa-sticker__small"><?php echo esc_html( $t( 'hero_sticker_small' ) ); ?></span><?php endif; ?>
			</div></div>
		<?php endif;
		$sticker = ob_get_clean();
		?>
		<?php if ( has_post_thumbnail( $page_id ) ) : ?>
			<div class="provapa-hero__visual">
				<figure class="provapa-polaroid">
					<span class="provapa-polaroid__tape" aria-hidden="true"></span>
					<?php echo get_the_post_thumbnail( $page_id, 'large', [ 'class' => 'provapa-polaroid__img' ] ); ?>
					<?php if ( $t( 'hero_photo_caption' ) ) : ?>
						<figcaption class="provapa-polaroid__cap"><?php echo esc_html( $t( 'hero_photo_caption' ) ); ?></figcaption>
					<?php endif; ?>
					<?php echo $sticker; // phpcs:ignore — escaped above ?>
				</figure>
			</div>
		<?php elseif ( $has_sticker ) : ?>
			<div class="provapa-hero__visual provapa-hero__visual--no-photo">
				<span class="provapa-hero__ring" aria-hidden="true"></span>
				<?php echo $sticker; // phpcs:ignore — escaped above ?>
			</div>
		<?php endif; ?>
		<?php if ( $has_sticker ) : /* the sticker is decorative — say it once for screen readers */ ?>
			<p class="screen-reader-text"><?php echo esc_html( trim( $t( 'hero_sticker_big' ) . ' ' . $t( 'hero_sticker_small' ) ) ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php if ( ! $season_open ) : ?>
<!-- ═══ SÄSONG STÄNGD ═══ -->
<div class="provapa-closed" role="status">
	<div class="container provapa-closed__inner">
		<?php if ( $t( 'closed_title' ) ) : ?>
			<p class="provapa-closed__title"><?php echo esc_html( $t( 'closed_title' ) ); ?></p>
		<?php endif; ?>
		<p class="provapa-closed__text"><?php echo nl2br( esc_html( tmc_provapa_season_message() ) ); ?></p>
	</div>
</div>
<?php endif; ?>

<?php $facts = tmc_provapa_items( 'fact_%d_', [ 'num', 'label' ], 6 ); if ( $facts ) : ?>
<!-- ═══ FAKTAREMSA ═══ -->
<section class="provapa-facts" aria-label="<?php esc_attr_e( 'Snabbfakta', 'tandstiftet' ); ?>">
	<div class="container provapa-facts__inner">
		<?php foreach ( $facts as $fact ) : ?>
			<div class="provapa-fact">
				<span class="provapa-fact__num"><?php echo esc_html( $fact['num'] ); ?></span>
				<?php if ( $fact['label'] ) : ?><span class="provapa-fact__label"><?php echo esc_html( $fact['label'] ); ?></span><?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</section>
<?php endif; ?>

<?php if ( tmc_sliding_enabled() && $t( 'adult_link' ) ) : ?>
<!-- ═══ VUXEN-HÄNVISNING ═══ -->
<div class="provapa-adult">
	<div class="container provapa-adult__inner">
		<?php if ( $t( 'adult_text' ) ) : ?><span><?php echo esc_html( $t( 'adult_text' ) ); ?></span><?php endif; ?>
		<a href="<?php echo esc_url( $sliding_url ); ?>"><?php echo esc_html( $t( 'adult_link' ) ); ?></a>
	</div>
</div>
<?php endif; ?>

<!-- ═══ KOM TILL EN VANLIG TRÄNING (beige) ═══ -->
<section class="section section--beige provapa-read" id="traningstider">
	<div class="container provapa-read__grid">
		<div class="provapa-read__text">
			<?php if ( $t( 'times_eyebrow' ) ) : ?><p class="label"><?php echo esc_html( $t( 'times_eyebrow' ) ); ?></p><?php endif; ?>
			<?php if ( $t( 'times_title' ) ) : ?><h2 class="provapa-title"><?php echo esc_html( $t( 'times_title' ) ); ?></h2><?php endif; ?>
			<?php if ( $season_open && $t( 'times_text' ) ) : ?>
				<p class="provapa-read__lead"><?php echo esc_html( $t( 'times_text' ) ); ?></p>
			<?php elseif ( ! $season_open && $t( 'closed_times_text' ) ) : ?>
				<p class="provapa-read__lead"><?php echo esc_html( $t( 'closed_times_text' ) ); ?></p>
			<?php endif; ?>

			<?php if ( $t( 'gear_text' ) ) : ?>
				<div class="provapa-note provapa-note--orange">
					<?php if ( $t( 'gear_title' ) ) : ?><strong><?php echo esc_html( $t( 'gear_title' ) ); ?></strong> <?php endif; ?>
					<?php echo esc_html( $t( 'gear_text' ) ); ?>
				</div>
			<?php endif; ?>
			<?php if ( $t( 'license_text' ) ) : ?>
				<div class="provapa-note">
					<?php if ( $t( 'license_title' ) ) : ?><strong><?php echo esc_html( $t( 'license_title' ) ); ?></strong> <?php endif; ?>
					<?php echo esc_html( $t( 'license_text' ) ); ?>
					<?php if ( $t( 'license_link' ) && $t( 'license_url' ) ) : ?>
						<a class="provapa-note__link" href="<?php echo esc_url( $t( 'license_url' ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $t( 'license_link' ) ); ?></a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $slots ) : ?>
		<div class="provapa-schedule">
			<?php foreach ( $slots as $card ) : ?>
				<div class="provapa-schedule__card">
					<span class="provapa-schedule__day"><?php echo esc_html( $card['day'] ); ?></span>
					<?php if ( $card['time'] ) : ?><span class="provapa-schedule__time"><?php echo esc_html( $card['time'] ); ?></span><?php endif; ?>
				</div>
			<?php endforeach; ?>
			<?php if ( $t( 'slot_note' ) ) : ?><p class="provapa-schedule__note"><?php echo esc_html( $t( 'slot_note' ) ); ?></p><?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
</section>

<?php $steps = tmc_provapa_items( 'step_%d_', [ 'title', 'text' ], 4 ); if ( $steps ) : ?>
<!-- ═══ FYRA ENKLA STEG (mörk) ═══ -->
<section class="section provapa-steps" id="vad-hander">
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

<?php $faqs = tmc_provapa_items( 'faq_%d_', [ 'q', 'a' ], 6 ); if ( $faqs ) : ?>
<!-- ═══ BRA ATT VETA (beige, fällbara frågor) ═══ -->
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

<!-- ═══ HÖR AV DIG INNAN (beige, formulär) ═══ -->
<section class="section provapa-book" id="boka">
	<div class="container provapa-book__grid">
		<div class="provapa-book__text">
			<?php if ( $season_open ) : ?>
				<?php if ( $t( 'book_eyebrow' ) ) : ?><p class="label"><?php echo esc_html( $t( 'book_eyebrow' ) ); ?></p><?php endif; ?>
				<?php if ( $t( 'book_title' ) ) : ?><h2 class="provapa-title"><?php echo esc_html( $t( 'book_title' ) ); ?></h2><?php endif; ?>
				<?php if ( $t( 'book_text' ) ) : ?><p class="provapa-read__lead"><?php echo esc_html( $t( 'book_text' ) ); ?></p><?php endif; ?>
				<?php $needs = tmc_provapa_lines( $t( 'book_needs' ) ); if ( $needs ) : ?>
					<div class="provapa-book__needs">
						<?php if ( $t( 'book_needs_title' ) ) : ?><p class="provapa-book__needs-title"><?php echo esc_html( $t( 'book_needs_title' ) ); ?></p><?php endif; ?>
						<ul><?php foreach ( $needs as $need ) : ?><li><?php echo esc_html( $need ); ?></li><?php endforeach; ?></ul>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<?php if ( $t( 'closed_title' ) ) : ?><p class="label"><?php echo esc_html( $t( 'closed_title' ) ); ?></p><?php endif; ?>
				<?php if ( $t( 'closed_book_title' ) ) : ?><h2 class="provapa-title"><?php echo esc_html( $t( 'closed_book_title' ) ); ?></h2><?php endif; ?>
				<p class="provapa-read__lead"><?php echo nl2br( esc_html( tmc_provapa_season_message() ) ); ?></p>
			<?php endif; ?>
		</div>

		<div class="provapa-book__form">
			<?php if ( $season_open ) : ?>
			<?php
			// Handle form
			$success    = false;
			$mail_error = false;
			$spam_error = false;
			if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['pp_nonce'] ) && wp_verify_nonce( $_POST['pp_nonce'], 'pp_booking' ) && ! tmc_antispam_is_bot() ) {
				$name   = sanitize_text_field( $_POST['pp_name'] ?? '' );
				$email  = sanitize_email( $_POST['pp_email'] ?? '' );
				$phone  = sanitize_text_field( $_POST['pp_phone'] ?? '' );
				$age    = sanitize_text_field( $_POST['pp_age'] ?? '' );
				$shoe   = sanitize_text_field( $_POST['pp_shoe'] ?? '' );
				$height = sanitize_text_field( $_POST['pp_height'] ?? '' );
				$slot   = sanitize_text_field( $_POST['pp_slot'] ?? '' );
				$again  = sanitize_text_field( $_POST['pp_again'] ?? '' );
				$msg    = sanitize_textarea_field( $_POST['pp_message'] ?? '' );

				if ( ! tmc_antispam_valid_age( $age, 1, 18 ) || tmc_antispam_text_is_spammy( $msg ) || tmc_antispam_text_is_spammy( $name, 0 ) ) {
					$spam_error = true;
				} else {
					$body  = "PROVA PÅ — FÖRHANDSKONTAKT (Barn & Ungdom, gratis)\n\n";
					$body .= "Namn: $name\nE-post: $email\nTelefon: $phone\n";
					$body .= "Ålder: $age\nSkostorlek: $shoe\nLängd: $height\n";
					$body .= "Planerar komma till: $slot\n";
					$body .= "Har provat på tidigare: $again\n\nÖvrigt:\n$msg";

					$mail_sent = wp_mail(
						get_option( 'admin_email' ),
						sprintf( '[%s] Prova På — hör av sig innan (Barn & Ungdom): %s', get_bloginfo( 'name' ), $name ),
						$body,
						[ "Reply-To: $email" ]
					);

					if ( $mail_sent ) {
						$success = true;
					} else {
						// Common cause: host blocks PHP mail() / no SMTP configured in WordPress.
						error_log( 'Tändstiftet: wp_mail() misslyckades för Prova På-formulär (Barn & Ungdom), avsändare: ' . $email );
						$mail_error = true;
					}
				}
			}
			?>
			<?php if ( $success ) : ?>
				<div class="pp-form-success" role="status">
					<p class="provapa-book__icon" aria-hidden="true">✓</p>
					<h3><?php echo esc_html( $t( 'book_success_title' ) ); ?></h3>
					<p><?php echo esc_html( $t( 'book_success_text' ) ); ?></p>
				</div>
			<?php else : ?>
				<?php if ( $spam_error ) : ?>
					<div class="pp-form-error" role="alert">
						<h3><?php esc_html_e( 'Kunde inte tolka uppgifterna', 'tandstiftet' ); ?></h3>
						<p><?php esc_html_e( 'Kontrollera att åldern är ett rimligt tal och att meddelandet inte innehåller länkar, och skicka igen.', 'tandstiftet' ); ?></p>
					</div>
				<?php elseif ( $mail_error ) : ?>
					<div class="pp-form-error" role="alert">
						<h3><?php esc_html_e( 'Något gick fel', 'tandstiftet' ); ?></h3>
						<p><?php esc_html_e( 'Meddelandet kunde inte skickas just nu. Testa gärna igen om en stund, eller kontakta oss direkt nedan.', 'tandstiftet' ); ?></p>
					</div>
				<?php endif; ?>
				<form method="post" class="pp-form provapa-form" action="#boka">
					<?php wp_nonce_field( 'pp_booking', 'pp_nonce' ); ?>
					<?php tmc_antispam_fields(); ?>
					<div class="pp-form-row">
						<div class="tmc-form-field">
							<label for="pp_name"><?php esc_html_e( 'Barnets namn *', 'tandstiftet' ); ?></label>
							<input type="text" id="pp_name" name="pp_name" required autocomplete="name" value="<?php echo esc_attr( $name ?? '' ); ?>">
						</div>
						<div class="tmc-form-field">
							<label for="pp_age"><?php esc_html_e( 'Ålder *', 'tandstiftet' ); ?></label>
							<input type="text" id="pp_age" name="pp_age" required inputmode="numeric" placeholder="<?php esc_attr_e( 't.ex. 9', 'tandstiftet' ); ?>" value="<?php echo esc_attr( $age ?? '' ); ?>">
						</div>
					</div>
					<div class="pp-form-row">
						<div class="tmc-form-field">
							<label for="pp_email"><?php esc_html_e( 'Vårdnadshavarens e-post *', 'tandstiftet' ); ?></label>
							<input type="email" id="pp_email" name="pp_email" required autocomplete="email" value="<?php echo esc_attr( $email ?? '' ); ?>">
						</div>
						<div class="tmc-form-field">
							<label for="pp_phone"><?php esc_html_e( 'Telefon', 'tandstiftet' ); ?></label>
							<input type="tel" id="pp_phone" name="pp_phone" autocomplete="tel" value="<?php echo esc_attr( $phone ?? '' ); ?>">
						</div>
					</div>
					<div class="pp-form-row">
						<div class="tmc-form-field">
							<label for="pp_shoe"><?php esc_html_e( 'Skostorlek', 'tandstiftet' ); ?></label>
							<input type="text" id="pp_shoe" name="pp_shoe" inputmode="numeric" placeholder="<?php esc_attr_e( 't.ex. 36', 'tandstiftet' ); ?>" value="<?php echo esc_attr( $shoe ?? '' ); ?>">
						</div>
						<div class="tmc-form-field">
							<label for="pp_height"><?php esc_html_e( 'Längd (cm)', 'tandstiftet' ); ?></label>
							<input type="text" id="pp_height" name="pp_height" inputmode="numeric" placeholder="<?php esc_attr_e( 't.ex. 140', 'tandstiftet' ); ?>" value="<?php echo esc_attr( $height ?? '' ); ?>">
						</div>
					</div>
					<div class="pp-form-row">
						<div class="tmc-form-field">
							<label for="pp_slot"><?php esc_html_e( 'Vilket träningstillfälle?', 'tandstiftet' ); ?></label>
							<select id="pp_slot" name="pp_slot">
								<?php
								$unsure  = __( 'Vet ej ännu', 'tandstiftet' );
								$options = array_map( function ( $s ) { return trim( $s['day'] . ' ' . $s['time'] ); }, $slots );
								$options[] = $unsure;
								$sv = $slot ?? $options[0];
								foreach ( $options as $opt ) : ?>
									<option value="<?php echo esc_attr( $opt ); ?>" <?php selected( $sv, $opt ); ?>><?php echo esc_html( $opt === $unsure ? __( 'Vet ej ännu — hör av er', 'tandstiftet' ) : $opt ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="tmc-form-field">
							<label for="pp_again"><?php esc_html_e( 'Provat hos oss förut?', 'tandstiftet' ); ?></label>
							<select id="pp_again" name="pp_again">
								<?php $av = $again ?? 'Nej, första gången'; ?>
								<option value="Nej, första gången" <?php selected( $av, 'Nej, första gången' ); ?>><?php esc_html_e( 'Nej, första gången', 'tandstiftet' ); ?></option>
								<option value="Ja, vill prova igen" <?php selected( $av, 'Ja, vill prova igen' ); ?>><?php esc_html_e( 'Ja, vill prova igen', 'tandstiftet' ); ?></option>
							</select>
						</div>
					</div>
					<div class="tmc-form-field">
						<label for="pp_message"><?php esc_html_e( 'Övrigt (frågor, tidigare erfarenhet …)', 'tandstiftet' ); ?></label>
						<textarea id="pp_message" name="pp_message" rows="3"><?php echo esc_textarea( $msg ?? '' ); ?></textarea>
					</div>
					<button type="submit" class="btn btn--primary provapa-form__submit"><?php esc_html_e( 'Skicka', 'tandstiftet' ); ?></button>
				</form>
			<?php endif; ?>
			<?php endif; ?>

			<?php $contact_email = get_theme_mod( 'tmc_contact_email' ); $contact_phone = get_theme_mod( 'tmc_contact_phone' ); ?>
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

<?php get_footer(); ?>
