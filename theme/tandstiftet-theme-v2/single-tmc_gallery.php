<?php
/**
 * Single tmc_gallery template
 *
 * @package tandstiftet
 */
get_header();
while (have_posts()) : the_post();
tmc_page_header(get_the_title(), get_the_date('j F Y'), 'GALLERI');
?>
<section class="section section--dark">
<div class="container">
	<?php if (get_the_excerpt()) : ?>
		<p style="max-width:640px;color:var(--color-grey-light);margin-bottom:var(--space-xl);font-size:1.05rem;line-height:1.7"><?php the_excerpt(); ?></p>
	<?php endif; ?>

	<div class="info-content" style="max-width:100%">
		<?php the_content(); ?>
	</div>

	<div style="margin-top:var(--space-xl)">
		<a href="<?php echo esc_url(get_post_type_archive_link('tmc_gallery')); ?>" class="btn btn--outline">← <?php esc_html_e('Tillbaka till galleri','tandstiftet'); ?></a>
	</div>
</div>
</section>
<?php endwhile; ?>
<?php get_footer(); ?>
