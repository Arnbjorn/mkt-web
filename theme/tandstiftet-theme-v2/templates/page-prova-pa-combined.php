<?php
/**
 * Prova På — barn OCH vuxna på samma sida (testläge).
 *
 * Inte en mall man väljer själv: används automatiskt i stället för page-prova-pa.php när
 * Anpassa → Sida: Prova På → Gemensam sida → "Visa barn och vuxna på samma sida" är ibockad
 * (tmc_combined_template i functions.php). Bocka ur → de två separata sidorna igen.
 *
 * Texter: delarna "Gemensam …" i samma panel, plus Prova Pås och Slidings egna texter
 * (tider, formulär, pris, grupper, bokning, frågor) — inget behöver skrivas två gånger.
 * Skiss: design/preview-provapa-kombo/skiss-A.html.
 *
 * @package tandstiftet
 */
get_header();

$page_id       = get_the_ID();
$p             = 'tmc_provapa_text';
$sl            = 'tmc_sliding_text';
$show          = function ( $part ) { return tmc_page_section_visible( 'provapa', $part ); };
$sl_show       = function ( $part ) { return tmc_page_section_visible( 'sliding', $part ); };
$pp_signup     = tmc_signup_state( 'provapa' );
$sl_signup     = tmc_signup_state( 'sliding' );
$adult_on      = tmc_sliding_enabled();
$ft_on         = tmc_flattrack_enabled();
$contact_email = get_theme_mod( 'tmc_contact_email' );
$contact_phone = get_theme_mod( 'tmc_contact_phone' );

/* A comparison cell: with several lines the first one is shown large (e.g. "0 kr"), the rest as normal text. */
$cell = function ( $text ) {
	$lines = tmc_provapa_lines( $text );
	if ( ! $lines ) return '';
	$first = array_shift( $lines );
	$out   = $lines ? '<strong>' . esc_html( $first ) . '</strong>' : esc_html( $first );
	foreach ( $lines as $l ) $out .= '<br>' . esc_html( $l );
	return $out;
};
?>

<!-- ═══ HERO — gemensam, med två val ═══ -->
<section class="provapa-hero cmb-hero">
	<div class="provapa-hero__speckle" aria-hidden="true"></div>
	<div class="container provapa-hero__inner">
		<div class="provapa-hero__text">
			<?php if ( $p( 'cmb_eyebrow' ) ) : ?><p class="provapa-hero__eyebrow"><?php echo esc_html( $p( 'cmb_eyebrow' ) ); ?></p><?php endif; ?>
			<h1 class="provapa-hero__title">
				<?php if ( $p( 'hero_title_1' ) ) : ?><span class="provapa-hero__line provapa-hero__line--gold"><?php echo esc_html( $p( 'hero_title_1' ) ); ?></span><?php endif; ?>
				<?php if ( $p( 'hero_title_2' ) ) : ?><span class="provapa-hero__line"><?php echo esc_html( $p( 'hero_title_2' ) ); ?><svg class="provapa-hero__stroke" viewBox="0 0 400 40" preserveAspectRatio="none" aria-hidden="true" focusable="false"><defs><filter id="provapa-rough" x="-5%" y="-40%" width="110%" height="180%"><feTurbulence type="fractalNoise" baseFrequency="0.035 0.25" numOctaves="2" seed="4" result="n"/><feDisplacementMap in="SourceGraphic" in2="n" scale="9"/></filter></defs><path filter="url(#provapa-rough)" d="M4 24 C70 10 150 30 230 17 S360 8 396 21 L392 31 C330 37 262 26 192 34 S62 40 6 33 Z"/></svg></span><?php endif; ?>
			</h1>
			<?php if ( $p( 'cmb_sub' ) ) : ?><p class="provapa-hero__sub"><?php echo esc_html( $p( 'cmb_sub' ) ); ?></p><?php endif; ?>
			<div class="cmb-choose">
				<?php if ( $show( 'cmb_kids' ) ) : ?>
				<a href="#barn" class="cmb-choose__card">
					<span class="cmb-choose__who"><?php echo esc_html( $p( 'cmb_kids_who' ) ); ?></span>
					<?php if ( $p( 'cmb_kids_what' ) ) : ?><span class="cmb-choose__what"><?php echo esc_html( $p( 'cmb_kids_what' ) ); ?></span><?php endif; ?>
					<?php if ( $p( 'cmb_kids_more' ) ) : ?><span class="cmb-choose__more"><?php echo esc_html( $p( 'cmb_kids_more' ) ); ?></span><?php endif; ?>
					<?php if ( $p( 'cmb_kids_go' ) ) : ?><span class="cmb-choose__go"><?php echo esc_html( $p( 'cmb_kids_go' ) ); ?></span><?php endif; ?>
				</a>
				<?php endif; ?>
				<?php if ( $show( 'cmb_adult' ) ) : ?>
				<a href="#vuxen" class="cmb-choose__card cmb-choose__card--adult">
					<span class="cmb-choose__who"><?php echo esc_html( $p( 'cmb_adult_who' ) ); ?></span>
					<span class="cmb-choose__what"><?php echo esc_html( $adult_on ? $p( 'cmb_adult_what' ) : $p( 'cmb_adult_paused' ) ); ?></span>
					<?php if ( $adult_on && $p( 'cmb_adult_more' ) ) : ?><span class="cmb-choose__more"><?php echo esc_html( $p( 'cmb_adult_more' ) ); ?></span><?php endif; ?>
					<?php if ( $p( 'cmb_adult_go' ) ) : ?><span class="cmb-choose__go"><?php echo esc_html( $p( 'cmb_adult_go' ) ); ?></span><?php endif; ?>
				</a>
				<?php endif; ?>
			</div>
		</div>
		<?php if ( has_post_thumbnail( $page_id ) ) : ?>
			<div class="provapa-hero__visual">
				<figure class="provapa-polaroid">
					<span class="provapa-polaroid__tape" aria-hidden="true"></span>
					<?php echo get_the_post_thumbnail( $page_id, 'large', [ 'class' => 'provapa-polaroid__img' ] ); ?>
					<?php if ( $p( 'hero_photo_caption' ) ) : ?><figcaption class="provapa-polaroid__cap"><?php echo esc_html( $p( 'hero_photo_caption' ) ); ?></figcaption><?php endif; ?>
				</figure>
			</div>
		<?php elseif ( $p( 'cmb_sticker_big' ) || $p( 'cmb_sticker_small' ) ) : /* no photo → the sticker (default) */ ?>
			<div class="provapa-hero__visual provapa-hero__visual--no-photo">
				<span class="provapa-hero__ring" aria-hidden="true"></span>
				<div class="provapa-sticker"><div>
					<?php if ( $p( 'cmb_sticker_big' ) ) : ?><span class="provapa-sticker__big"><?php echo esc_html( $p( 'cmb_sticker_big' ) ); ?></span><?php endif; ?>
					<?php if ( $p( 'cmb_sticker_small' ) ) : ?><span class="provapa-sticker__small"><?php echo esc_html( $p( 'cmb_sticker_small' ) ); ?></span><?php endif; ?>
				</div></div>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php $rows = $show( 'cmb_compare' ) ? tmc_provapa_items( 'cmp_%d_', [ 'label', 'kids', 'adult' ], 5 ) : []; if ( $rows ) : ?>
<!-- ═══ JÄMFÖRELSE (beige) ═══ -->
<section class="section section--beige cmb-compare">
	<div class="container">
		<?php if ( $p( 'cmp_label' ) ) : ?><p class="label"><?php echo esc_html( $p( 'cmp_label' ) ); ?></p><?php endif; ?>
		<?php if ( $p( 'cmp_title' ) ) : ?><h2 class="provapa-title"><?php echo esc_html( $p( 'cmp_title' ) ); ?></h2><?php endif; ?>
		<table class="cmb-compare__table">
			<thead><tr>
				<td></td>
				<th scope="col" class="cmb-compare__kids"><?php echo esc_html( $p( 'cmp_head_kids' ) ); ?></th>
				<?php if ( $adult_on ) : ?><th scope="col" class="cmb-compare__adult"><?php echo esc_html( $p( 'cmp_head_adult' ) ); ?></th><?php endif; ?>
			</tr></thead>
			<tbody>
				<?php foreach ( $rows as $r ) : ?>
					<tr>
						<th scope="row"><?php echo esc_html( $r['label'] ); ?></th>
						<td data-who="<?php echo esc_attr( $p( 'cmp_head_kids' ) ); ?>"><?php echo $cell( $r['kids'] ); // escaped in $cell ?></td>
						<?php if ( $adult_on ) : ?><td data-who="<?php echo esc_attr( $p( 'cmp_head_adult' ) ); ?>" class="cmb-compare__adult-cell"><?php echo $cell( $r['adult'] ); // escaped in $cell ?></td><?php endif; ?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</section>
<?php endif; ?>

<?php $steps = $show( 'cmb_steps' ) ? tmc_provapa_items( 'cmb_step_%d_', [ 'title', 'text' ], 4 ) : []; if ( $steps ) : ?>
<!-- ═══ SÅ GÅR DET TILL — gäller alla (mörk) ═══ -->
<section class="section provapa-steps" id="vad-hander">
	<div class="container">
		<div class="provapa-steps__head">
			<?php if ( $p( 'cmb_steps_eyebrow' ) ) : ?><p class="provapa-eyebrow"><?php echo esc_html( $p( 'cmb_steps_eyebrow' ) ); ?></p><?php endif; ?>
			<?php if ( $p( 'cmb_steps_title' ) ) : ?><h2 class="provapa-title"><?php echo esc_html( $p( 'cmb_steps_title' ) ); ?></h2><?php endif; ?>
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

<?php if ( $show( 'cmb_kids' ) ) :
	$slots = tmc_provapa_slots(); ?>
<!-- ═══ BARN & UNGDOM (beige, guld) ═══ -->
<section class="section section--beige cmb-part cmb-part--kids" id="barn">
	<div class="container">
		<div class="cmb-part__head">
			<?php if ( $p( 'cmb_kids_badge' ) ) : ?><span class="cmb-part__badge"><?php echo esc_html( $p( 'cmb_kids_badge' ) ); ?></span><?php endif; ?>
			<?php if ( $p( 'cmb_kids_title' ) ) : ?><h2 class="provapa-title"><?php echo esc_html( $p( 'cmb_kids_title' ) ); ?></h2><?php endif; ?>
		</div>
		<div class="provapa-read__grid">
			<div class="provapa-read__text">
				<?php $lead = $pp_signup['open'] ? $p( 'times_text' ) : $p( 'closed_times_text' ); if ( $lead ) : ?><p class="provapa-read__lead"><?php echo esc_html( $lead ); ?></p><?php endif; ?>
				<?php if ( $p( 'gear_text' ) ) : ?><div class="provapa-note provapa-note--orange"><?php if ( $p( 'gear_title' ) ) : ?><strong><?php echo esc_html( $p( 'gear_title' ) ); ?></strong> <?php endif; ?><?php echo esc_html( $p( 'gear_text' ) ); ?></div><?php endif; ?>
				<?php if ( $p( 'license_text' ) ) : ?>
					<div class="provapa-note"><?php if ( $p( 'license_title' ) ) : ?><strong><?php echo esc_html( $p( 'license_title' ) ); ?></strong> <?php endif; ?><?php echo esc_html( $p( 'license_text' ) ); ?>
						<?php if ( $p( 'license_link' ) && $p( 'license_url' ) ) : ?><a class="provapa-note__link" href="<?php echo esc_url( $p( 'license_url' ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $p( 'license_link' ) ); ?></a><?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
			<?php if ( $slots ) : ?>
			<div class="provapa-schedule">
				<?php foreach ( $slots as $card ) : ?>
					<div class="provapa-schedule__card"><span class="provapa-schedule__day"><?php echo esc_html( $card['day'] ); ?></span><?php if ( $card['time'] ) : ?><span class="provapa-schedule__time"><?php echo esc_html( $card['time'] ); ?></span><?php endif; ?></div>
				<?php endforeach; ?>
				<?php if ( $p( 'slot_note' ) ) : ?><p class="provapa-schedule__note"><?php echo esc_html( $p( 'slot_note' ) ); ?></p><?php endif; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php
	$t = $p; $signup = $pp_signup; $season_open = $pp_signup['open']; $signup_anchor = 'barn-boka';
	include get_theme_file_path( 'template-parts/provapa-signup.php' );
endif; ?>

<?php if ( $show( 'cmb_adult' ) ) : ?>
<!-- ═══ VUXEN (mörk, orange) ═══ -->
<section class="section cmb-part cmb-part--adult" id="vuxen">
	<div class="container">
		<div class="cmb-part__head">
			<?php if ( $p( 'cmb_adult_badge' ) ) : ?><span class="cmb-part__badge cmb-part__badge--adult"><?php echo esc_html( $p( 'cmb_adult_badge' ) ); ?></span><?php endif; ?>
			<h2 class="provapa-title"><?php echo esc_html( $adult_on ? $p( 'cmb_adult_title' ) : $sl( 'paused_title' ) ); ?></h2>
		</div>
		<?php if ( ! $adult_on ) : ?>
			<?php if ( $sl( 'paused_text' ) ) : ?><p class="cmb-part__lead"><?php echo esc_html( $sl( 'paused_text' ) ); ?></p><?php endif; ?>
			<div class="sliding-paused__actions">
				<?php if ( $contact_phone ) : ?><a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $contact_phone ) ); ?>" class="btn btn--outline provapa-btn--light"><?php echo esc_html( $contact_phone ); ?></a><?php endif; ?>
				<?php if ( $contact_email ) : ?><a href="mailto:<?php echo esc_attr( $contact_email ); ?>" class="btn btn--outline provapa-btn--light"><?php echo esc_html( $contact_email ); ?></a><?php endif; ?>
			</div>
		<?php else : ?>
			<?php if ( $p( 'cmb_adult_text' ) ) : ?><p class="cmb-part__lead"><?php echo esc_html( $p( 'cmb_adult_text' ) ); ?></p><?php endif; ?>
			<?php if ( $sl_show( 'price' ) && ( $sl( 'price_club' ) || $sl( 'price_license' ) ) ) : ?>
				<div class="sliding-price__boxes">
					<?php if ( $sl( 'price_club' ) ) : ?><div class="sliding-price__box"><span class="sliding-price__amount"><?php echo esc_html( $sl( 'price_club' ) ); ?></span><span class="sliding-price__what"><?php echo esc_html( $sl( 'price_club_text' ) ); ?></span></div><?php endif; ?>
					<?php if ( $sl( 'price_club' ) && $sl( 'price_license' ) ) : ?><span class="sliding-price__plus" aria-hidden="true">+</span><?php endif; ?>
					<?php if ( $sl( 'price_license' ) ) : ?><div class="sliding-price__box sliding-price__box--license"><span class="sliding-price__amount"><?php echo esc_html( $sl( 'price_license' ) ); ?></span><span class="sliding-price__what"><?php echo esc_html( $sl( 'price_license_text' ) ); ?></span></div><?php endif; ?>
				</div>
				<?php if ( $sl( 'price_note' ) ) : ?><p class="cmb-part__note"><?php echo esc_html( $sl( 'price_note' ) ); ?></p><?php endif; ?>
			<?php endif; ?>
			<?php $groups = $sl_show( 'groups' ) ? tmc_sliding_items( 'group_%d_', [ 'title', 'text' ], 3 ) : []; if ( $groups ) : ?>
				<div class="sliding-cards cmb-part__groups">
					<?php foreach ( $groups as $g ) : ?>
						<div class="sliding-card"><h3 class="sliding-card__title"><?php echo esc_html( $g['title'] ); ?></h3><?php if ( $g['text'] ) : ?><p class="sliding-card__text"><?php echo esc_html( $g['text'] ); ?></p><?php endif; ?></div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</section>
<?php if ( $adult_on ) {
	$t = $sl; $signup = $sl_signup; $booking_anchor = 'vuxen-boka';
	include get_theme_file_path( 'template-parts/sliding-booking.php' );
} ?>
<?php endif; ?>

<?php
$kids_faq  = $show( 'cmb_faq' ) && $show( 'faq' ) ? tmc_provapa_items( 'faq_%d_', [ 'q', 'a' ], 6 ) : [];
$adult_faq = $show( 'cmb_faq' ) && $adult_on && $sl_show( 'faq' ) ? tmc_sliding_items( 'faq_%d_', [ 'q', 'a' ], 6 ) : [];
if ( $kids_faq || $adult_faq ) : ?>
<!-- ═══ BRA ATT VETA — frågorna märkta Barn / Vuxen (beige) ═══ -->
<section class="section section--beige provapa-faq" id="bra-att-veta">
	<div class="container provapa-faq__inner">
		<?php if ( $p( 'cmb_faq_eyebrow' ) ) : ?><p class="label"><?php echo esc_html( $p( 'cmb_faq_eyebrow' ) ); ?></p><?php endif; ?>
		<?php if ( $p( 'cmb_faq_title' ) ) : ?><h2 class="provapa-title"><?php echo esc_html( $p( 'cmb_faq_title' ) ); ?></h2><?php endif; ?>
		<div class="provapa-faq__list">
			<?php foreach ( [ [ $kids_faq, $p( 'cmb_faq_kids_tag' ), '' ], [ $adult_faq, $p( 'cmb_faq_adult_tag' ), ' cmb-tag--adult' ] ] as list( $list, $tag, $mod ) ) : ?>
				<?php foreach ( $list as $faq ) : ?>
					<details class="provapa-faq__item">
						<summary class="provapa-faq__q"><span><?php if ( $tag ) : ?><span class="cmb-tag<?php echo esc_attr( $mod ); ?>"><?php echo esc_html( $tag ); ?></span><?php endif; ?><?php echo esc_html( $faq['q'] ); ?></span></summary>
						<div class="provapa-faq__a"><?php foreach ( tmc_provapa_lines( $faq['a'] ) as $para ) : ?><p><?php echo esc_html( $para ); ?></p><?php endforeach; ?></div>
					</details>
				<?php endforeach; ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
