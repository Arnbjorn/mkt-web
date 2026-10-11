<?php
/**
 * Vanlig sida (Standardmall) — v2.
 * Sidhuvud, sedan innehållet på en beige läsyta i en smal spalt (lättläst),
 * med sidans Utvald bild överst om den finns. Ingen sidopanel.
 *
 * @package tandstiftet
 */
get_header();
tmc_page_header();
?>
<main class="section section--beige page-read" id="main">
	<div class="container page-read__inner">
		<?php while ( have_posts() ) : the_post(); ?>
			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="page-read__image"><?php the_post_thumbnail( 'large' ); ?></figure>
			<?php endif; ?>
			<div class="info-content page-read__content">
				<?php the_content(); ?>
			</div>
			<?php wp_link_pages(); ?>
		<?php endwhile; ?>
	</div>
</main>
<?php get_footer(); ?>
