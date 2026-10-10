<?php
/**
 * Template Name: Prova På (Barn & Ungdom)
 * Description: Prova På-sida för barn & ungdomar — gratis, speedwaymaskiner anpassade för yngre förare.
 *
 * @package tandstiftet
 */
get_header();

$page_id = get_the_ID();
$sliding_page = get_page_by_path('sliding');
$sliding_url  = $sliding_page ? get_permalink( $sliding_page->ID ) : home_url('/sliding/');
?>

<?php tmc_page_header(
	__('Prova På','tandstiftet'),
	__('För barn & ungdomar — kostnadsfritt från klubben. Vi lånar ut allt och har instruktörer på plats. (Svemos licensavgift på 225 kr tillkommer för den som är 12+ år.)','tandstiftet'),
	'PROVA PÅ'
); ?>

<!-- ═══ VUXEN-HÄNVISNING ═══ -->
<?php if ( tmc_sliding_enabled() ) : ?>
<div class="pp-audience-note">
	<div class="container container--page pp-audience-note__inner">
		<span><?php esc_html_e('Den här sidan är för barn & ungdomar. Är du vuxen och vill prova på?','tandstiftet'); ?></span>
		<a href="<?php echo esc_url( $sliding_url ); ?>" class="btn btn--outline btn--sm"><?php esc_html_e('Se Sliding — Prova På för vuxna →','tandstiftet'); ?></a>
	</div>
</div>
<?php endif; ?>

<?php if ( ! tmc_provapa_season_open() ) : ?>
<!-- ═══ SÄSONG STÄNGD ═══ -->
<div class="pp-season-closed">
	<div class="container container--page pp-season-closed__inner">
		<div class="pp-season-closed__icon" aria-hidden="true">🏁</div>
		<div>
			<div class="pp-season-closed__title"><?php esc_html_e('Säsongen är slut för i år','tandstiftet'); ?></div>
			<p class="pp-season-closed__text"><?php echo nl2br( esc_html( tmc_provapa_season_message() ) ); ?></p>
		</div>
	</div>
</div>
<?php endif; ?>

<!-- ═══ FACTS STRIP ═══ -->
<div class="pp-facts-strip">
	<div class="container container--page pp-facts-strip__inner">
		<div class="pp-facts-strip__fact">
			<span class="pp-facts-strip__number">0</span>
			<span class="pp-facts-strip__label"><?php esc_html_e('kr — helt gratis','tandstiftet'); ?></span>
			<span class="pp-facts-strip__sublabel"><?php esc_html_e('+225 kr licens (12+)','tandstiftet'); ?></span>
		</div>
		<div class="pp-facts-strip__divider"></div>
		<div class="pp-facts-strip__fact">
			<span class="pp-facts-strip__number" style="font-size:1.5rem">Ons 17 / Lör 10</span>
			<span class="pp-facts-strip__label"><?php esc_html_e('ordinarie barnträning','tandstiftet'); ?></span>
		</div>
		<div class="pp-facts-strip__divider"></div>
		<div class="pp-facts-strip__fact">
			<span class="pp-facts-strip__number">6+</span>
			<span class="pp-facts-strip__label"><?php esc_html_e('år välkommen','tandstiftet'); ?></span>
		</div>
		<div class="pp-facts-strip__divider"></div>
		<div class="pp-facts-strip__fact">
			<span class="pp-facts-strip__number">0</span>
			<span class="pp-facts-strip__label"><?php esc_html_e('krav på erfarenhet','tandstiftet'); ?></span>
		</div>
		<div class="pp-facts-strip__divider"></div>
		<div class="pp-facts-strip__cta">
			<a href="#boka" class="btn btn--primary"><?php esc_html_e('Hör av dig innan du kommer','tandstiftet'); ?> →</a>
			<a href="#vad-hander" class="btn btn--outline"><?php esc_html_e('Vad händer på dagen?','tandstiftet'); ?></a>
		</div>
	</div>
</div>

<!-- ═══ NÄR KAN MAN KOMMA ═══ -->
<section class="section" id="traningstider">
	<div class="container container--page">
		<div class="section-header">
			<div class="label"><?php esc_html_e('Enkelt att komma igång','tandstiftet'); ?></div>
			<h2 class="section-title"><?php esc_html_e('Kom till en vanlig träning','tandstiftet'); ?></h2>
		</div>
		<p class="pp-intro-text"><?php esc_html_e('Prova På för barn & ungdom sker under vår ordinarie barnträning — det är alltså inget separat, isolerat tillfälle. Du kommer helt enkelt en av träningsdagarna, blir en del av gruppen och får undervisning av samma instruktörer som tränar våra aktiva förare.','tandstiftet'); ?></p>
		<div class="pp-schedule-grid">
			<div class="pp-next-card">
				<div class="pp-next-card__icon">🗓️</div>
				<h3 class="pp-next-card__title"><?php esc_html_e('Onsdagar kl 17:00','tandstiftet'); ?></h3>
				<p class="pp-next-card__desc"><?php esc_html_e('Ordinarie barnträning. Kom vid starttid — det är då vi börjar byta om, värma upp motorcyklar och vattna banan.','tandstiftet'); ?></p>
			</div>
			<div class="pp-next-card">
				<div class="pp-next-card__icon">🗓️</div>
				<h3 class="pp-next-card__title"><?php esc_html_e('Lördagar kl 10:00','tandstiftet'); ?></h3>
				<p class="pp-next-card__desc"><?php esc_html_e('Ordinarie barnträning. Kom vid starttid — det är då vi börjar byta om, värma upp motorcyklar och vattna banan.','tandstiftet'); ?></p>
			</div>
		</div>
		<?php if ( tmc_provapa_season_open() ) : ?>
		<div class="pp-safety-note">
			<div class="pp-safety-note__title"><?php esc_html_e('Ingen bokning krävs','tandstiftet'); ?></div>
			<p><?php esc_html_e('Det räcker att dyka upp — ni behöver inte anmäla er i förväg. Vi rekommenderar dock starkt att höra av sig till oss innan ni kommer, eftersom vi ibland tvingas ställa in träningen på grund av väder eller banans skick. Då slipper ni en bortkastad resa.','tandstiftet'); ?></p>
		</div>
		<?php else : ?>
		<div class="pp-safety-note">
			<div class="pp-safety-note__title"><?php esc_html_e('Just nu pausat','tandstiftet'); ?></div>
			<p><?php esc_html_e('Tiderna ovan gäller normalt under säsong. Se meddelandet högst upp på sidan för aktuell status.','tandstiftet'); ?></p>
		</div>
		<?php endif; ?>
	</div>
</section>

<!-- ═══ UTRUSTNING — visuell grid ═══ -->
<section class="section section--dark" id="utrustning">
	<div class="container container--page">
		<div class="section-header">
			<div class="label"><?php esc_html_e('Ingår — helt gratis','tandstiftet'); ?></div>
			<h2 class="section-title"><?php esc_html_e('Vi lånar ut allt','tandstiftet'); ?></h2>
		</div>
		<p class="pp-intro-text"><?php esc_html_e('Du behöver inte äga ett enda plagg eller ett enda däck. Kom i bekväma kläder — vi fixar resten.','tandstiftet'); ?></p>

		<?php
		// Default gear items — override via custom fields tmc_gear_N_icon, tmc_gear_N_name, tmc_gear_N_desc
		$gear_defaults = [
			1 => [ 'name' => 'Hjälm',                'desc' => 'Fullface-hjälm, Svemo-godkänd. Barnstorlekar XS–M. Desinficeras mellan varje körning.' ],
			2 => [ 'name' => 'Cross-jacka & byxor',  'desc' => 'Cross- eller racingkläder med inbyggda skydd. Storlekar för barn och ungdom.' ],
			3 => [ 'name' => 'Ryggskydd',             'desc' => 'CE-godkänt ryggskydd. Obligatoriskt för alla åldrar — ingår alltid i utrustningslånet.' ],
			4 => [ 'name' => 'Nackskydd',             'desc' => 'Obligatoriskt skydd mot vrickningar och stötskador. Ingår i utrustningslånet.' ],
			5 => [ 'name' => 'Armbågsskydd',          'desc' => 'Separata CE-godkända armbågsskydd. Ingår i utrustningslånet.' ],
			6 => [ 'name' => 'Stövlar',               'desc' => 'Motorcykel- eller crossstövlar med ankelstöd. Barnstorlekar. Meddela skostorlek vid bokning.' ],
			7 => [ 'name' => 'Handskar',              'desc' => 'Motorcykelhandskar i barnstorlekar. Har du egna handskar är de självklart välkomna.' ],
			8 => [ 'name' => 'Speedwaymaskin',        'desc' => 'Anpassad barn- och ungdomsspeedwaymaskin, 50–80cc beroende på ålder och storlek — byggd speciellt för yngre förare.' ],
		];
		$gear_count = (int) get_post_meta( $page_id, 'tmc_gear_count', true ) ?: count( $gear_defaults );
		?>

		<div class="pp-gear-grid">
			<?php for ( $i = 1; $i <= $gear_count; $i++ ) :
				$icon_url = get_post_meta( $page_id, "tmc_gear_{$i}_icon", true );
				$name     = get_post_meta( $page_id, "tmc_gear_{$i}_name", true ) ?: ( $gear_defaults[$i]['name'] ?? '' );
				$desc     = get_post_meta( $page_id, "tmc_gear_{$i}_desc", true ) ?: ( $gear_defaults[$i]['desc'] ?? '' );
				if ( ! $name ) continue;
			?>
			<div class="pp-gear-item">
				<div class="pp-gear-icon">
					<?php if ( $icon_url ) : ?>
					<img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $name ); ?>" width="80" height="80" style="object-fit:contain;display:block;margin:0 auto 0.5rem;">
					<?php else : ?>
						<span style="display:block;width:48px;height:48px;background:#1e3352;border:2px solid #d4a017;margin:0 auto;border-radius:4px;"></span>
					<?php endif; ?>
				</div>
				<div class="pp-gear-name"><?php echo esc_html( $name ); ?></div>
				<div class="pp-gear-desc"><?php echo esc_html( $desc ); ?></div>
			</div>
			<?php endfor; ?>
		</div>

		<div class="pp-gear-bring">
			<div class="pp-gear-bring__title"><?php esc_html_e('Instruktör ingår alltid','tandstiftet'); ?></div>
			<div class="pp-gear-bring__items">
				<span><?php esc_html_e('Personlig instruktör från genomgång till sista varvet','tandstiftet'); ?></span>
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
					<h3 class="pp-step__title"><?php esc_html_e('Ankomst — vid starttid','tandstiftet'); ?></h3>
					<p class="pp-step__desc"><?php esc_html_e('Kom till ordinarie barnträning onsdag kl 17:00 eller lördag kl 10:00, tillsammans med en vårdnadshavare. Ingen anledning att komma tidigare — det är då vi börjar byta om, värma upp motorcyklar och vattna banan.','tandstiftet'); ?></p>
				</div>
			</div>
			<div class="pp-step">
				<div class="pp-step__num">2</div>
				<div class="pp-step__body">
					<h3 class="pp-step__title"><?php esc_html_e('Licens & utrustning','tandstiftet'); ?></h3>
					<p class="pp-step__desc"><?php esc_html_e('Vi hjälper till med licens vid behov och provar ut utrustning i rätt storlek innan träningen sätter igång.','tandstiftet'); ?></p>
				</div>
			</div>
			<div class="pp-step">
				<div class="pp-step__num">3</div>
				<div class="pp-step__body">
					<h3 class="pp-step__title"><?php esc_html_e('Delta i träningen','tandstiftet'); ?></h3>
					<p class="pp-step__desc"><?php esc_html_e('Du blir en del av den pågående barnträningen och får undervisning av våra instruktörer — säkerhet, banregler och grundteknik. Vi garanterar att du får den information om säkerhet som behövs för en trygg körning.','tandstiftet'); ?></p>
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
					<p class="pp-step__desc"><?php esc_html_e('Vi pratar igenom hur det gick tillsammans med vårdnadshavare, svarar på frågor och berättar om nästa steg om man vill fortsätta.','tandstiftet'); ?></p>
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
				<div class="pp-license-card__age"><?php esc_html_e('Under 12 år','tandstiftet'); ?></div>
				<div class="pp-license-card__title"><?php esc_html_e('Ingen licens krävs','tandstiftet'); ?></div>
				<p class="pp-license-card__desc"><?php esc_html_e('Barn upp till och med det år de fyller 11 år behöver ingen licens. Välkommen direkt.','tandstiftet'); ?></p>
			</div>
			<div class="pp-license-card">
				<div class="pp-license-card__age"><?php esc_html_e('Från 12 år','tandstiftet'); ?></div>
				<div class="pp-license-card__price">225 kr</div>
				<div class="pp-license-card__title"><?php esc_html_e('Prova På-licens','tandstiftet'); ?></div>
				<p class="pp-license-card__desc"><?php esc_html_e('Köps på provapasvemo.se — en avgift till Svemo, inte till klubben. Gäller bara angiven dag och bana. Lös den på morgonen — inte i förväg ifall vi ställer in.','tandstiftet'); ?></p>
				<a href="https://www.provapasvemo.se" target="_blank" rel="noopener" class="btn btn--outline btn--sm" style="margin-top:1rem"><?php esc_html_e('Köp licens →','tandstiftet'); ?></a>
			</div>
		</div>
		<div class="pp-license-note">
			<?php esc_html_e('Själva Prova På-tillfället kostar ingenting — klubben tar inte betalt av barn & ungdomar. Den enda eventuella kostnaden är Svemos licensavgift för den som är 12 år eller äldre.','tandstiftet'); ?>
		</div>
		<div class="pp-safety-note">
			<div class="pp-safety-note__title"><?php esc_html_e('Viktigt att veta','tandstiftet'); ?></div>
			<p><?php esc_html_e('Vi kan erbjuda Prova På helt kostnadsfritt eftersom vi vill att fler ska hitta hit — men det förutsätter att vi kan garantera tryggheten för alla på banan. Alla får den information om säkerhet som behövs för en trygg körning, och våra instruktörer gör allt för att förebygga risksituationer. Om ett barn eller en ungdom trots det inte kan följa instruktionerna eller utsätter andra för onödig risk, behöver vi kunna avbryta det pågående passet — det handlar om att skydda alla som är på banan samtidigt.','tandstiftet'); ?></p>
		</div>
	</div>
</section>

<!-- ═══ VILL DU FORTSÄTTA / FLER TILLFÄLLEN ═══ -->
<section class="section" id="fler-tillfallen">
	<div class="container container--page">
		<?php if ( tmc_provapa_season_open() ) : ?>
		<div class="section-header">
			<div class="label"><?php esc_html_e('Fastnade du?','tandstiftet'); ?></div>
			<h2 class="section-title"><?php esc_html_e('Ett gratistillfälle — fler går att ordna','tandstiftet'); ?></h2>
		</div>
		<p class="pp-intro-text">
			<?php esc_html_e('Vårt mål är att fler barn och ungdomar ska hitta till speedwayn, så vi vill helst att alla ska få chansen att känna efter ordentligt. Första tillfället är alltid gratis. Räcker inte en gång för att veta om det är din grej? Hör av dig till oss — vi ordnar gärna fler tillfällen när det behövs, till exempel genom att komma till en annan onsdag eller lördag. Det avgör vi tillsammans från fall till fall.','tandstiftet'); ?>
		</p>
		<?php else : ?>
		<div class="section-header">
			<div class="label"><?php esc_html_e('Nyfiken ändå?','tandstiftet'); ?></div>
			<h2 class="section-title"><?php esc_html_e('Häng med till nästa säsong','tandstiftet'); ?></h2>
		</div>
		<p class="pp-intro-text">
			<?php esc_html_e('Prova På för barn & ungdom är just nu pausat för säsongen (se meddelandet högst upp på sidan). Vill ni ändå fråga något, eller vara bland de första som får veta när det öppnar igen? Kontakta oss gärna — se kontaktuppgifter i sidfoten.','tandstiftet'); ?>
		</p>
		<?php endif; ?>
	</div>
</section>

<!-- ═══ NÄSTA STEG ═══ -->
<section class="section section--dark" id="nasta-steg">
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
				<h3 class="pp-next-card__title"><?php esc_html_e('Trackracing-skolan','tandstiftet'); ?></h3>
				<p class="pp-next-card__desc"><?php esc_html_e('7 träffar för 860 kr. För ungdomar som vill lära sig grunderna ordentligt med stegvis progression.','tandstiftet'); ?></p>
				<a href="/klubbinfo/#bli-medlem" class="pp-next-card__link"><?php esc_html_e('Läs mer om skolan','tandstiftet'); ?> →</a>
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
			<?php if ( tmc_provapa_season_open() ) : ?>
			<div class="label" style="margin-bottom:0.5rem"><?php esc_html_e('Redo?','tandstiftet'); ?></div>
			<h2 class="pp-book-section__title"><?php esc_html_e('Hör av dig innan du kommer','tandstiftet'); ?></h2>
			<p class="pp-book-section__desc"><?php esc_html_e('Det räcker att dyka upp på ordinarie barnträning — onsdagar kl 17:00 eller lördagar kl 10:00. Men vi rekommenderar starkt att ni hör av er innan, eftersom vi ibland tvingas ställa in träningen på grund av väder eller banans skick. Fyll i nedan eller kontakta oss direkt.','tandstiftet'); ?></p>
			<div class="pp-book-needs">
				<div class="pp-book-needs__title"><?php esc_html_e('Bra att veta om ni hör av er:','tandstiftet'); ?></div>
				<p>◆ &nbsp;<?php esc_html_e('Namn och ålder','tandstiftet'); ?></p>
				<p>◆ &nbsp;<?php esc_html_e('Skostorlek (för stövlar)','tandstiftet'); ?></p>
				<p>◆ &nbsp;<?php esc_html_e('Ungefärlig längd (för overall)','tandstiftet'); ?></p>
				<p>◆ &nbsp;<?php esc_html_e('Vilket träningstillfälle ni planerar komma till','tandstiftet'); ?></p>
			</div>
			<?php else : ?>
			<div class="label" style="margin-bottom:0.5rem"><?php esc_html_e('Säsongen är slut','tandstiftet'); ?></div>
			<h2 class="pp-book-section__title"><?php esc_html_e('Vi ses nästa säsong','tandstiftet'); ?></h2>
			<p class="pp-book-section__desc"><?php echo nl2br( esc_html( tmc_provapa_season_message() ) ); ?></p>
			<?php endif; ?>
		</div>
		<div class="pp-book-section__form">
			<?php if ( tmc_provapa_season_open() ) : ?>
			<?php
			// Handle form
			$success = false;
			$mail_error = false;
			$spam_error = false;
			if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pp_nonce']) && wp_verify_nonce($_POST['pp_nonce'], 'pp_booking') && ! tmc_antispam_is_bot() ) {
				$name    = sanitize_text_field($_POST['pp_name'] ?? '');
				$email   = sanitize_email($_POST['pp_email'] ?? '');
				$phone   = sanitize_text_field($_POST['pp_phone'] ?? '');
				$age     = sanitize_text_field($_POST['pp_age'] ?? '');
				$shoe    = sanitize_text_field($_POST['pp_shoe'] ?? '');
				$height  = sanitize_text_field($_POST['pp_height'] ?? '');
				$slot    = sanitize_text_field($_POST['pp_slot'] ?? '');
				$again   = sanitize_text_field($_POST['pp_again'] ?? '');
				$msg     = sanitize_textarea_field($_POST['pp_message'] ?? '');

				if ( ! tmc_antispam_valid_age( $age, 1, 18 ) || tmc_antispam_text_is_spammy( $msg ) || tmc_antispam_text_is_spammy( $name, 0 ) ) {
					$spam_error = true;
				} else {
				$body = "PROVA PÅ — FÖRHANDSKONTAKT (Barn & Ungdom, gratis)\n\n";
				$body .= "Namn: $name\nE-post: $email\nTelefon: $phone\n";
				$body .= "Ålder: $age\nSkostorlek: $shoe\nLängd: $height\n";
				$body .= "Planerar komma till: $slot\n";
				$body .= "Har provat på tidigare: $again\n\nÖvrigt:\n$msg";

				$mail_sent = wp_mail(
					get_option('admin_email'),
					sprintf('[%s] Prova På — hör av sig innan (Barn & Ungdom): %s', get_bloginfo('name'), $name),
					$body,
					["Reply-To: $email"]
				);

				if ( $mail_sent ) {
					$success = true;
				} else {
					// Common cause: host blocks PHP mail() / no SMTP configured in WordPress.
					error_log('Tändstiftet: wp_mail() misslyckades för Prova På-formulär (Barn & Ungdom), avsändare: ' . $email);
					$mail_error = true;
				}
				}
			}
			?>
			<?php if ($success) : ?>
				<div class="pp-form-success">
					<div style="font-size:2rem;margin-bottom:0.5rem">✓</div>
					<h3><?php esc_html_e('Tack, vi hör av oss!','tandstiftet'); ?></h3>
					<p><?php esc_html_e('Vi bekräftar att träningen är igång eller hör av oss om något ändras. Kom ihåg att lösa licens på provapasvemo.se om du är 12+.','tandstiftet'); ?></p>
				</div>
			<?php elseif ($spam_error) : ?>
				<div class="pp-form-error">
					<div style="font-size:2rem;margin-bottom:0.5rem">⚠</div>
					<h3><?php esc_html_e('Kunde inte tolka uppgifterna','tandstiftet'); ?></h3>
					<p><?php esc_html_e('Kontrollera att åldern är ett rimligt tal och att meddelandet inte innehåller länkar, och skicka igen.','tandstiftet'); ?></p>
				</div>
			<?php elseif ($mail_error) : ?>
				<div class="pp-form-error">
					<div style="font-size:2rem;margin-bottom:0.5rem">⚠</div>
					<h3><?php esc_html_e('Något gick fel','tandstiftet'); ?></h3>
					<p><?php esc_html_e('Meddelandet kunde inte skickas just nu. Testa gärna igen om en stund, eller kontakta oss direkt nedan.','tandstiftet'); ?></p>
				</div>
			<?php else : ?>
			<form method="post" class="pp-form" action="#boka">
				<?php wp_nonce_field('pp_booking','pp_nonce'); ?>
				<?php tmc_antispam_fields(); ?>
				<div class="pp-form-row">
					<div class="tmc-form-field">
						<label><?php esc_html_e('Namn *','tandstiftet'); ?></label>
						<input type="text" name="pp_name" required autocomplete="name" value="<?php echo esc_attr($name ?? ''); ?>">
					</div>
					<div class="tmc-form-field">
						<label><?php esc_html_e('Ålder *','tandstiftet'); ?></label>
						<input type="text" name="pp_age" required value="<?php echo esc_attr($age ?? ''); ?>">
					</div>
				</div>
				<div class="pp-form-row">
					<div class="tmc-form-field">
						<label><?php esc_html_e('Vårdnadshavarens e-post *','tandstiftet'); ?></label>
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
						<input type="text" name="pp_shoe" placeholder="t.ex. 36" value="<?php echo esc_attr($shoe ?? ''); ?>">
					</div>
					<div class="tmc-form-field">
						<label><?php esc_html_e('Längd (cm)','tandstiftet'); ?></label>
						<input type="text" name="pp_height" placeholder="t.ex. 140" value="<?php echo esc_attr($height ?? ''); ?>">
					</div>
				</div>
				<div class="pp-form-row">
					<div class="tmc-form-field">
						<label><?php esc_html_e('Vilket träningstillfälle?','tandstiftet'); ?></label>
						<select name="pp_slot">
							<?php $sv = $slot ?? 'Onsdag 17:00'; ?>
							<option value="Onsdag 17:00" <?php selected($sv, 'Onsdag 17:00'); ?>><?php esc_html_e('Onsdag kl 17:00','tandstiftet'); ?></option>
							<option value="Lördag 10:00" <?php selected($sv, 'Lördag 10:00'); ?>><?php esc_html_e('Lördag kl 10:00','tandstiftet'); ?></option>
							<option value="Vet ej ännu" <?php selected($sv, 'Vet ej ännu'); ?>><?php esc_html_e('Vet ej ännu — hör av oss','tandstiftet'); ?></option>
						</select>
					</div>
					<div class="tmc-form-field">
						<label><?php esc_html_e('Har provat på hos oss förut?','tandstiftet'); ?></label>
						<select name="pp_again">
							<?php $av = $again ?? 'Nej, första gången'; ?>
							<option value="Nej, första gången" <?php selected($av, 'Nej, första gången'); ?>><?php esc_html_e('Nej, första gången','tandstiftet'); ?></option>
							<option value="Ja, vill prova igen" <?php selected($av, 'Ja, vill prova igen'); ?>><?php esc_html_e('Ja, vill prova igen','tandstiftet'); ?></option>
						</select>
					</div>
				</div>
				<div class="tmc-form-field">
					<label><?php esc_html_e('Övrigt (frågor, antal personer etc.)','tandstiftet'); ?></label>
					<textarea name="pp_message" rows="3"><?php echo esc_textarea($msg ?? ''); ?></textarea>
				</div>
				<button type="submit" class="btn btn--primary" style="width:100%;justify-content:center"><?php esc_html_e('Skicka','tandstiftet'); ?></button>
			</form>
			<?php endif; ?>
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

<?php get_footer(); ?>
