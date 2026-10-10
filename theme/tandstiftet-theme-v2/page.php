<?php /* page.php */ get_header(); tmc_page_header(); ?>
<div class="content-sidebar-wrap">
	<main class="main-content">
		<div class="info-content">
			<?php while (have_posts()) : the_post(); the_content(); endwhile; ?>
		</div>
	</main>
	<aside class="sidebar">
		<?php if (is_active_sidebar('sidebar-pages')) dynamic_sidebar('sidebar-pages'); ?>
	</aside>
</div>
<?php get_footer(); ?>
