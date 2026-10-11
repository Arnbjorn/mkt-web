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

<?php
$booking_anchor = 'boka';
include get_theme_file_path( 'template-parts/sliding-booking.php' );   // shared with the combined page
?>

<?php get_footer(); ?>
