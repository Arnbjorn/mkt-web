<?php
/**
 * Template Name: Kontakt & Medlemskap
 *
 * @package tandstiftet
 */
get_header();
tmc_page_header(__('Kontakt & Medlemskap','tandstiftet'), __('Välkommen att höra av dig eller ansöka om medlemskap.','tandstiftet'), 'KONTAKT');

// Handle form submission
$member_success = false;
$contact_success = false;
$member_error = false;
$contact_error = false;
$member_spam_error = false;
$contact_spam_error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tmc_nonce_field'])) {
    if (wp_verify_nonce($_POST['tmc_nonce_field'], 'tmc_contact') && ! tmc_antispam_is_bot()) {
        $type = sanitize_text_field($_POST['form_type'] ?? '');
        $name = sanitize_text_field($_POST['contact_name'] ?? '');
        $email = sanitize_email($_POST['contact_email'] ?? '');
        $message = sanitize_textarea_field($_POST['contact_message'] ?? '');
        $phone = sanitize_text_field($_POST['contact_phone'] ?? '');
        $age = sanitize_text_field($_POST['member_age'] ?? '');
        $license = sanitize_text_field($_POST['member_license'] ?? '');

        if ( ! tmc_antispam_valid_age_or_year( $age ) || tmc_antispam_text_is_spammy( $message ) || tmc_antispam_text_is_spammy( $name, 0 ) ) {
            if ($type === 'member') $member_spam_error = true;
            else $contact_spam_error = true;
        } else {
        $admin_email = get_option('admin_email');
        $subject = $type === 'member'
            ? sprintf(__('[%s] Ny medlemsansökan: %s','tandstiftet'), get_bloginfo('name'), $name)
            : sprintf(__('[%s] Nytt kontaktformulär: %s','tandstiftet'), get_bloginfo('name'), $name);

        $body = "Namn: $name\nE-post: $email\nTelefon: $phone\n";
        if ($type === 'member') {
            $body .= "Ålder/födelseår: $age\nLicens: $license\n";
        }
        $body .= "\nMeddelande:\n$message";

        $mail_sent = wp_mail($admin_email, $subject, $body, ['Reply-To: ' . $email]);

        if ($mail_sent) {
            if ($type === 'member') $member_success = true;
            else $contact_success = true;
        } else {
            // wp_mail() failed (common cause: host blocks PHP mail() / no SMTP configured).
            // Log for the site admin to investigate, and tell the visitor to use direct contact instead.
            error_log('Tändstiftet: wp_mail() misslyckades för kontaktformulär (' . $type . '), avsändare: ' . $email);
            if ($type === 'member') $member_error = true;
            else $contact_error = true;
        }
        }
    }
}
?>

<section class="section section--dark">
<div class="container" style="max-width:900px">

	<div class="grid grid--2" style="gap:var(--space-2xl)">

		<!-- MEMBERSHIP FORM -->
		<div id="bli-medlem">
			<div class="label" style="margin-bottom:0.4rem"><?php esc_html_e('Gå med oss','tandstiftet'); ?></div>
			<h2 class="section-title" style="margin-bottom:var(--space-lg)"><?php esc_html_e('Bli medlem','tandstiftet'); ?></h2>
			<p style="color:var(--color-grey-light);margin-bottom:var(--space-xl);line-height:1.7">
				<?php esc_html_e('Fyll i formuläret nedan så kontaktar vi dig med information om hur du ansluter dig till Tändstiftet MC.','tandstiftet'); ?>
			</p>

			<?php if ($member_success) : ?>
				<div style="background:rgba(212,160,23,0.1);border:1px solid var(--color-gold);padding:var(--space-lg);margin-bottom:var(--space-lg)">
					<p style="color:var(--color-gold-light)"><?php esc_html_e('Tack för din ansökan! Vi hör av oss snart.','tandstiftet'); ?></p>
				</div>
			<?php elseif ($member_error) : ?>
				<div style="background:rgba(196,60,60,0.12);border:1px solid #c43c3c;padding:var(--space-lg);margin-bottom:var(--space-lg)">
					<p style="color:#f0a8a8"><?php esc_html_e('Något gick fel och ansökan kunde inte skickas. Testa gärna igen, eller kontakta oss direkt via telefon/e-post nedan.','tandstiftet'); ?></p>
				</div>
			<?php elseif ($member_spam_error) : ?>
				<div style="background:rgba(196,60,60,0.12);border:1px solid #c43c3c;padding:var(--space-lg);margin-bottom:var(--space-lg)">
					<p style="color:#f0a8a8"><?php esc_html_e('Kunde inte tolka uppgifterna — kontrollera ålder/födelseår och att meddelandet inte innehåller länkar, och skicka igen.','tandstiftet'); ?></p>
				</div>
			<?php endif; ?>

			<?php if ( ! $member_success ) : ?>
			<form method="post" class="tmc-form" action="#bli-medlem" novalidate>
				<?php wp_nonce_field('tmc_contact','tmc_nonce_field'); ?>
				<?php tmc_antispam_fields(); ?>
				<input type="hidden" name="form_type" value="member">

				<div class="tmc-form-row">
					<div class="tmc-form-field">
						<label for="m_name"><?php esc_html_e('Namn *','tandstiftet'); ?></label>
						<input type="text" id="m_name" name="contact_name" required autocomplete="name" value="<?php echo esc_attr( (isset($type) && $type==='member') ? ($name ?? '') : '' ); ?>">
					</div>
					<div class="tmc-form-field">
						<label for="m_email"><?php esc_html_e('E-post *','tandstiftet'); ?></label>
						<input type="email" id="m_email" name="contact_email" required autocomplete="email" value="<?php echo esc_attr( (isset($type) && $type==='member') ? ($email ?? '') : '' ); ?>">
					</div>
				</div>
				<div class="tmc-form-row">
					<div class="tmc-form-field">
						<label for="m_phone"><?php esc_html_e('Telefon','tandstiftet'); ?></label>
						<input type="tel" id="m_phone" name="contact_phone" autocomplete="tel" value="<?php echo esc_attr( (isset($type) && $type==='member') ? ($phone ?? '') : '' ); ?>">
					</div>
					<div class="tmc-form-field">
						<label for="m_age"><?php esc_html_e('Ålder / Födelseår','tandstiftet'); ?></label>
						<input type="text" id="m_age" name="member_age" value="<?php echo esc_attr( (isset($type) && $type==='member') ? ($age ?? '') : '' ); ?>">
					</div>
				</div>
				<div class="tmc-form-field">
					<label for="m_license"><?php esc_html_e('Har du körlicens? (ja/nej/vet ej)','tandstiftet'); ?></label>
					<select id="m_license" name="member_license">
						<?php $lv = (isset($type) && $type==='member') ? ($license ?? '') : ''; ?>
						<option value="" <?php selected($lv, ''); ?>><?php esc_html_e('Välj…','tandstiftet'); ?></option>
						<option value="ja" <?php selected($lv, 'ja'); ?>><?php esc_html_e('Ja, aktiv licens','tandstiftet'); ?></option>
						<option value="tidigare" <?php selected($lv, 'tidigare'); ?>><?php esc_html_e('Ja, tidigare','tandstiftet'); ?></option>
						<option value="nej" <?php selected($lv, 'nej'); ?>><?php esc_html_e('Nej','tandstiftet'); ?></option>
					</select>
				</div>
				<div class="tmc-form-field">
					<label for="m_msg"><?php esc_html_e('Berätta lite om dig själv','tandstiftet'); ?></label>
					<textarea id="m_msg" name="contact_message" rows="5"><?php echo esc_textarea( (isset($type) && $type==='member') ? ($message ?? '') : '' ); ?></textarea>
				</div>
				<button type="submit" class="btn btn--primary"><?php esc_html_e('Skicka ansökan','tandstiftet'); ?></button>
			</form>
			<?php endif; ?>
		</div>

		<!-- CONTACT FORM -->
		<div id="kontakta-oss">
			<div class="label" style="margin-bottom:0.4rem"><?php esc_html_e('Frågor?','tandstiftet'); ?></div>
			<h2 class="section-title" style="margin-bottom:var(--space-lg)"><?php esc_html_e('Kontakta oss','tandstiftet'); ?></h2>
			<p style="color:var(--color-grey-light);margin-bottom:var(--space-xl);line-height:1.7">
				<?php esc_html_e('Har du frågor om klubben, tävlingar eller annat? Hör av dig!','tandstiftet'); ?>
			</p>

			<?php if ($contact_success) : ?>
				<div style="background:rgba(212,160,23,0.1);border:1px solid var(--color-gold);padding:var(--space-lg);margin-bottom:var(--space-lg)">
					<p style="color:var(--color-gold-light)"><?php esc_html_e('Tack för ditt meddelande! Vi återkommer inom kort.','tandstiftet'); ?></p>
				</div>
			<?php elseif ($contact_error) : ?>
				<div style="background:rgba(196,60,60,0.12);border:1px solid #c43c3c;padding:var(--space-lg);margin-bottom:var(--space-lg)">
					<p style="color:#f0a8a8"><?php esc_html_e('Något gick fel och meddelandet kunde inte skickas. Testa gärna igen, eller kontakta oss direkt via telefon/e-post nedan.','tandstiftet'); ?></p>
				</div>
			<?php elseif ($contact_spam_error) : ?>
				<div style="background:rgba(196,60,60,0.12);border:1px solid #c43c3c;padding:var(--space-lg);margin-bottom:var(--space-lg)">
					<p style="color:#f0a8a8"><?php esc_html_e('Kunde inte tolka uppgifterna — kontrollera att meddelandet inte innehåller länkar, och skicka igen.','tandstiftet'); ?></p>
				</div>
			<?php endif; ?>

			<?php if ( ! $contact_success ) : ?>
			<form method="post" class="tmc-form" action="#kontakta-oss" novalidate>
				<?php wp_nonce_field('tmc_contact','tmc_nonce_field'); ?>
				<?php tmc_antispam_fields(); ?>
				<input type="hidden" name="form_type" value="contact">

				<div class="tmc-form-row">
					<div class="tmc-form-field">
						<label for="c_name"><?php esc_html_e('Namn *','tandstiftet'); ?></label>
						<input type="text" id="c_name" name="contact_name" required autocomplete="name" value="<?php echo esc_attr( (isset($type) && $type==='contact') ? ($name ?? '') : '' ); ?>">
					</div>
					<div class="tmc-form-field">
						<label for="c_email"><?php esc_html_e('E-post *','tandstiftet'); ?></label>
						<input type="email" id="c_email" name="contact_email" required autocomplete="email" value="<?php echo esc_attr( (isset($type) && $type==='contact') ? ($email ?? '') : '' ); ?>">
					</div>
				</div>
				<div class="tmc-form-field">
					<label for="c_msg"><?php esc_html_e('Meddelande *','tandstiftet'); ?></label>
					<textarea id="c_msg" name="contact_message" rows="8" required><?php echo esc_textarea( (isset($type) && $type==='contact') ? ($message ?? '') : '' ); ?></textarea>
				</div>
				<button type="submit" class="btn btn--primary"><?php esc_html_e('Skicka meddelande','tandstiftet'); ?></button>
			</form>
			<?php endif; ?>

			<?php
			$phone   = get_theme_mod('tmc_contact_phone');
			$email   = get_theme_mod('tmc_contact_email');
			$address = get_theme_mod('tmc_contact_address');
			if ($phone || $email || $address) : ?>
			<div style="margin-top:var(--space-xl);border-top:var(--border-asphalt);padding-top:var(--space-lg)">
				<h3 style="font-size:1rem;color:var(--color-gold);margin-bottom:var(--space-md)"><?php esc_html_e('Direktkontakt','tandstiftet'); ?></h3>
				<?php if ($phone)   echo '<p class="label">' . esc_html__('Telefon','tandstiftet') . '</p><p>' . esc_html($phone) . '</p>'; ?>
				<?php if ($email)   echo '<p class="label" style="margin-top:var(--space-sm)">' . esc_html__('E-post','tandstiftet') . '</p><p><a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a></p>'; ?>
				<?php if ($address) echo '<p class="label" style="margin-top:var(--space-sm)">' . esc_html__('Adress','tandstiftet') . '</p><p>' . nl2br(esc_html($address)) . '</p>'; ?>
			</div>
			<?php endif; ?>
		</div>

	</div>
</div>
</section>

<?php get_footer(); ?>
