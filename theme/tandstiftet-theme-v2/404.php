<?php get_header(); ?>
<section class="section section--dark" style="min-height:60vh;display:flex;align-items:center">
	<div class="container" style="text-align:center">
		<p class="label"><?php esc_html_e('Fel 404','tandstiftet'); ?></p>
		<h1 style="font-size:clamp(5rem,20vw,12rem);color:var(--color-gold);opacity:0.2;line-height:1">404</h1>
		<h2 style="margin-bottom:var(--space-lg)"><?php esc_html_e('Sidan hittades inte','tandstiftet'); ?></h2>
		<p style="color:var(--color-grey-light);margin-bottom:var(--space-xl)">
			<?php esc_html_e('Sidan du letar efter verkar inte finnas. Den kan ha flyttats, tagits bort eller aldrig existerat.','tandstiftet'); ?>
		</p>
		<a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn--primary"><?php esc_html_e('Tillbaka till startsidan','tandstiftet'); ?></a>
	</div>
</section>
<?php get_footer(); ?>
