<?php
/**
 * Template Name: Startsida
 * Template Post Type: page
 * @package tandstiftet
 */
get_header();

// Hero slides
$hero_slides = [];
for ( $i = 1; $i <= 5; $i++ ) {
	$img_id = get_theme_mod( 'tmc_hero_image_' . $i );
	if ( $img_id ) { $url = wp_get_attachment_image_url( $img_id, 'hero-bg' ); if ( $url ) $hero_slides[] = $url; }
}
if ( empty( $hero_slides ) ) {
	$legacy = get_theme_mod( 'tmc_hero_image' );
	$hero_slides[] = $legacy ? wp_get_attachment_image_url( $legacy, 'hero-bg' ) : TMC_URI . '/assets/images/hero-bg.jpg';
}
$has_slideshow = count( $hero_slides ) > 1;
$hero_torn     = (bool) get_theme_mod( 'tmc_hero_torn_edge', true );   // torn photo edge (Anpassa → Hero)
$hero_logo_id  = get_theme_mod( 'custom_logo' );

// Title: the last word is highlighted in gold (see tmc_hero_title_html in functions.php)
$hero_title_html = tmc_hero_title_html( get_theme_mod( 'tmc_hero_title', '' ) ?: get_bloginfo( 'name' ) );
?>

<?php tmc_render_notice_banner(); ?>

<!-- ═══ HERO ═══ -->
<section class="hero<?php echo $has_slideshow ? ' hero--slideshow' : ''; ?><?php echo $hero_torn ? ' hero--torn' : ''; ?>">
	<?php if ( $has_slideshow ) : ?>
		<div class="hero__slides" aria-hidden="true">
			<?php foreach ( $hero_slides as $i => $url ) : ?>
				<div class="hero__slide<?php echo $i===0?' is-active':''; ?>" style="background-image:url('<?php echo esc_url($url); ?>')"></div>
			<?php endforeach; ?>
		</div>
		<div class="hero__dots" aria-hidden="true">
			<?php foreach ( $hero_slides as $i => $url ) : ?>
				<button class="hero__dot<?php echo $i===0?' is-active':''; ?>" data-index="<?php echo $i; ?>" aria-label="Bild <?php echo $i+1; ?>"></button>
			<?php endforeach; ?>
		</div>
	<?php else : ?>
		<div class="hero__bg" style="background-image:url('<?php echo esc_url($hero_slides[0]); ?>')" aria-hidden="true"></div>
	<?php endif; ?>
	<div class="hero__overlay"></div>
	<?php if ( $hero_logo_id ) {
		echo wp_get_attachment_image( $hero_logo_id, 'full', false, [ 'class' => 'hero__watermark', 'alt' => '', 'aria-hidden' => 'true', 'loading' => 'lazy' ] );
	} ?>
	<div class="hero__content">
		<p class="hero__kicker"><?php esc_html_e('Speedway & Flattrack · Malmö', 'tandstiftet'); ?></p>
		<h1 class="hero__title"><?php echo $hero_title_html; ?></h1>
		<p class="hero__subtitle"><?php echo nl2br( esc_html( get_theme_mod( 'tmc_hero_subtitle', __( 'Vi kör dirt track med hjärta. Välkommen till klubben.', 'tandstiftet' ) ) ) ); ?></p>
		<div class="hero__actions">
			<a href="<?php echo esc_url( get_theme_mod('tmc_hero_btn1_url', '#evenemang') ); ?>" class="btn btn--primary"><?php echo esc_html( get_theme_mod('tmc_hero_btn1_text', __('Se kommande tävlingar', 'tandstiftet')) ); ?></a>
			<a href="<?php echo esc_url( get_theme_mod('tmc_hero_btn2_url', '#prova-pa') ); ?>" class="btn btn--outline"><?php echo esc_html( get_theme_mod('tmc_hero_btn2_text', __('Prova På →', 'tandstiftet')) ); ?></a>
		</div>
	</div>
	<div class="hero__diagonal"></div>
</section>

<!-- ═══ STATS BAR ═══ -->
<div class="stats-bar">
	<div class="stats-bar__inner">
		<?php $stats = [
			[ get_theme_mod('tmc_stat1_num','1970'),  get_theme_mod('tmc_stat1_label', __('Grundat','tandstiftet')) ],
			[ get_theme_mod('tmc_stat2_num','Div 1'), get_theme_mod('tmc_stat2_label', __('Seriespel','tandstiftet')) ],
			[ get_theme_mod('tmc_stat3_num','20+'),   get_theme_mod('tmc_stat3_label', __('Aktiva förare','tandstiftet')) ],
			[ get_theme_mod('tmc_stat4_num','Malmö'), get_theme_mod('tmc_stat4_label', __('Hemmatrack','tandstiftet')) ],
		];
		foreach ($stats as [$num, $label]) : ?>
			<div class="stat-item">
				<div class="stat-item__number"><?php echo esc_html($num); ?></div>
				<div class="stat-item__label"><?php echo esc_html($label); ?></div>
			</div>
		<?php endforeach; ?>
	</div>
</div>

<!-- ═══ NÄSTA TÄVLING (featured) ═══ -->
<?php $next = tmc_get_upcoming_events(1); if ($next->have_posts()) : $next->the_post();
	$edate    = get_post_meta(get_the_ID(),'tmc_event_date',true);
	$etime    = get_post_meta(get_the_ID(),'tmc_event_time',true);
	$eloc     = get_post_meta(get_the_ID(),'tmc_event_location',true);
	$eprice   = get_post_meta(get_the_ID(),'tmc_event_price',true);
	$next_url = get_permalink();
	$next_title = get_the_title();
	wp_reset_postdata();
?>
<?php if ( get_theme_mod('tmc_show_next_event', true) ) : ?>
<section class="next-event-banner">
	<div class="container">
		<div class="next-event-banner__inner">
			<div class="next-event-banner__left">
				<div class="label" style="margin-bottom:0.4rem"><?php esc_html_e('Nästa tävling','tandstiftet'); ?></div>
				<?php if ($edate) : ?>
				<div class="next-event-banner__date">
					<span class="next-event-banner__day"><?php echo date_i18n('j', strtotime($edate)); ?></span>
					<span class="next-event-banner__month"><?php echo date_i18n('F Y', strtotime($edate)); ?></span>
				</div>
				<?php endif; ?>
				<h2 class="next-event-banner__title"><?php echo esc_html($next_title); ?></h2>
				<?php if ($eloc || $etime) : ?>
				<p class="next-event-banner__meta">
					<?php if ($eloc) echo esc_html($eloc); ?>
					<?php if ($etime) echo ' &middot; ' . esc_html($etime); ?>
				</p>
				<?php endif; ?>
			</div>
			<div class="next-event-banner__right">
				<a href="<?php echo esc_url($next_url); ?>" class="btn btn--primary"><?php esc_html_e('Matchinfo','tandstiftet'); ?> →</a>
				<span class="next-event-banner__price"><?php if ($eprice) echo esc_html($eprice) . ' · '; esc_html_e('Biljetter säljs på plats','tandstiftet'); ?></span>
			</div>
		</div>
	</div>
</section>
<?php endif; // tmc_show_next_event ?>
<?php endif; // $next->have_posts() ?>
<?php if ( get_theme_mod('tmc_show_disciplines', true) ) : ?>
<section class="section section--coal" id="sektioner">
	<div class="container">
		<div class="section-header">
			<div class="label" style="margin-bottom:0.4rem"><?php esc_html_e('Trackracing','tandstiftet'); ?></div>
			<h2 class="section-title"><?php esc_html_e('Sektioner','tandstiftet'); ?></h2>
		</div>
		<div class="discipline-grid">
			<?php
			$disciplines = [
				[
					'slug'    => 'speedway',
					'title'   => get_theme_mod('tmc_disc1_title', __('Speedway','tandstiftet')),
					'desc'    => get_theme_mod('tmc_disc1_desc',  __('Oval bana, 500cc motorer utan broms. Den klassiska trackracing-disciplinen på hög nivå.','tandstiftet')),
					'img'     => get_theme_mod('tmc_disc1_image'),
					'url'     => get_theme_mod('tmc_disc1_url',   home_url('/sektioner/speedway/')),
				],
				[
					'slug'    => 'flattrack',
					'title'   => get_theme_mod('tmc_disc2_title', __('Flat Track','tandstiftet')),
					'desc'    => get_theme_mod('tmc_disc2_desc',  __('Dirt track med bromsar. Öppet för fler maskintyper och klasser — perfekt för nybörjare.','tandstiftet')),
					'img'     => get_theme_mod('tmc_disc2_image'),
					'url'     => get_theme_mod('tmc_disc2_url',   home_url('/sektioner/flat-track/')),
				],
				[
					'slug'    => 'veteran',
					'title'   => get_theme_mod('tmc_disc3_title', __('Veteran','tandstiftet')),
					'desc'    => get_theme_mod('tmc_disc3_desc',  __('För förare 40+ år. Klassiska maskiner, avslappnad stämning — passionen finns kvar.','tandstiftet')),
					'img'     => get_theme_mod('tmc_disc3_image'),
					'url'     => get_theme_mod('tmc_disc3_url',   home_url('/sektioner/veteran/')),
				],
			];
			foreach ($disciplines as $d) :
				$img_url = $d['img'] ? wp_get_attachment_image_url($d['img'], 'news-thumb') : '';
			?>
			<a href="<?php echo esc_url($d['url']); ?>" class="discipline-card">
				<div class="discipline-card__img" <?php if ($img_url) echo 'style="background-image:url(' . esc_url($img_url) . ')"'; ?>>
					<?php if (!$img_url) : ?>
						<div class="discipline-card__img-placeholder"></div>
					<?php endif; ?>
					<div class="discipline-card__img-overlay"></div>
				</div>
				<div class="discipline-card__body">
					<h3 class="discipline-card__title"><?php echo esc_html($d['title']); ?></h3>
					<p class="discipline-card__desc"><?php echo esc_html($d['desc']); ?></p>
					<span class="discipline-card__link"><?php esc_html_e('Läs mer','tandstiftet'); ?> →</span>
				</div>
			</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- ═══ PROVA PÅ (Svemo-krav — prominent) ═══ -->
<?php if ( get_theme_mod('tmc_show_provapa', true) ) : ?>
<section class="prova-pa-section" id="prova-pa">
	<div class="prova-pa-section__bg" aria-hidden="true"></div>
	<div class="prova-pa-section__overlay"></div>
	<div class="container" style="position:relative;z-index:2">
		<div class="prova-pa-section__inner">
			<div class="prova-pa-section__text">
				<div class="label" style="margin-bottom:0.4rem"><?php esc_html_e('Kom & kör','tandstiftet'); ?></div>
				<h2 class="prova-pa-section__title"><?php esc_html_e('Prova På!','tandstiftet'); ?></h2>
				<p class="prova-pa-section__desc">
					<?php echo esc_html( get_theme_mod('tmc_provapa_desc', __('Ingen erfarenhet krävs — vi lånar ut utrustning och har instruktörer på plats.','tandstiftet')) ); ?>
				</p>
				<ul class="prova-pa-section__bullets">
					<li><?php esc_html_e('Barn & ungdomar kör speedway gratis (licens tillkommer 12+)','tandstiftet'); ?></li>
					<?php if ( tmc_sliding_enabled() ) : ?>
					<li><?php esc_html_e('Vuxna kör Sliding — flat track eller speedway, 800 kr','tandstiftet'); ?></li>
					<li><?php esc_html_e('Sliding bokas även för kalas, möhippa & företagsevent','tandstiftet'); ?></li>
					<?php endif; ?>
					<li><?php esc_html_e('Hjälm & skyddsutrustning ingår alltid','tandstiftet'); ?></li>
					<li><?php esc_html_e('Svemo-godkänd verksamhet','tandstiftet'); ?></li>
				</ul>
				<div style="display:flex;gap:var(--space-md);flex-wrap:wrap;margin-top:var(--space-xl)">
					<?php $pp_page = get_page_by_path('prova-pa'); $sliding_page = get_page_by_path('sliding'); ?>
					<a href="<?php echo esc_url($pp_page ? get_permalink($pp_page->ID) : '#'); ?>" class="btn btn--primary"><?php esc_html_e('Prova På — Barn & Ungdom (gratis)','tandstiftet'); ?></a>
					<?php if ( tmc_sliding_enabled() ) : ?>
					<a href="<?php echo esc_url($sliding_page ? get_permalink($sliding_page->ID) : '#'); ?>" class="btn btn--outline"><?php esc_html_e('Sliding — Prova På för vuxna','tandstiftet'); ?></a>
					<?php endif; ?>
				</div>
			</div>
			<div class="prova-pa-section__badge">
				<div class="prova-pa-badge">
					<span class="prova-pa-badge__top"><?php esc_html_e('Svemo','tandstiftet'); ?></span>
					<span class="prova-pa-badge__main"><?php esc_html_e('Prova På','tandstiftet'); ?></span>
					<span class="prova-pa-badge__sub"><?php esc_html_e('Godkänd klubb','tandstiftet'); ?></span>
				</div>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- ═══ TÄVLINGAR — kommande / fler kommer / tack för i år / dold (Anpassa → Startsida — Tävlingar) ═══ -->
<?php if ( get_theme_mod('tmc_show_events', true) ) :
	$today        = date( 'Y-m-d' );
	$events       = tmc_get_upcoming_events( 4 );
	$season_year  = tmc_competition_season_year( $today );
	$season_query = tmc_get_season_events( $season_year, 8 );
	$ev_state     = tmc_home_events_state( $today, $events->have_posts(), $season_query->found_posts );
	$he           = 'tmc_home_events_text';
	$ev_archive   = get_post_type_archive_link( 'tmc_event' );
	$with_year    = function ( $s ) use ( $season_year ) { return str_replace( '{år}', $season_year, $s ); };
	if ( $ev_state !== 'hidden' ) : ?>
<section class="section section--dark home-events home-events--<?php echo esc_attr( $ev_state ); ?>" id="evenemang">
	<div class="container">
	<?php if ( $ev_state === 'upcoming' ) : ?>
		<div class="section-header">
			<?php if ( $he( 'upcoming_label' ) ) : ?><div class="label" style="margin-bottom:0.4rem"><?php echo esc_html( $he( 'upcoming_label' ) ); ?></div><?php endif; ?>
			<h2 class="section-title"><?php echo esc_html( $he( 'upcoming_title' ) ); ?></h2>
		</div>
		<div class="grid grid--2">
			<?php while ( $events->have_posts() ) : $events->the_post(); tmc_event_card( get_the_ID() ); endwhile; wp_reset_postdata(); ?>
		</div>
		<p class="home-events__earlier">
			<?php if ( $season_year === (int) substr( $today, 0, 4 ) && $season_query->found_posts > 0 && $he( 'earlier_text' ) ) : ?>
				<?php echo esc_html( str_replace( '{antal}', (int) $season_query->found_posts, $he( 'earlier_text' ) ) ); ?> ·
			<?php endif; ?>
			<?php if ( $ev_archive && $he( 'archive_link' ) ) : ?><a href="<?php echo esc_url( $ev_archive ); ?>"><?php echo esc_html( $he( 'archive_link' ) ); ?></a><?php endif; ?>
		</p>
	<?php else :
		$k = $ev_state === 'season_over' ? 'over' : 'more'; ?>
		<div class="section-header">
			<?php if ( $he( $k . '_label' ) ) : ?><div class="label" style="margin-bottom:0.4rem"><?php echo esc_html( $with_year( $he( $k . '_label' ) ) ); ?></div><?php endif; ?>
			<h2 class="section-title"><?php echo esc_html( $with_year( $he( $k . '_title' ) ) ); ?></h2>
		</div>
		<?php if ( $he( $k . '_text' ) ) : ?><p class="home-events__intro"><?php echo esc_html( $with_year( $he( $k . '_text' ) ) ); ?></p><?php endif; ?>
		<ul class="home-season-list">
			<?php while ( $season_query->have_posts() ) : $season_query->the_post();
				$d    = get_post_meta( get_the_ID(), 'tmc_event_date', true );
				$loc  = get_post_meta( get_the_ID(), 'tmc_event_location', true );
				$meta = array_filter( [ $loc, get_the_excerpt() ] ); ?>
				<li>
					<span class="home-season-list__date"><?php echo $d ? esc_html( date_i18n( 'j M', strtotime( $d ) ) ) : ''; ?></span>
					<span class="home-season-list__title"><?php the_title(); ?>
						<?php if ( $meta ) : ?><span class="home-season-list__meta"><?php echo esc_html( implode( ' · ', $meta ) ); ?></span><?php endif; ?>
					</span>
					<a href="<?php the_permalink(); ?>"><?php esc_html_e( 'Matchinfo', 'tandstiftet' ); ?> →</a>
				</li>
			<?php endwhile; wp_reset_postdata(); ?>
		</ul>
		<?php if ( $ev_archive && $he( 'archive_link' ) ) : ?><p class="home-events__earlier"><a href="<?php echo esc_url( $ev_archive ); ?>"><?php echo esc_html( $he( 'archive_link' ) ); ?></a></p><?php endif; ?>

		<?php if ( $ev_state === 'season_over' ) :
			$pp       = tmc_signup_state( 'provapa' );
			$pp_words = $pp['open'] ? __( 'Kom och prova!', 'tandstiftet' ) : ( $pp['reopens'] ? sprintf( __( 'Öppnar igen %s', 'tandstiftet' ), tmc_swedish_date( $pp['reopens'] ) ) : __( 'Hör av dig till oss', 'tandstiftet' ) );
			$boxes    = array_filter( array_map( function ( $i ) use ( $he, $pp_words ) {
				return [ 'title' => $he( "box_{$i}_title" ), 'text' => str_replace( '{provapa}', $pp_words, $he( "box_{$i}_text" ) ), 'url' => $he( "box_{$i}_url" ) ];
			}, [ 1, 2, 3 ] ), function ( $b ) { return $b['title'] !== ''; } );
			if ( $boxes ) : ?>
			<div class="home-season-next">
				<?php foreach ( $boxes as $b ) : ?>
					<a href="<?php echo esc_url( $b['url'] ?: '#' ); ?>"><strong><?php echo esc_html( $b['title'] ); ?></strong><?php if ( $b['text'] ) : ?><span><?php echo esc_html( $b['text'] ); ?></span><?php endif; ?></a>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>
	</div>
</section>
<?php endif; endif; // tmc_show_events / hidden ?>

<!-- ═══ LATEST NEWS ═══ -->
<?php if ( get_theme_mod('tmc_show_news', true) ) : ?>
<section class="section section--beige" id="nyheter">
	<div class="container">
		<div class="section-header">
			<div class="label" style="margin-bottom:0.4rem"><?php esc_html_e('Senaste nytt','tandstiftet'); ?></div>
			<h2 class="section-title"><?php esc_html_e('Nyheter','tandstiftet'); ?></h2>
		</div>
		<?php $news = new WP_Query(['posts_per_page'=>3,'post_status'=>'publish']); ?>
		<?php if ($news->have_posts()) : ?>
			<div class="grid grid--3">
				<?php while ($news->have_posts()) : $news->the_post(); ?>
					<article class="news-card">
						<?php if (has_post_thumbnail()) : ?><div class="news-card__image"><?php the_post_thumbnail('news-thumb'); ?></div><?php endif; ?>
						<div class="news-card__body">
							<div class="news-card__meta"><span class="news-card__date"><?php echo get_the_date('j M Y'); ?></span></div>
							<h3 class="news-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p class="news-card__excerpt"><?php the_excerpt(); ?></p>
							<a href="<?php the_permalink(); ?>" class="news-card__link"><?php esc_html_e('Läs mer','tandstiftet'); ?></a>
						</div>
					</article>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
			<div style="text-align:center;margin-top:var(--space-xl)">
				<a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" class="btn btn--outline"><?php esc_html_e('Alla nyheter','tandstiftet'); ?></a>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<!-- ═══ DIVISION 1 RIDERS ═══ -->
<?php if ( get_theme_mod('tmc_show_riders', true) ) : ?>
<section class="section section--coal" id="forare">
	<div class="container">
		<div class="section-header">
			<div class="label" style="margin-bottom:0.4rem"><?php esc_html_e('Division 1','tandstiftet'); ?></div>
			<h2 class="section-title"><?php esc_html_e('Laget','tandstiftet'); ?></h2>
		</div>
		<?php $riders = tmc_get_riders('division-1', 4); ?>
		<?php if ($riders->have_posts()) : ?>
			<div class="grid grid--4">
				<?php while ($riders->have_posts()) : $riders->the_post(); tmc_rider_card(get_the_ID()); endwhile; wp_reset_postdata(); ?>
			</div>
			<div style="text-align:center;margin-top:var(--space-xl)">
				<a href="<?php echo esc_url(get_permalink(get_page_by_path('forare-och-lag'))); ?>" class="btn btn--primary"><?php esc_html_e('Se alla förare & lag','tandstiftet'); ?></a>
			</div>
		<?php else : ?>
			<p style="text-align:center;color:var(--color-grey-mid)"><?php esc_html_e('Inga förarprofiler publicerade ännu.','tandstiftet'); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<!-- ========================================================
     VÄRDERINGAR
     ======================================================== -->
<?php if ( get_theme_mod('tmc_show_values', true) ) : ?>
<section class="section values-section" id="varderingar">
	<div class="container">

		<div class="values-section__header">
			<span class="label"><?php echo esc_html( get_theme_mod('tmc_values_label', __('Vår kultur','tandstiftet')) ); ?></span>
			<h2 class="section-title"><?php echo esc_html( get_theme_mod('tmc_values_title', __('Vi tar hand om varandra','tandstiftet')) ); ?></h2>
			<div class="section-title-line"></div>
			<p class="values-section__intro"><?php echo esc_html( get_theme_mod('tmc_values_intro', __('På Tändstiftet tävlar vi hårt på banan — men utanför den tar vi hand om varandra. Det är en av de viktigaste reglerna vi har.','tandstiftet')) ); ?></p>
		</div>

		<div class="values-grid">
			<div class="values-card">
				<span class="values-card__icon" aria-hidden="true">🏁</span>
				<h3 class="values-card__title"><?php echo esc_html( get_theme_mod('tmc_values_card1_title', __('Barnen först','tandstiftet')) ); ?></h3>
				<p class="values-card__text"><?php echo esc_html( get_theme_mod('tmc_values_card1_text', __('Varje barn som kliver in på vår bana ska känna sig välkommen, sedd och trygg — oavsett om de vinner, förlorar eller råkar ut för en olycka. Vid en incident är första prioritet alltid omhändertagande, inte skuld.','tandstiftet')) ); ?></p>
			</div>
			<div class="values-card">
				<span class="values-card__icon" aria-hidden="true">🤝</span>
				<h3 class="values-card__title"><?php echo esc_html( get_theme_mod('tmc_values_card2_title', __('Nolltolerans','tandstiftet')) ); ?></h3>
				<p class="values-card__text"><?php echo esc_html( get_theme_mod('tmc_values_card2_text', __('Vi har nolltolerans mot mobbning och kränkande behandling — på banan, i paddocken och online. Om något känns fel ska det alltid gå att prata med en vuxen i klubben.','tandstiftet')) ); ?></p>
			</div>
			<div class="values-card">
				<span class="values-card__icon" aria-hidden="true">👨‍👩‍👧</span>
				<h3 class="values-card__title"><?php echo esc_html( get_theme_mod('tmc_values_card3_title', __('Föräldrar & vuxna','tandstiftet')) ); ?></h3>
				<p class="values-card__text"><?php echo esc_html( get_theme_mod('tmc_values_card3_text', __('Föräldrar och anhöriga är välkomna och uppskattade. Vi ber er stötta era egna barn, visa respekt mot alla andra och lita på att funktionärerna gör sitt bästa. Hur vi vuxna uppför oss sätter tonen för hela klubben.','tandstiftet')) ); ?></p>
			</div>
		</div>

		<div class="values-quote-block">
			<p class="values-quote-block__text"><?php echo esc_html( get_theme_mod('tmc_values_quote', __('Ett gott bemötande är inte valfritt hos oss — det är en förutsättning för att få vara en del av vår klubb. Vi stödjer SVEMO:s arbete för en trygg och ren motorsport för unga, och uppmanar alla familjer att bekanta sig med deras riktlinjer.','tandstiftet')) ); ?></p>
			<a href="https://www.svemo.se/om-forbundet/trygg-motorsport" class="values-quote-block__link" target="_blank" rel="noopener"><?php esc_html_e('Läs mer på svemo.se','tandstiftet'); ?> →</a>
		</div>

		<div class="values-punchline">
			<p><?php echo esc_html( get_theme_mod('tmc_values_punchline', __('Snabbast på banan. Schysst mot varandra.','tandstiftet')) ); ?></p>
		</div>

	</div>
</section>
<?php endif; ?>

<!-- ═══ SPONSORS ═══ -->
<?php $sponsors = new WP_Query(['post_type'=>'tmc_sponsor','posts_per_page'=>-1,'meta_key'=>'tmc_sponsor_order','orderby'=>'meta_value_num','order'=>'ASC']);
if ($sponsors->have_posts()) : ?>
<section class="sponsors-section" id="sponsorer">
	<div class="container">
		<div class="section-header">
			<div class="label" style="margin-bottom:0.4rem"><?php esc_html_e('Tack till','tandstiftet'); ?></div>
			<h2 class="section-title"><?php esc_html_e('Våra sponsorer','tandstiftet'); ?></h2>
		</div>
		<div class="sponsors-grid">
			<?php while ($sponsors->have_posts()) : $sponsors->the_post();
				$url = get_post_meta(get_the_ID(),'tmc_sponsor_url',true);
				$tag = $url ? 'a' : 'div';
				$attrs = $url ? 'href="'.esc_url($url).'" target="_blank" rel="noopener sponsor"' : '';
			?>
				<<?php echo $tag; ?> class="sponsor-item" <?php echo $attrs; ?>>
					<?php if (has_post_thumbnail()) : the_post_thumbnail('sponsor-logo',['loading'=>'lazy']);
					else : ?><span style="font-family:var(--font-condensed);font-weight:700;font-size:0.9rem;color:#666"><?php the_title(); ?></span><?php endif; ?>
				</<?php echo $tag; ?>>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
