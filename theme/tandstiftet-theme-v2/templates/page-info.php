<?php
/**
 * Template Name: Informationssida
 * Description: Flexibel informationssida för regler, historia, säkerhet etc.
 *
 * @package tandstiftet
 */
get_header();
tmc_page_header(get_the_title(), get_post_meta(get_the_ID(),'tmc_page_subtitle',true), strtoupper(get_the_title()));
?>

<div class="content-sidebar-wrap">
	<main class="main-content">
		<div class="info-content">
			<?php while (have_posts()) : the_post(); ?>
				<?php the_content(); ?>
			<?php endwhile; ?>
		</div>
	</main>

	<aside class="sidebar">
		<!-- Sub-pages in same section -->
		<?php
		$current_id = get_the_ID();
		$parent_id  = wp_get_post_parent_id($current_id) ?: $current_id;

		$siblings = new WP_Query([
			'post_type'      => 'page',
			'posts_per_page' => -1,
			'post_parent'    => $parent_id,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		]);

		if ($siblings->have_posts()) : ?>
		<div class="sidebar-widget">
			<h3 class="sidebar-widget__title"><?php esc_html_e('I detta avsnitt', 'tandstiftet'); ?></h3>
			<ul style="display:grid;gap:var(--space-xs)">
				<?php
				// Also list the parent
				echo '<li><a href="' . esc_url(get_permalink($parent_id)) . '" style="' . ($parent_id===$current_id ? 'color:var(--color-gold)' : '') . '">' . esc_html(get_the_title($parent_id)) . '</a></li>';
				while ($siblings->have_posts()) : $siblings->the_post(); ?>
					<li style="padding-left:var(--space-md)">
						<a href="<?php the_permalink(); ?>" style="font-family:var(--font-condensed);font-size:0.85rem;<?php echo get_the_ID()===$current_id?'color:var(--color-gold)':'color:var(--color-grey-light)'; ?>">
							<?php the_title(); ?>
						</a>
					</li>
				<?php endwhile; wp_reset_postdata(); ?>
			</ul>
		</div>
		<?php endif; ?>

		<?php if (is_active_sidebar('sidebar-pages')) dynamic_sidebar('sidebar-pages'); ?>
	</aside>
</div>

<?php get_footer(); ?>
