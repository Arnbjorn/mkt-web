<?php
/**
 * Archive template for tmc_gallery (album listing)
 *
 * @package tandstiftet
 */
get_header();
tmc_page_header(__('Galleri', 'tandstiftet'), __('Bilder från tävlingar, träningar och klubblivet.','tandstiftet'), 'GALLERI');
?>

<section class="section section--dark">
<div class="container">
	<?php if (have_posts()) : ?>
		<div class="grid grid--3">
			<?php while (have_posts()) : the_post(); ?>
				<article class="news-card">
					<?php if (has_post_thumbnail()) : ?>
						<a href="<?php the_permalink(); ?>" class="news-card__image" style="display:block">
							<?php the_post_thumbnail('gallery-thumb'); ?>
						</a>
					<?php endif; ?>
					<div class="news-card__body">
						<div class="news-card__meta">
							<span class="news-card__date"><?php echo get_the_date('j M Y'); ?></span>
						</div>
						<h3 class="news-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<?php if (get_the_excerpt()) : ?>
							<p class="news-card__excerpt"><?php the_excerpt(); ?></p>
						<?php endif; ?>
						<a href="<?php the_permalink(); ?>" class="news-card__link"><?php esc_html_e('Visa album','tandstiftet'); ?></a>
					</div>
				</article>
			<?php endwhile; ?>
		</div>
		<div style="margin-top:var(--space-xl)"><?php the_posts_pagination(); ?></div>
	<?php else : ?>
		<p style="text-align:center;color:var(--color-grey-mid)"><?php esc_html_e('Inga album publicerade ännu.','tandstiftet'); ?></p>
	<?php endif; ?>
</div>
</section>

<?php get_footer(); ?>
