<?php
/**
 * Single tmc_event template
 *
 * @package tandstiftet
 */
get_header();

while (have_posts()) : the_post();
	$date      = get_post_meta(get_the_ID(), 'tmc_event_date', true);
	$time      = get_post_meta(get_the_ID(), 'tmc_event_time', true);
	$location  = get_post_meta(get_the_ID(), 'tmc_event_location', true);
	$address   = get_post_meta(get_the_ID(), 'tmc_event_address', true);
	$price     = get_post_meta(get_the_ID(), 'tmc_event_price', true);
	$fb_url    = get_post_meta(get_the_ID(), 'tmc_event_facebook', true);
?>
<?php tmc_page_header(get_the_title(), '', 'EVENT'); ?>

<div class="content-sidebar-wrap">
	<main class="main-content">
		<?php if (has_post_thumbnail()) : ?>
			<figure style="margin-bottom:var(--space-xl)">
				<?php the_post_thumbnail('large', ['style'=>'width:100%;filter:sepia(0.15)']); ?>
			</figure>
		<?php endif; ?>

		<div class="info-content">
			<?php the_content(); ?>
		</div>

		<div style="display:flex;gap:var(--space-md);flex-wrap:wrap;margin-top:var(--space-xl)">
			<?php if ($fb_url) : ?>
				<a href="<?php echo esc_url($fb_url); ?>" class="btn btn--outline" target="_blank" rel="noopener">
					<?php esc_html_e('Facebook-evenemang', 'tandstiftet'); ?>
				</a>
			<?php endif; ?>
			<a href="<?php echo esc_url(get_post_type_archive_link('tmc_event')); ?>" class="btn btn--outline">
				← <?php esc_html_e('Alla tävlingar', 'tandstiftet'); ?>
			</a>
		</div>
	</main>

	<aside class="sidebar">
		<div class="sidebar-widget">
			<h3 class="sidebar-widget__title"><?php esc_html_e('Detaljer', 'tandstiftet'); ?></h3>
			<dl style="display:grid;gap:var(--space-sm)">
				<?php if ($date) : ?>
					<dt class="label"><?php esc_html_e('Datum', 'tandstiftet'); ?></dt>
					<dd style="color:var(--color-cream)"><?php echo esc_html(date_i18n('l j F Y', strtotime($date))); ?></dd>
				<?php endif; ?>
				<?php if ($time) : ?>
					<dt class="label"><?php esc_html_e('Tid', 'tandstiftet'); ?></dt>
					<dd><?php echo esc_html($time); ?></dd>
				<?php endif; ?>
				<?php if ($location) : ?>
					<dt class="label"><?php esc_html_e('Plats', 'tandstiftet'); ?></dt>
					<dd><?php echo esc_html($location); ?></dd>
				<?php endif; ?>
				<?php if ($address) : ?>
					<dt class="label"><?php esc_html_e('Adress', 'tandstiftet'); ?></dt>
					<dd><?php echo esc_html($address); ?></dd>
				<?php endif; ?>
				<?php if ($price) : ?>
					<dt class="label"><?php esc_html_e('Inträde', 'tandstiftet'); ?></dt>
					<dd><?php echo esc_html($price); ?></dd>
				<?php endif; ?>
			</dl>
		</div>

		<?php
		$upcoming = tmc_get_upcoming_events(4);
		if ($upcoming->have_posts()) : ?>
		<div class="sidebar-widget">
			<h3 class="sidebar-widget__title"><?php esc_html_e('Fler tävlingar', 'tandstiftet'); ?></h3>
			<ul style="display:grid;gap:var(--space-sm)">
				<?php while ($upcoming->have_posts()) : $upcoming->the_post();
					if (get_the_ID() === get_queried_object_id()) { wp_reset_postdata(); continue; }
					$edate = get_post_meta(get_the_ID(), 'tmc_event_date', true);
				?>
					<li style="border-bottom:1px solid rgba(255,255,255,0.06);padding-bottom:var(--space-sm)">
						<a href="<?php the_permalink(); ?>" style="font-family:var(--font-condensed);font-size:0.85rem;color:var(--color-cream)"><?php the_title(); ?></a>
						<?php if($edate) : ?><br><small style="color:var(--color-grey-mid)"><?php echo esc_html(date_i18n('j M Y', strtotime($edate))); ?></small><?php endif; ?>
					</li>
				<?php endwhile; wp_reset_postdata(); ?>
			</ul>
		</div>
		<?php endif; ?>
	</aside>
</div>

<?php endwhile; ?>

<?php get_footer(); ?>
