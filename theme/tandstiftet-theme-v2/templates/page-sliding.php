<?php
/**
 * Template Name: Sliding (Prova På Vuxna)
 * Description: Sliding — Prova På för vuxna, mot en avgift. Flat track eller speedway beroende på lämplighet/tillgänglighet/önskemål.
 *
 * @package tandstiftet
 */
get_header();

$page_id = get_the_ID();
$pp_page = get_page_by_path('prova-pa');
$pp_url  = $pp_page ? get_permalink( $pp_page->ID ) : home_url('/prova-pa/');

if ( ! tmc_sliding_enabled() ) : ?>

	<?php tmc_page_header(
		__('Sliding','tandstiftet'),
		__('Just nu pausat — men snart tillbaka.','tandstiftet'),
		'SLIDING'
	); ?>

	<section class="section">
		<div class="container container--page" style="max-width:700px;text-align:center">
			<div class="pp-safety-note" style="text-align:left">
				<div class="pp-safety-note__title"><?php esc_html_e('Sliding är tillfälligt pausat','tandstiftet'); ?></div>
				<p><?php esc_html_e('Vi jobbar just nu på vuxen-erbjudandet och tar en kort paus. Vill du prova på speedway eller flat track ändå, hör av dig direkt så hjälper vi dig — eller kolla in Prova På för barn & ungdom nedan.','tandstiftet'); ?></p>
			</div>
			<div style="margin-top:var(--space-xl);display:flex;gap:var(--space-md);justify-content:center;flex-wrap:wrap">
				<a href="<?php echo esc_url( $pp_url ); ?>" class="btn btn--primary"><?php esc_html_e('Se Prova På — Barn & Ungdom','tandstiftet'); ?></a>
				<?php $email = get_theme_mod('tmc_contact_email'); $phone = get_theme_mod('tmc_contact_phone'); ?>
				<?php if ($phone) : ?><a href="tel:<?php echo esc_attr(preg_replace('/\s+/','',$phone)); ?>" class="btn btn--outline">☎ <?php echo esc_html($phone); ?></a><?php endif; ?>
				<?php if ($email) : ?><a href="mailto:<?php echo esc_attr($email); ?>" class="btn btn--outline">✉ <?php echo esc_html($email); ?></a><?php endif; ?>
			</div>
		</div>
	</section>

	<?php get_footer(); return; endif; ?>

<?php tmc_page_header(
	__('Sliding','tandstiftet'),
	__('Prova På för vuxna — ingen erfarenhet krävs. Vi lånar ut allt och har instruktörer på plats.','tandstiftet'),
	'SLIDING'
); ?>

<!-- ═══ BARN-HÄNVISNING ═══ -->
<div class="pp-audience-note">
	<div class="container container--page pp-audience-note__inner">
		<span><?php esc_html_e('Sliding är vår Prova På för vuxna. Har du barn eller är ungdom som vill prova på?','tandstiftet'); ?></span>
		<a href="<?php echo esc_url( $pp_url ); ?>" class="btn btn--outline btn--sm"><?php esc_html_e('Se Prova På för barn & ungdom — gratis →','tandstiftet'); ?></a>
	</div>
</div>

<!-- ═══ FACTS STRIP ═══ -->
<div class="pp-facts-strip">
	<div class="container container--page pp-facts-strip__inner">
		<div class="pp-facts-strip__fact">
			<span class="pp-facts-strip__number">800</span>
			<span class="pp-facts-strip__label"><?php esc_html_e('kr inkl. utrustning','tandstiftet'); ?></span>
			<span class="pp-facts-strip__sublabel"><?php esc_html_e('+225 kr licens','tandstiftet'); ?></span>
		</div>
		<div class="pp-facts-strip__divider"></div>
		<div class="pp-facts-strip__fact">
			<span class="pp-facts-strip__number">1,5</span>
			<span class="pp-facts-strip__label"><?php esc_html_e('timmar på banan','tandstiftet'); ?></span>
		</div>
		<div class="pp-facts-strip__divider"></div>
		<div class="pp-facts-strip__fact">
			<span class="pp-facts-strip__number"><?php echo tmc_flattrack_enabled() ? '2' : '1'; ?></span>
			<span class="pp-facts-strip__label"><?php echo tmc_flattrack_enabled() ? esc_html__('discipliner att välja mellan','tandstiftet') : esc_html__('disciplin — speedway','tandstiftet'); ?></span>
		</div>
		<div class="pp-facts-strip__divider"></div>
		<div class="pp-facts-strip__fact">
			<span class="pp-facts-strip__number">0</span>
			<span class="pp-facts-strip__label"><?php esc_html_e('krav på erfarenhet','tandstiftet'); ?></span>
		</div>
		<div class="pp-facts-strip__divider"></div>
		<div class="pp-facts-strip__cta">
			<a href="#boka" class="btn btn--primary"><?php esc_html_e('Boka din plats','tandstiftet'); ?> →</a>
			<a href="#vad-hander" class="btn btn--outline"><?php esc_html_e('Vad händer på dagen?','tandstiftet'); ?></a>
		</div>
	</div>
</div>

<!-- ═══ PRIS — tydlig uppdelning, lätt att missa annars ═══ -->
<div class="pp-price-callout">
	<div class="container container--page">
		<div class="pp-price-callout__inner">
			<div class="pp-price-callout__row">
				<span><strong>800 kr</strong> <?php esc_html_e('till klubben — utrustning, instruktör & bana ingår','tandstiftet'); ?></span>
				<span class="pp-price-callout__plus">+</span>
				<span><strong>225 kr</strong> <?php esc_html_e('Svemo Prova På-licens — betalas separat, direkt till Svemo','tandstiftet'); ?></span>
			</div>
			<p class="pp-price-callout__note"><?php esc_html_e('Räkna alltså med ca 1 025 kr totalt. Licensen är inte en klubbavgift och syns inte i vår bokningsbekräftelse.','tandstiftet'); ?></p>
		</div>
	</div>
</div>

<!-- ═══ BOKNING & GRUPPER/KALAS/EVENT ═══ -->
<section class="section section--dark" id="grupper-kalas">
	<div class="container container--page">
		<div class="section-header">
			<div class="label"><?php esc_html_e('Alltid bokat i förväg','tandstiftet'); ?></div>
			<h2 class="section-title"><?php esc_html_e('Själv, med kompisar eller ett helt kalas','tandstiftet'); ?></h2>
		</div>
		<p class="pp-intro-text"><?php esc_html_e('Sliding är ingen drop-in-aktivitet — du bokar alltid en tid i förväg, oavsett om du kommer själv eller är flera. Sliding är samtidigt vårt sätt att ta emot grupper, kalas och event: samma upplägg, samma pris per person, men bokat som ett eget tillfälle för ert sällskap.','tandstiftet'); ?></p>
		<div class="pp-next-grid">
			<div class="pp-next-card">
				<div class="pp-next-card__icon">🙋</div>
				<h3 class="pp-next-card__title"><?php esc_html_e('På egen hand','tandstiftet'); ?></h3>
				<p class="pp-next-card__desc"><?php esc_html_e('Boka en plats för dig själv — vi slår ofta ihop enskilda bokningar till samma pass.','tandstiftet'); ?></p>
			</div>
			<div class="pp-next-card">
				<div class="pp-next-card__icon">👥</div>
				<h3 class="pp-next-card__title"><?php esc_html_e('Sällskap & vänner','tandstiftet'); ?></h3>
				<p class="pp-next-card__desc"><?php esc_html_e('Kom några stycken tillsammans — ange gärna hur många ni är när ni bokar så planerar vi passet efter er grupp.','tandstiftet'); ?></p>
			</div>
			<div class="pp-next-card">
				<div class="pp-next-card__icon">🎉</div>
				<h3 class="pp-next-card__title"><?php esc_html_e('Kalas, möhippa & event','tandstiftet'); ?></h3>
				<p class="pp-next-card__desc"><?php esc_html_e('Firma, möhippa, svensexa eller födelsedag — boka Sliding som ett eget event för hela sällskapet. Hör av dig i god tid så bokar vi in er.','tandstiftet'); ?></p>
			</div>
		</div>
	</div>
</section>

<!-- ═══ VILKEN DISCIPLIN ═══ -->
<section class="section" id="disciplin">
	<div class="container container--page">
		<?php if ( tmc_flattrack_enabled() ) : ?>
			<div class="section-header">
				<div class="label"><?php esc_html_e('Flat track eller speedway?','tandstiftet'); ?></div>
				<h2 class="section-title"><?php esc_html_e('Vi hjälper dig hitta rätt','tandstiftet'); ?></h2>
			</div>
			<p class="pp-intro-text"><?php esc_html_e('Som vuxen provar du flat track eller speedway — vilket som passar beror på din erfarenhet, vad som finns tillgängligt just den dagen och vad du själv önskar. Instruktören hjälper dig avgöra vad som är lämpligast om du är osäker.','tandstiftet'); ?></p>
			<div class="pp-schedule-grid">
				<div class="pp-next-card">
					<div class="pp-next-card__icon">🏍️</div>
					<h3 class="pp-next-card__title"><?php esc_html_e('Flat Track','tandstiftet'); ?></h3>
					<p class="pp-next-card__desc"><?php esc_html_e('Dirt track med bromsar — ofta det naturliga förstavalet för helt nya förare. Mer tillgängligt och förlåtande att lära sig på.','tandstiftet'); ?></p>
				</div>
				<div class="pp-next-card">
					<div class="pp-next-card__icon">🏁</div>
					<h3 class="pp-next-card__title"><?php esc_html_e('Speedway','tandstiftet'); ?></h3>
					<p class="pp-next-card__desc"><?php esc_html_e('Oval bana utan broms — för dig med tidigare erfarenhet av motorcykel eller som vill utmana dig direkt, i mån av tillgänglig maskin.','tandstiftet'); ?></p>
				</div>
			</div>

			<div class="pp-partner-callout">
				<div class="pp-partner-callout__body">
					<span class="pp-partner-callout__label"><?php esc_html_e('Flat track sker i samarbete med','tandstiftet'); ?></span>
					<a href="https://sparks.tändstiftet.se/" target="_blank" rel="noopener" class="pp-partner-callout__title-link"><span class="pp-partner-callout__title">The Sparks Flattrack School</span></a>
					<p class="pp-partner-callout__desc"><?php esc_html_e('Vet du redan att det är flat track du vill köra? Då kan du höra av dig direkt till Sparks eget tränarteam istället för att boka via formuläret nedan — de svarar snabbast på specifika frågor om flat track-passen.','tandstiftet'); ?></p>
					<a href="https://sparks.tändstiftet.se/" target="_blank" rel="noopener" class="pp-partner-callout__more-link"><?php esc_html_e('Läs mer om Sparks Flattrack School','tandstiftet'); ?> →</a>
				</div>
				<div class="pp-partner-callout__contact">
					<span class="pp-partner-callout__contact-name">Sören Teil Hansen</span>
					<span class="pp-partner-callout__contact-role"><?php esc_html_e('Regerande svensk mästare, Hooligan Class Flattrack (Harley-Davidson)','tandstiftet'); ?></span>
					<a href="tel:0700055982" class="pp-partner-callout__contact-phone">070-005 59 82</a>
				</div>
			</div>
		<?php else : ?>
			<div class="section-header">
				<div class="label"><?php esc_html_e('Disciplin','tandstiftet'); ?></div>
				<h2 class="section-title"><?php esc_html_e('Speedway','tandstiftet'); ?></h2>
			</div>
			<p class="pp-intro-text"><?php esc_html_e('Sliding kör vi just nu på speedway — en oval bana utan broms. Ingen erfarenhet krävs, instruktören hjälper dig komma igång i din egen takt.','tandstiftet'); ?></p>
		<?php endif; ?>
	</div>
</section>

<!-- ═══ UTRUSTNING — kort sammanfattning, ingen genomgång behövs på sidan ═══ -->
<section class="section section--dark" id="utrustning">
	<div class="container container--page">
		<div class="section-header">
			<div class="label"><?php esc_html_e('Ingår i priset','tandstiftet'); ?></div>
			<h2 class="section-title"><?php esc_html_e('Vi hjälper dig med utrustningen','tandstiftet'); ?></h2>
		</div>
		<p class="pp-intro-text"><?php esc_html_e('Efter säkerhetsgenomgången hjälper instruktören dig att prova ut all skyddsutrustning som klubben tillhandahåller. Du behöver inte äga något själv eller fundera på det i förväg.','tandstiftet'); ?></p>

		<div class="pp-gear-bring">
			<div class="pp-gear-bring__title"><?php esc_html_e('Ingår i priset','tandstiftet'); ?></div>
			<div class="pp-gear-bring__items">
				<span><?php esc_html_e('Hjälm','tandstiftet'); ?></span>
				<span><?php esc_html_e('Cross-jacka & byxor','tandstiftet'); ?></span>
				<span><?php esc_html_e('Rygg-, nack- & armbågsskydd','tandstiftet'); ?></span>
				<span><?php esc_html_e('Stövlar & handskar','tandstiftet'); ?></span>
				<span><?php echo tmc_flattrack_enabled() ? esc_html__('Motorcykel — flat track eller speedway','tandstiftet') : esc_html__('Motorcykel — speedway','tandstiftet'); ?></span>
				<span><?php esc_html_e('Personlig instruktör hela passet','tandstiftet'); ?></span>
			</div>
		</div>
		<div class="pp-gear-bring" style="margin-top:var(--space-md)">
			<div class="pp-gear-bring__title"><?php esc_html_e('Det enda du tar med','tandstiftet'); ?></div>
			<div class="pp-gear-bring__items">
				<span><?php esc_html_e('Långa underbyxor & undertröja','tandstiftet'); ?></span>
				<span><?php esc_html_e('Strumpor som täcker ankeln','tandstiftet'); ?></span>
				<span><?php esc_html_e('Kläder du inte är rädd om','tandstiftet'); ?></span>
				<span><?php esc_html_e('Vatten','tandstiftet'); ?></span>
			</div>
		</div>
	</div>
</section>

<!-- ═══ VAD HÄNDER PÅ DAGEN ═══ -->
<section class="section pp-day-section" id="vad-hander">
	<div class="container container--page">
		<div class="section-header">
			<div class="label"><?php esc_html_e('Steg för steg','tandstiftet'); ?></div>
			<h2 class="section-title"><?php esc_html_e('Så går dagen till','tandstiftet'); ?></h2>
		</div>
		<div class="pp-steps">
			<div class="pp-step">
				<div class="pp-step__num">1</div>
				<div class="pp-step__body">
					<h3 class="pp-step__title"><?php esc_html_e('Ankomst — 30 min före start','tandstiftet'); ?></h3>
					<p class="pp-step__desc"><?php echo tmc_flattrack_enabled() ? esc_html__('Kom i god tid. Visa licens och betala. Vi pratar igenom vilken disciplin — flat track eller speedway — som passar dig bäst.','tandstiftet') : esc_html__('Kom i god tid. Visa licens och betala.','tandstiftet'); ?></p>
				</div>
			</div>
			<div class="pp-step">
				<div class="pp-step__num">2</div>
				<div class="pp-step__body">
					<h3 class="pp-step__title"><?php esc_html_e('Säkerhetsgenomgång','tandstiftet'); ?></h3>
					<p class="pp-step__desc"><?php esc_html_e('Instruktören går igenom säkerhetsregler, signaler på banan och grundläggande körteknik, och hjälper dig samtidigt med skyddsutrustningen. Ca 20 minuter — obligatoriskt för alla.','tandstiftet'); ?></p>
				</div>
			</div>
			<div class="pp-step">
				<div class="pp-step__num">3</div>
				<div class="pp-step__body">
					<h3 class="pp-step__title"><?php esc_html_e('Ut på banan','tandstiftet'); ?></h3>
					<p class="pp-step__desc"><?php esc_html_e('Du börjar i liten grupp med max 3–4 nybörjare. Instruktören styr tempot. Pausa när du vill — ingen stress.','tandstiftet'); ?></p>
				</div>
			</div>
			<div class="pp-step">
				<div class="pp-step__num">4</div>
				<div class="pp-step__body">
					<h3 class="pp-step__title"><?php esc_html_e('Banunderhåll & pauser','tandstiftet'); ?></h3>
					<p class="pp-step__desc"><?php esc_html_e('Banan vattnas och fixas regelbundet. Vid underhåll stannar all körning. Instruktören ger klartecken när det är dags igen.','tandstiftet'); ?></p>
				</div>
			</div>
			<div class="pp-step">
				<div class="pp-step__num">5</div>
				<div class="pp-step__body">
					<h3 class="pp-step__title"><?php esc_html_e('Eftersnack','tandstiftet'); ?></h3>
					<p class="pp-step__desc"><?php esc_html_e('Vi pratar igenom hur det gick, svarar på frågor och berättar om nästa steg om du vill fortsätta.','tandstiftet'); ?></p>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- ═══ LICENS ═══ -->
<section class="section section--dark" id="licens">
	<div class="container container--page">
		<div class="section-header">
			<div class="label"><?php esc_html_e('Svemo','tandstiftet'); ?></div>
			<h2 class="section-title"><?php esc_html_e('Licens — snabbt och enkelt','tandstiftet'); ?></h2>
		</div>
		<div class="pp-license-grid">
			<div class="pp-license-card pp-license-card--highlight">
				<div class="pp-license-card__price">225 kr</div>
				<div class="pp-license-card__title"><?php esc_html_e('Prova På-licens','tandstiftet'); ?></div>
				<p class="pp-license-card__desc"><?php esc_html_e('Köps på provapasvemo.se. Gäller bara angiven dag och bana. Lös den på morgonen — inte i förväg ifall vi ställer in.','tandstiftet'); ?></p>
				<a href="https://www.provapasvemo.se" target="_blank" rel="noopener" class="btn btn--outline btn--sm" style="margin-top:1rem"><?php esc_html_e('Köp licens →','tandstiftet'); ?></a>
			</div>
		</div>
		<div class="pp-license-note">
			<?php esc_html_e('OBS: licensen på 225 kr betalas separat till Svemo — den ingår inte i klubbens pris på 800 kr. Sliding-priset inkluderar dock MKT-medlemskap för resten av kalenderåret — du är redan halvvägs in i klubben.','tandstiftet'); ?>
		</div>
	</div>
</section>

<!-- ═══ NÄSTA STEG ═══ -->
<section class="section" id="nasta-steg">
	<div class="container container--page">
		<div class="section-header">
			<div class="label"><?php esc_html_e('Gillade du det?','tandstiftet'); ?></div>
			<h2 class="section-title"><?php esc_html_e('Nästa steg','tandstiftet'); ?></h2>
		</div>
		<div class="pp-next-grid">
			<div class="pp-next-card">
				<div class="pp-next-card__icon">🏁</div>
				<h3 class="pp-next-card__title"><?php esc_html_e('Bli aktiv medlem','tandstiftet'); ?></h3>
				<p class="pp-next-card__desc"><?php esc_html_e('500 kr/år. Lös träningslicens via Svemo och börja träna regelbundet med klubben.','tandstiftet'); ?></p>
				<a href="/klubbinfo/#bli-medlem" class="pp-next-card__link"><?php esc_html_e('Läs mer om medlemskap','tandstiftet'); ?> →</a>
			</div>
			<div class="pp-next-card">
				<div class="pp-next-card__icon">🎓</div>
				<h3 class="pp-next-card__title"><?php esc_html_e('Veteranklassen','tandstiftet'); ?></h3>
				<p class="pp-next-card__desc"><?php esc_html_e('Kör du 40+ finns en avslappnad grupp med klassiska maskiner — hör av dig så berättar vi mer.','tandstiftet'); ?></p>
				<a href="/sektioner/veteran/" class="pp-next-card__link"><?php esc_html_e('Läs mer om Veteran','tandstiftet'); ?> →</a>
			</div>
			<div class="pp-next-card">
				<div class="pp-next-card__icon">🏆</div>
				<h3 class="pp-next-card__title"><?php esc_html_e('Tävla','tandstiftet'); ?></h3>
				<p class="pp-next-card__desc"><?php esc_html_e('Lös tävlingslicens och ta steget in i seriespelet. Vår Division 1-trupp söker alltid talanger.','tandstiftet'); ?></p>
				<a href="/forare-och-lag/" class="pp-next-card__link"><?php esc_html_e('Se förare & lag','tandstiftet'); ?> →</a>
			</div>
		</div>
	</div>
</section>

<!-- ═══ BOKA ═══ -->
<section class="pp-book-section" id="boka">
	<div class="pp-book-section__bg" aria-hidden="true"></div>
	<div class="container pp-book-section__inner">
		<div class="pp-book-section__text">
			<div class="label" style="margin-bottom:0.5rem"><?php esc_html_e('Redo?','tandstiftet'); ?></div>
			<h2 class="pp-book-section__title"><?php esc_html_e('Boka din plats','tandstiftet'); ?></h2>
			<p class="pp-book-section__desc"><?php esc_html_e('Sliding bokas alltid i förväg — vi tar inte drop-in. Välj ett ledigt datum i kalendern, fyll i formuläret och skicka.','tandstiftet'); ?></p>
			<div class="pp-book-needs">
				<div class="pp-book-needs__title"><?php esc_html_e('Vi behöver veta:','tandstiftet'); ?></div>
				<p>◆ &nbsp;<?php esc_html_e('Namn och ålder','tandstiftet'); ?></p>
				<p>◆ &nbsp;<?php esc_html_e('Antal personer (om ni är flera / kalas)','tandstiftet'); ?></p>
				<p>◆ &nbsp;<?php esc_html_e('Skostorlek (för stövlar)','tandstiftet'); ?></p>
				<p>◆ &nbsp;<?php esc_html_e('Ungefärlig längd (för overall)','tandstiftet'); ?></p>
				<?php if ( tmc_flattrack_enabled() ) : ?><p>◆ &nbsp;<?php esc_html_e('Önskad disciplin (flat track / speedway)','tandstiftet'); ?></p><?php endif; ?>
			</div>
		</div>
		<div class="pp-book-section__form">
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
				if ( $discipline !== 'flattrack' || ! tmc_flattrack_enabled() ) $discipline = 'speedway';
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
			<?php if ($success) : ?>
				<div class="pp-form-success">
					<div style="font-size:2rem;margin-bottom:0.5rem">✓</div>
					<h3><?php esc_html_e('Bokning mottagen!','tandstiftet'); ?></h3>
					<p><?php echo esc_html( sprintf( __('Din plats den %s är reserverad. Vi återkommer med bekräftelse inom kort. Kom ihåg att lösa licens på provapasvemo.se.','tandstiftet'), $date ) ); ?></p>
				</div>
			<?php elseif ($mail_error) : ?>
				<div class="pp-form-error">
					<div style="font-size:2rem;margin-bottom:0.5rem">⚠</div>
					<h3><?php esc_html_e('Något gick fel','tandstiftet'); ?></h3>
					<p><?php esc_html_e('Bokningen kunde inte skickas just nu. Testa gärna igen om en stund, eller kontakta oss direkt nedan.','tandstiftet'); ?></p>
				</div>
			<?php else : ?>

				<?php if ( $spam_error ) : ?>
					<div class="pp-form-error" style="margin-bottom:var(--space-lg)">
						<div style="font-size:2rem;margin-bottom:0.5rem">⚠</div>
						<h3><?php esc_html_e('Kunde inte tolka uppgifterna','tandstiftet'); ?></h3>
						<p><?php esc_html_e('Kontrollera att åldern är ett rimligt tal och att meddelandet inte innehåller länkar, och skicka igen.','tandstiftet'); ?></p>
					</div>
				<?php elseif ( $date_full_error ) : ?>
					<div class="pp-form-error" style="margin-bottom:var(--space-lg)">
						<div style="font-size:2rem;margin-bottom:0.5rem">⚠</div>
						<h3><?php esc_html_e('Det datumet går inte att boka','tandstiftet'); ?></h3>
						<p><?php esc_html_e('Antingen blev det precis fullbokat, eller så erbjuder vi inte Sliding den dagen. Välj ett annat datum i kalendern nedan och skicka igen.','tandstiftet'); ?></p>
					</div>
				<?php elseif ( $date_invalid_error ) : ?>
					<div class="pp-form-error" style="margin-bottom:var(--space-lg)">
						<div style="font-size:2rem;margin-bottom:0.5rem">⚠</div>
						<h3><?php esc_html_e('Välj ett datum','tandstiftet'); ?></h3>
						<p><?php esc_html_e('Klicka på ett ledigt datum i kalendern innan du skickar formuläret.','tandstiftet'); ?></p>
					</div>
				<?php elseif ( $within_24h_error ) : ?>
					<div class="pp-form-error" style="margin-bottom:var(--space-lg)">
						<div style="font-size:2rem;margin-bottom:0.5rem">⚠</div>
						<h3><?php esc_html_e('För sent för bokning online','tandstiftet'); ?></h3>
						<p><?php esc_html_e('Det här datumet ligger mindre än 24 timmar bort. Hör av er direkt via telefon eller mejl nedan så löser vi det tillsammans.','tandstiftet'); ?></p>
					</div>
				<?php endif; ?>

				<?php
				$weekday_labels = [
					1 => 'Måndagar', 2 => 'Tisdagar', 3 => 'Onsdagar', 4 => 'Torsdagar',
					5 => 'Fredagar', 6 => 'Lördagar', 7 => 'Söndagar',
				];
				$days_map = [1=>'mon',2=>'tue',3=>'wed',4=>'thu',5=>'fri',6=>'sat',7=>'sun'];
				$speedway_summary = tmc_sliding_schedule_summary( $weekday_labels, $days_map, '_speedway' );
				$flattrack_summary = tmc_flattrack_enabled() ? tmc_sliding_schedule_summary( $weekday_labels, $days_map, '_flattrack' ) : [];
				?>
				<?php if ( tmc_flattrack_enabled() ) : ?>
					<div class="pp-discipline-tabs" id="pp-discipline-tabs">
						<button type="button" class="pp-discipline-tab<?php echo ( ! isset($discipline) || $discipline !== 'flattrack' ) ? ' is-active' : ''; ?>" data-discipline="speedway"><?php esc_html_e('Speedway','tandstiftet'); ?></button>
						<button type="button" class="pp-discipline-tab<?php echo ( isset($discipline) && $discipline === 'flattrack' ) ? ' is-active' : ''; ?>" data-discipline="flattrack"><?php esc_html_e('Flat track','tandstiftet'); ?></button>
					</div>
					<?php if ( $speedway_summary ) : ?>
						<p class="pp-schedule-summary" data-discipline="speedway" style="color:var(--color-grey-light);margin-bottom:var(--space-md)<?php echo ( isset($discipline) && $discipline === 'flattrack' ) ? ';display:none' : ''; ?>">
							<?php echo esc_html__( 'Speedway erbjuds normalt: ', 'tandstiftet' ) . esc_html( implode( ', ', $speedway_summary ) ); ?>
						</p>
					<?php endif; ?>
					<?php if ( $flattrack_summary ) : ?>
						<p class="pp-schedule-summary" data-discipline="flattrack" style="color:var(--color-grey-light);margin-bottom:var(--space-md)<?php echo ( isset($discipline) && $discipline === 'flattrack' ) ? '' : ';display:none'; ?>">
							<?php echo esc_html__( 'Flat track erbjuds normalt: ', 'tandstiftet' ) . esc_html( implode( ', ', $flattrack_summary ) ); ?>
						</p>
					<?php endif; ?>
				<?php elseif ( $speedway_summary ) : ?>
					<p style="color:var(--color-grey-light);margin-bottom:var(--space-md)">
						<?php echo esc_html__( 'Sliding erbjuds normalt: ', 'tandstiftet' ) . esc_html( implode( ', ', $speedway_summary ) ); ?>
					</p>
				<?php endif; ?>

				<div class="pp-calendar" id="pp-calendar" data-availability="<?php echo esc_attr( wp_json_encode( tmc_sliding_get_calendar_data( 90 ) ) ); ?>" data-flattrack-enabled="<?php echo tmc_flattrack_enabled() ? '1' : '0'; ?>">
					<div class="pp-calendar__nav">
						<button type="button" class="pp-calendar__nav-btn" id="pp-cal-prev">←</button>
						<span class="pp-calendar__month-label" id="pp-cal-label"></span>
						<button type="button" class="pp-calendar__nav-btn" id="pp-cal-next">→</button>
					</div>
					<div class="pp-calendar__weekdays">
						<span>Mån</span><span>Tis</span><span>Ons</span><span>Tor</span><span>Fre</span><span>Lör</span><span>Sön</span>
					</div>
					<div class="pp-calendar__grid" id="pp-cal-grid"></div>
					<div class="pp-calendar__legend">
						<span><i class="i-avail"></i> <?php esc_html_e('Lediga platser','tandstiftet'); ?></span>
						<span><i class="i-low"></i> <?php esc_html_e('Få platser kvar','tandstiftet'); ?></span>
						<span><i class="i-contact"></i> <?php esc_html_e('Inom 24h — kontakta oss direkt','tandstiftet'); ?></span>
						<span><i class="i-full"></i> <?php esc_html_e('Fullt / stängt','tandstiftet'); ?></span>
					</div>
					<div class="pp-calendar__selected-info" id="pp-cal-selected-info" style="display:none"></div>
				</div>

			<form method="post" class="pp-form" action="#boka" id="pp-sliding-form">
				<?php wp_nonce_field('pp_booking','pp_nonce'); ?>
				<?php tmc_antispam_fields(); ?>
				<input type="hidden" name="pp_date" id="pp_date_input" value="<?php echo esc_attr($date ?? ''); ?>">
				<div class="pp-form-row">
					<div class="tmc-form-field">
						<label><?php esc_html_e('Namn *','tandstiftet'); ?></label>
						<input type="text" name="pp_name" required autocomplete="name" value="<?php echo esc_attr($name ?? ''); ?>">
					</div>
					<div class="tmc-form-field">
						<label><?php esc_html_e('Ålder','tandstiftet'); ?></label>
						<input type="text" name="pp_age" value="<?php echo esc_attr($age ?? ''); ?>">
					</div>
				</div>
				<div class="pp-form-row">
					<div class="tmc-form-field">
						<label><?php esc_html_e('E-post *','tandstiftet'); ?></label>
						<input type="email" name="pp_email" required autocomplete="email" value="<?php echo esc_attr($email ?? ''); ?>">
					</div>
					<div class="tmc-form-field">
						<label><?php esc_html_e('Telefon','tandstiftet'); ?></label>
						<input type="tel" name="pp_phone" autocomplete="tel" value="<?php echo esc_attr($phone ?? ''); ?>">
					</div>
				</div>
				<div class="pp-form-row">
					<div class="tmc-form-field">
						<label><?php esc_html_e('Skostorlek','tandstiftet'); ?></label>
						<input type="text" name="pp_shoe" placeholder="t.ex. 42" value="<?php echo esc_attr($shoe ?? ''); ?>">
					</div>
					<div class="tmc-form-field">
						<label><?php esc_html_e('Längd (cm)','tandstiftet'); ?></label>
						<input type="text" name="pp_height" placeholder="t.ex. 175" value="<?php echo esc_attr($height ?? ''); ?>">
					</div>
				</div>
				<div class="pp-form-row">
					<div class="tmc-form-field">
						<label><?php esc_html_e('Typ av bokning','tandstiftet'); ?></label>
						<select name="pp_group_type">
							<?php $gt = $group_type ?? 'Enskild'; ?>
							<option value="Enskild" <?php selected($gt, 'Enskild'); ?>><?php esc_html_e('Enskild — bara jag','tandstiftet'); ?></option>
							<option value="Sällskap/vänner" <?php selected($gt, 'Sällskap/vänner'); ?>><?php esc_html_e('Sällskap / vänner','tandstiftet'); ?></option>
							<option value="Kalas/möhippa/svensexa" <?php selected($gt, 'Kalas/möhippa/svensexa'); ?>><?php esc_html_e('Kalas / möhippa / svensexa','tandstiftet'); ?></option>
							<option value="Företag/personalaktivitet" <?php selected($gt, 'Företag/personalaktivitet'); ?>><?php esc_html_e('Företag / personalaktivitet','tandstiftet'); ?></option>
						</select>
					</div>
					<div class="tmc-form-field">
						<label><?php esc_html_e('Antal personer','tandstiftet'); ?></label>
						<input type="number" min="1" name="pp_group_size" value="<?php echo esc_attr($group_size ?? 1); ?>">
					</div>
				</div>
				<div class="pp-form-row">
					<div class="tmc-form-field">
						<label><?php esc_html_e('Disciplin','tandstiftet'); ?></label>
						<?php
						$repop_discipline = ( isset($discipline) && $discipline === 'flattrack' ) ? 'flattrack' : ( tmc_flattrack_enabled() ? '' : 'speedway' );
						$repop_discipline_label = $repop_discipline === 'flattrack' ? 'Flat track' : ( $repop_discipline === 'speedway' ? 'Speedway' : '' );
						?>
						<input type="text" id="pp_discipline_display" readonly value="<?php echo esc_attr($repop_discipline_label); ?>" style="cursor:default">
						<input type="hidden" name="pp_discipline" id="pp_discipline_input" value="<?php echo esc_attr($repop_discipline ?: 'speedway'); ?>">
					</div>
					<div class="tmc-form-field">
						<label><?php esc_html_e('Valt datum','tandstiftet'); ?></label>
						<?php
						$repop_date_label = '';
						if ( ! empty( $date ) ) {
							$d2 = DateTime::createFromFormat('Y-m-d', $date);
							if ( $d2 ) {
								$dagar = ['Söndag','Måndag','Tisdag','Onsdag','Torsdag','Fredag','Lördag'];
								$manader = ['','Januari','Februari','Mars','April','Maj','Juni','Juli','Augusti','September','Oktober','November','December'];
								$repop_date_label = $dagar[(int)$d2->format('w')] . ' ' . (int)$d2->format('j') . ' ' . $manader[(int)$d2->format('n')];
							}
						}
						?>
						<input type="text" id="pp_date_display" readonly value="<?php echo esc_attr($repop_date_label); ?>" placeholder="<?php esc_attr_e('Välj i kalendern ovan','tandstiftet'); ?>" style="cursor:default">
					</div>
				</div>
				<div class="tmc-form-field">
					<label><?php esc_html_e('Övrigt (frågor, tidigare erfarenhet etc.)','tandstiftet'); ?></label>
					<textarea name="pp_message" rows="3"><?php echo esc_textarea($msg ?? ''); ?></textarea>
				</div>
				<button type="submit" class="btn btn--primary" style="width:100%;justify-content:center" id="pp_submit_btn"><?php esc_html_e('Skicka bokning','tandstiftet'); ?></button>
			</form>
			<?php endif; ?>

			<div class="pp-book-direct">
				<span><?php esc_html_e('Eller kontakta oss direkt:','tandstiftet'); ?></span>
				<?php $email = get_theme_mod('tmc_contact_email'); $phone = get_theme_mod('tmc_contact_phone'); ?>
				<?php if ($email) : ?><a href="mailto:<?php echo esc_attr($email); ?>">✉ <?php echo esc_html($email); ?></a><?php endif; ?>
				<?php if ($phone) : ?><a href="tel:<?php echo esc_attr(preg_replace('/\s+/','',$phone)); ?>">☎ <?php echo esc_html($phone); ?></a><?php endif; ?>
			</div>
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
	var grid = document.getElementById('pp-cal-grid');
	var label = document.getElementById('pp-cal-label');
	var prevBtn = document.getElementById('pp-cal-prev');
	var nextBtn = document.getElementById('pp-cal-next');

	// Carries over from a failed submission (e.g. spam/age validation error)
	// so the visitor doesn't have to re-pick their date/discipline.
	var currentDiscipline = disciplineInput.value || 'speedway';

	var monthNames = ['Januari','Februari','Mars','April','Maj','Juni','Juli','Augusti','September','Oktober','November','December'];
	var today = new Date(); today.setHours(0,0,0,0);
	var viewYear = today.getFullYear();
	var viewMonth = today.getMonth();

	if (dateInput.value) {
		var preDate = new Date(dateInput.value + 'T00:00:00');
		if (!isNaN(preDate.getTime())) {
			viewYear = preDate.getFullYear();
			viewMonth = preDate.getMonth();
			if (selectedInfo && dateDisplay.value) {
				selectedInfo.style.display = 'block';
				selectedInfo.textContent = (currentDiscipline === 'flattrack' ? 'Flat track' : 'Speedway') + ' — Valt: ' + dateDisplay.value;
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

	function render(){
		var availability = availabilityByDiscipline[currentDiscipline] || {};
		grid.innerHTML = '';
		label.textContent = monthNames[viewMonth] + ' ' + viewYear;

		var firstDay = new Date(viewYear, viewMonth, 1);
		var startOffset = (firstDay.getDay() + 6) % 7; // Monday-first
		var daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();

		for (var i = 0; i < startOffset; i++) {
			var empty = document.createElement('div');
			empty.className = 'pp-calendar__day is-empty';
			grid.appendChild(empty);
		}

		for (var d = 1; d <= daysInMonth; d++) {
			var cell = document.createElement('div');
			cell.className = 'pp-calendar__day';

			var numEl = document.createElement('span');
			numEl.className = 'pp-calendar__day-num';
			numEl.textContent = d;
			cell.appendChild(numEl);

			var key = fmt(viewYear, viewMonth, d);
			var cellDate = new Date(viewYear, viewMonth, d);
			var info = availability.hasOwnProperty(key) ? availability[key] : null;
			var open = info ? info.open : false;
			var remaining = info ? info.remaining : 0;
			var within24h = info ? info.within24h : true;
			var slotTime = info ? info.time : '';

			if (cellDate < minDate || cellDate > maxDate || !info || !open) {
				// Either outside the bookable window, or simply not a day this
				// discipline runs on this week — no "Fullt" tag, it just isn't a service day.
				cell.classList.add('is-disabled');
			} else if (remaining <= 0) {
				cell.classList.add('is-disabled');
				var tagFull = document.createElement('span');
				tagFull.className = 'pp-calendar__day-tag';
				tagFull.textContent = 'Fullt';
				cell.appendChild(tagFull);
			} else if (within24h) {
				cell.classList.add('is-contact');
				var tagContact = document.createElement('span');
				tagContact.className = 'pp-calendar__day-tag';
				tagContact.textContent = 'Ring oss';
				cell.appendChild(tagContact);
			} else {
				if (remaining <= 2) cell.classList.add('is-low');
				if (dateInput.value === key && disciplineInput.value === currentDiscipline) cell.classList.add('is-selected');
				cell.addEventListener('click', (function(key, remaining, slotTime, discipline){
					return function(){
						dateInput.value = key;
						disciplineInput.value = discipline;
						disciplineDisplay.value = disciplineLabel(discipline);
						var d2 = new Date(key + 'T00:00:00');
						var dayNames = ['Söndag','Måndag','Tisdag','Onsdag','Torsdag','Fredag','Lördag'];
						dateDisplay.value = dayNames[d2.getDay()] + ' ' + d2.getDate() + ' ' + monthNames[d2.getMonth()] + (slotTime ? ' kl ' + slotTime : '');
						selectedInfo.style.display = 'block';
						selectedInfo.textContent = disciplineLabel(discipline) + ' — Valt: ' + dateDisplay.value + ' — ' + remaining + ' platser kvar';
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
				tabs.forEach(function(t){ t.classList.toggle('is-active', t === tab); });
				document.querySelectorAll('.pp-schedule-summary').forEach(function(el){
					el.style.display = (el.dataset.discipline === currentDiscipline) ? 'block' : 'none';
				});
				// switching discipline invalidates any previously chosen date
				dateInput.value = '';
				disciplineInput.value = currentDiscipline;
				disciplineDisplay.value = disciplineLabel(currentDiscipline);
				dateDisplay.value = '';
				selectedInfo.style.display = 'none';
				render();
			});
		});
	} else {
		disciplineInput.value = 'speedway';
		disciplineDisplay.value = 'Speedway';
	}

	document.getElementById('pp-sliding-form') && document.getElementById('pp-sliding-form').addEventListener('submit', function(e){
		if (!dateInput.value) {
			e.preventDefault();
			alert('Välj ett datum i kalendern innan du skickar bokningen.');
		}
	});

	render();
})();
</script>

<?php get_footer(); ?>
