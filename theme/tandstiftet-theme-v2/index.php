<?php
/**
 * Blog archive / Nyheter
 * @package tandstiftet
 */
get_header();
tmc_page_header(
	is_home() ? __('Nyheter','tandstiftet') : wp_strip_all_tags( get_the_archive_title() ),
	is_home() ? __('Senaste nytt från klubben','tandstiftet') : get_the_archive_description(),
	'NYHETER'
);
?>

<div class="news-archive-wrap">

	<!-- ── Main content ── -->
	<main class="news-archive-main">
		<?php if ( have_posts() ) : ?>

			<div class="news-archive-grid">
				<?php while ( have_posts() ) : the_post(); ?>
					<article class="news-card">
						<?php if ( has_post_thumbnail() ) : ?>
							<a href="<?php the_permalink(); ?>" class="news-card__image" style="display:block">
								<?php the_post_thumbnail('news-thumb'); ?>
							</a>
						<?php else : ?>
							<div class="news-card__image news-card__image--placeholder">
								<span class="dp" style="font-size:2rem;color:rgba(212,160,23,0.2)"><?php echo mb_strtoupper( mb_substr( get_the_title(), 0, 1 ) ); ?></span>
							</div>
						<?php endif; ?>
						<div class="news-card__body">
							<div class="news-card__meta">
								<span class="news-card__date"><?php echo get_the_date('j M Y'); ?></span>
								<?php $cats = get_the_category();
								if ( $cats ) : ?>
									<span class="news-card__category"><?php echo esc_html( $cats[0]->name ); ?></span>
								<?php endif; ?>
							</div>
							<h2 class="news-card__title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>
							<p class="news-card__excerpt"><?php the_excerpt(); ?></p>
							<a href="<?php the_permalink(); ?>" class="news-card__link"><?php esc_html_e('Läs mer','tandstiftet'); ?></a>
						</div>
					</article>
				<?php endwhile; ?>
			</div>

			<!-- Pagination -->
			<nav class="news-pagination">
				<?php the_posts_pagination([
					'mid_size'           => 2,
					'prev_text'          => '← ' . __('Äldre','tandstiftet'),
					'next_text'          => __('Nyare','tandstiftet') . ' →',
					'before_page_number' => '',
				]); ?>
			</nav>

		<?php else : ?>
			<div style="padding:var(--space-2xl);text-align:center">
				<p style="color:var(--color-grey-mid);font-family:var(--font-condensed);font-size:1.1rem;letter-spacing:0.05em">
					<?php esc_html_e('Inga nyheter publicerade ännu. Håll utkik!','tandstiftet'); ?>
				</p>
			</div>
		<?php endif; ?>
	</main>

	<!-- ── Sidebar ── -->
	<aside class="news-archive-sidebar">

		<!-- Upcoming events widget -->
		<?php $upcoming = tmc_get_upcoming_events(3); if ( $upcoming->have_posts() ) : ?>
		<div class="sidebar-widget">
			<h3 class="sidebar-widget__title"><?php esc_html_e('Kommande tävlingar','tandstiftet'); ?></h3>
			<ul style="display:flex;flex-direction:column;gap:var(--space-md)">
				<?php while ( $upcoming->have_posts() ) : $upcoming->the_post();
					$edate = get_post_meta(get_the_ID(),'tmc_event_date',true);
					$eloc  = get_post_meta(get_the_ID(),'tmc_event_location',true);
				?>
				<li style="display:grid;grid-template-columns:48px 1fr;gap:var(--space-sm);align-items:start">
					<?php if ($edate) : ?>
					<div style="background:var(--color-gold);text-align:center;padding:4px 0;flex-shrink:0">
						<div style="font-family:var(--font-display);font-size:1.4rem;color:var(--color-dark);line-height:1"><?php echo date_i18n('j',strtotime($edate)); ?></div>
						<div style="font-family:var(--font-condensed);font-size:0.6rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:rgba(10,22,40,0.6)"><?php echo date_i18n('M',strtotime($edate)); ?></div>
					</div>
					<?php else : ?>
					<div style="background:var(--color-mid);width:48px;height:48px"></div>
					<?php endif; ?>
					<div>
						<a href="<?php the_permalink(); ?>" style="font-family:var(--font-condensed);font-size:0.9rem;font-weight:600;color:var(--color-cream);text-decoration:none;line-height:1.2;display:block;margin-bottom:2px"><?php the_title(); ?></a>
						<?php if ($eloc) : ?><span style="font-size:0.75rem;color:var(--color-grey-mid)"><?php echo esc_html($eloc); ?></span><?php endif; ?>
					</div>
				</li>
				<?php endwhile; wp_reset_postdata(); ?>
			</ul>
			<div style="margin-top:var(--space-md)">
				<a href="<?php echo esc_url(get_post_type_archive_link('tmc_event')); ?>" class="btn btn--outline" style="font-size:0.7rem;padding:0.5em 1em"><?php esc_html_e('Alla tävlingar','tandstiftet'); ?></a>
			</div>
		</div>
		<?php endif; ?>

		<!-- Categories widget -->
		<?php $cats = get_categories(['hide_empty'=>true]);
		if ( $cats ) : ?>
		<div class="sidebar-widget">
			<h3 class="sidebar-widget__title"><?php esc_html_e('Kategorier','tandstiftet'); ?></h3>
			<ul style="display:flex;flex-direction:column;gap:4px">
				<?php foreach ($cats as $cat) : ?>
				<li>
					<a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>"
					   style="font-family:var(--font-condensed);font-size:0.85rem;color:var(--color-grey-light);text-decoration:none;display:flex;justify-content:space-between;align-items:center;padding:0.3em 0;border-bottom:1px solid rgba(255,255,255,0.05);transition:color 0.2s"
					   onmouseover="this.style.color='#d4a017'" onmouseout="this.style.color=''">
						<span><?php echo esc_html($cat->name); ?></span>
						<span style="font-size:0.7rem;color:var(--color-grey-mid)"><?php echo $cat->count; ?></span>
					</a>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endif; ?>

		<!-- Featured riders widget -->
		<?php $riders = tmc_get_riders('division-1', 3);
		if ($riders->have_posts()) : ?>
		<div class="sidebar-widget">
			<h3 class="sidebar-widget__title"><?php esc_html_e('Division 1-laget','tandstiftet'); ?></h3>
			<ul style="display:flex;flex-direction:column;gap:var(--space-sm)">
				<?php while ($riders->have_posts()) : $riders->the_post();
					$num = get_post_meta(get_the_ID(),'tmc_rider_number',true);
				?>
				<li style="display:flex;align-items:center;gap:var(--space-sm);padding-bottom:var(--space-sm);border-bottom:1px solid rgba(255,255,255,0.05)">
					<?php if (has_post_thumbnail()) : ?>
						<div style="width:36px;height:36px;border-radius:50%;overflow:hidden;flex-shrink:0">
							<?php the_post_thumbnail('thumbnail',['style'=>'width:100%;height:100%;object-fit:cover']); ?>
						</div>
					<?php else : ?>
						<div style="width:36px;height:36px;border-radius:50%;background:var(--color-mid);flex-shrink:0;display:flex;align-items:center;justify-content:center">
							<span style="font-family:var(--font-display);font-size:0.9rem;color:var(--color-gold)"><?php echo $num ?: '?'; ?></span>
						</div>
					<?php endif; ?>
					<span style="font-family:var(--font-condensed);font-size:0.875rem;color:var(--color-cream)"><?php the_title(); ?></span>
					<?php if ($num) : ?><span style="font-family:var(--font-display);font-size:0.9rem;color:var(--color-gold-dim);margin-left:auto">#<?php echo esc_html($num); ?></span><?php endif; ?>
				</li>
				<?php endwhile; wp_reset_postdata(); ?>
			</ul>
			<div style="margin-top:var(--space-md)">
				<a href="<?php echo esc_url(get_permalink(get_page_by_path('forare-och-lag'))); ?>" class="btn btn--outline" style="font-size:0.7rem;padding:0.5em 1em"><?php esc_html_e('Se alla förare','tandstiftet'); ?></a>
			</div>
		</div>
		<?php endif; ?>

		<!-- WordPress sidebar widgets (fallback) -->
		<?php if ( is_active_sidebar('sidebar-news') ) dynamic_sidebar('sidebar-news'); ?>

	</aside>

</div><!-- /.news-archive-wrap -->

<?php get_footer(); ?>
