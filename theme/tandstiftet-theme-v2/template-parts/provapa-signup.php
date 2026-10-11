<?php
/**
 * Prova På (barn och ungdom) — "Hör av dig innan du kommer": texts, form, form handling.
 * Used by templates/page-prova-pa.php and by the combined page (templates/page-prova-pa-combined.php).
 * Expects in scope: $t (text function), $season_open, $signup, $slots. Optional: $signup_anchor (section id).
 * The hidden field pp_form=provapa keeps this handler apart from the Sliding booking on the combined page.
 *
 * @package tandstiftet
 */
?>
<!-- ═══ HÖR AV DIG INNAN (beige, formulär) ═══ -->
<section class="section provapa-book" id="<?php echo esc_attr( $signup_anchor ?? 'boka' ); ?>">
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
				<p class="provapa-read__lead"><?php echo nl2br( esc_html( $signup['message'] ) ); ?></p>
			<?php endif; ?>
		</div>

		<div class="provapa-book__form">
			<?php if ( $season_open ) : ?>
			<?php
			// Handle form
			$success    = false;
			$mail_error = false;
			$spam_error = false;
			if ( $_SERVER['REQUEST_METHOD'] === 'POST' && ( $_POST['pp_form'] ?? '' ) === 'provapa' && isset( $_POST['pp_nonce'] ) && wp_verify_nonce( $_POST['pp_nonce'], 'pp_booking' ) && ! tmc_antispam_is_bot() ) {
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
				<form method="post" class="pp-form provapa-form" action="#<?php echo esc_attr( $signup_anchor ?? 'boka' ); ?>">
					<input type="hidden" name="pp_form" value="provapa">
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
