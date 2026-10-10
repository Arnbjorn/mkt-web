<?php get_header(); tmc_page_header(); ?>
<div class="content-sidebar-wrap">
	<main class="main-content">
		<?php while (have_posts()) : the_post(); ?>
			<?php if (has_post_thumbnail()) : ?>
				<figure style="margin-bottom:var(--space-xl)"><?php the_post_thumbnail('large', ['style'=>'width:100%;filter:sepia(0.1)']); ?></figure>
			<?php endif; ?>
			<div class="info-content">
				<p class="label" style="margin-bottom:var(--space-md)"><?php echo get_the_date('j F Y'); ?></p>
				<?php the_content(); ?>
			</div>
			<div style="margin-top:var(--space-xl);padding-top:var(--space-lg);border-top:var(--border-asphalt)">
				<?php the_post_navigation(['prev_text'=>'← %title','next_text'=>'%title →']); ?>
			</div>
		<?php endwhile; ?>
	</main>
	<aside class="sidebar">
		<?php if (is_active_sidebar('sidebar-news')) dynamic_sidebar('sidebar-news'); ?>
	</aside>
</div>
<?php get_footer(); ?>
