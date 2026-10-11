<?php
/**
 * Template Name: Prova På (Barn & Ungdom)
 * Description: Prova På-sida för barn & ungdomar — gratis, på ordinarie barnträning.
 *
 * v2-layout enligt design/concepts/koncept-provapa.html med hero "D"
 * (affischrubrik + foto i ram med klistermärke). Alla texter ändras i
 * Utseende → Anpassa → Sida: Prova På (tmc_provapa_fields() i functions.php).
 * Fotot i ramen är sidans Utvald bild.
 *
 * @package tandstiftet
 */
get_header();

$page_id      = get_the_ID();
$signup       = tmc_signup_state( 'provapa' );   // Anpassa → Sida: Prova På → Anmälan och säsong
$season_open  = $signup['open'];
$show         = function ( $part ) { return tmc_page_section_visible( 'provapa', $part ); };
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
		<p class="provapa-closed__text"><?php echo nl2br( esc_html( $signup['message'] ) ); ?></p>
	</div>
</div>
<?php endif; ?>

<?php $facts = $show( 'facts' ) ? tmc_provapa_items( 'fact_%d_', [ 'num', 'label' ], 6 ) : []; if ( $facts ) : ?>
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

<?php if ( $show( 'adult' ) && tmc_sliding_enabled() && $t( 'adult_link' ) ) : ?>
<!-- ═══ VUXEN-HÄNVISNING ═══ -->
<div class="provapa-adult">
	<div class="container provapa-adult__inner">
		<?php if ( $t( 'adult_text' ) ) : ?><span><?php echo esc_html( $t( 'adult_text' ) ); ?></span><?php endif; ?>
		<a href="<?php echo esc_url( $sliding_url ); ?>"><?php echo esc_html( $t( 'adult_link' ) ); ?></a>
	</div>
</div>
<?php endif; ?>

<?php if ( $show( 'times' ) ) : ?>
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
<?php endif; // times ?>

<?php $steps = $show( 'steps' ) ? tmc_provapa_items( 'step_%d_', [ 'title', 'text' ], 4 ) : []; if ( $steps ) : ?>
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

<?php $faqs = $show( 'faq' ) ? tmc_provapa_items( 'faq_%d_', [ 'q', 'a' ], 6 ) : []; if ( $faqs ) : ?>
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

<?php
$signup_anchor = 'boka';
include get_theme_file_path( 'template-parts/provapa-signup.php' );   // shared with the combined page
?>

<?php get_footer(); ?>
