<?php
/**
 * Template Name: Lång informationssida (ankar-sektioner)
 * Description: En lång sida med flera namngivna sektioner och sticky sidonavigering.
 *              Lägg till sektioner via anpassade fält: tmc_sections (se nedan).
 *              Fungerar för Klubbinfo, Sektioner, eller vilken samlad informationssida som helst.
 *
 * Anpassade fält per sektion (repeater via tmc_section_N_*):
 *   tmc_section_1_title   — sektionens rubrik (används som ankartext i menyn)
 *   tmc_section_1_slug    — ankar-id (t.ex. "banan", "historia") — inga mellanslag
 *   tmc_section_1_content — fritext / HTML-innehåll för sektionen
 *   tmc_section_1_image   — valfri bild-ID (visas som bred banner ovanför texten)
 *   tmc_section_count     — antal sektioner (heltal, t.ex. 5)
 *
 * @package tandstiftet
 */
get_header();

$page_id    = get_the_ID();
$page_title = get_the_title();
$page_intro = get_post_meta( $page_id, 'tmc_page_intro', true );
$count      = (int) get_post_meta( $page_id, 'tmc_section_count', true );

// Build sections array
$sections = [];
for ( $i = 1; $i <= $count; $i++ ) {
	$title   = get_post_meta( $page_id, "tmc_section_{$i}_title",   true );
	$slug    = get_post_meta( $page_id, "tmc_section_{$i}_slug",    true );
	$content = get_post_meta( $page_id, "tmc_section_{$i}_content", true );
	$img_id  = get_post_meta( $page_id, "tmc_section_{$i}_image",   true );
	if ( $title ) {
		$sections[] = compact( 'title', 'slug', 'content', 'img_id' );
	}
}

tmc_page_header( $page_title, $page_intro );
?>

<div class="longpage-wrap">

	<!-- ── Sticky side nav (desktop) ── -->
	<?php if ( $sections ) : ?>
	<nav class="longpage-sidenav" id="longpage-sidenav" aria-label="<?php esc_attr_e( 'Sidinnehåll', 'tandstiftet' ); ?>">
		<div class="longpage-sidenav__inner">
			<p class="longpage-sidenav__heading label"><?php esc_html_e( 'På denna sida', 'tandstiftet' ); ?></p>
			<ul>
				<?php foreach ( $sections as $s ) : ?>
					<li>
						<a href="#<?php echo esc_attr( $s['slug'] ); ?>"
						   class="longpage-sidenav__link"
						   data-target="<?php echo esc_attr( $s['slug'] ); ?>">
							<?php echo esc_html( $s['title'] ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</nav>
	<?php endif; ?>

	<!-- ── Main content ── -->
	<main class="longpage-main">

		<?php if ( $sections ) : ?>

			<?php foreach ( $sections as $idx => $s ) :
				$alt_bg = $idx % 2 === 1 ? 'style="background:var(--color-coal)"' : '';
			?>
			<section class="longpage-section"
			         id="<?php echo esc_attr( $s['slug'] ); ?>"
			         <?php echo $alt_bg; ?>>

				<?php if ( $s['img_id'] ) :
					$img_url = wp_get_attachment_image_url( $s['img_id'], 'hero-bg' );
				?>
				<div class="longpage-section__banner"
				     style="background-image:url('<?php echo esc_url( $img_url ); ?>')">
				</div>
				<?php endif; ?>

				<div class="longpage-section__inner">
					<h2 class="longpage-section__title"><?php echo esc_html( $s['title'] ); ?></h2>
					<div class="info-content longpage-section__content">
						<?php echo wp_kses_post( wpautop( $s['content'] ) ); ?>
					</div>
				</div>

			</section>
			<?php endforeach; ?>

		<?php else :
			// Fallback: render the_content() if no sections defined yet
			while ( have_posts() ) : the_post(); ?>
			<section class="longpage-section">
				<div class="longpage-section__inner">
					<div class="info-content">
						<?php the_content(); ?>
					</div>
					<div style="margin-top:var(--space-xl);padding:var(--space-xl);background:var(--color-asphalt);border:1px solid var(--color-mid);border-left:3px solid var(--color-gold)">
						<p class="label" style="margin-bottom:var(--space-sm)"><?php esc_html_e( 'Så lägger du till sektioner', 'tandstiftet' ); ?></p>
						<p style="font-size:0.85rem;color:var(--color-grey-light);line-height:1.7">
							<?php esc_html_e( 'Redigera denna sida i wp-admin och scrolla ned till metafältsrutan "Informationssektioner". Ange antal sektioner i fältet tmc_section_count, sedan tmc_section_1_title, tmc_section_1_slug, tmc_section_1_content osv.', 'tandstiftet' ); ?>
						</p>
					</div>
				</div>
			</section>
		<?php endwhile; endif; ?>

	</main>

</div><!-- /.longpage-wrap -->

<?php get_footer(); ?>
