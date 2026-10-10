<?php
/**
 * Template Name: Förare & Lag
 * Description: Single page with anchor-linked sections: Division 1, Vuxna, Ungdomar
 *
 * @package tandstiftet
 */
get_header();

tmc_page_header(
	__('Förare & Lag', 'tandstiftet'),
	__('Möt klubbens aktiva förare, ledare och tränare.', 'tandstiftet'),
	'FÖRARE'
);
?>

<!-- Quick-jump nav -->
<nav class="section section--coal" style="padding-block:var(--space-lg)">
	<div class="container">
		<div class="riders-filter" role="navigation" aria-label="<?php esc_attr_e('Hoppa till sektion','tandstiftet'); ?>">
			<a href="#division-1"  class="riders-filter__btn"><?php esc_html_e('Division 1', 'tandstiftet'); ?></a>
			<a href="#vuxna"       class="riders-filter__btn"><?php esc_html_e('Vuxna', 'tandstiftet'); ?></a>
			<a href="#ungdomar"    class="riders-filter__btn"><?php esc_html_e('Ungdomar', 'tandstiftet'); ?></a>
		</div>
	</div>
</nav>

<!-- ========================================================
     DIVISION 1 — Featured team section
     ======================================================== -->
<section class="section riders-section section--dark" id="division-1">
	<div class="container">

		<!-- Team heading -->
		<div class="team-header">
			<span class="team-header__number">1</span>
			<div class="team-header__info">
				<p class="team-header__division"><?php esc_html_e('Seriespel', 'tandstiftet'); ?></p>
				<h2 class="team-header__name"><?php esc_html_e('Division 1-laget', 'tandstiftet'); ?></h2>
				<p class="team-header__bio">
					<?php echo esc_html( get_theme_mod('tmc_div1_bio', __('Tändstiftets representationslag tävlar i Division 1. Här hittar du lagets sammansättning, ledare och förare för säsongen.', 'tandstiftet')) ); ?>
				</p>
			</div>
		</div>

		<?php
		// Team leader — rider with tmc_rider_is_leader = 1 in division-1
		$leader_query = new WP_Query([
			'post_type'      => 'tmc_rider',
			'posts_per_page' => 1,
			'meta_query'     => [[
				'key'     => 'tmc_rider_is_leader',
				'value'   => '1',
				'compare' => '=',
			]],
			'tax_query' => [[
				'taxonomy' => 'tmc_division',
				'field'    => 'slug',
				'terms'    => 'division-1',
			]],
		]);
		if ( $leader_query->have_posts() ) : $leader_query->the_post();
			$leader_role = get_post_meta(get_the_ID(), 'tmc_rider_leader_role', true) ?: __('Lagledare','tandstiftet');
		?>
		<div class="team-leader-card">
			<?php if (has_post_thumbnail()) :
				the_post_thumbnail('rider-portrait', ['class'=>'team-leader-card__photo','loading'=>'lazy']);
			endif; ?>
			<div>
				<p class="team-leader-card__role"><?php echo esc_html($leader_role); ?></p>
				<h3 class="team-leader-card__name"><?php the_title(); ?></h3>
				<div class="team-leader-card__bio"><?php the_content(); ?></div>
			</div>
		</div>
		<?php wp_reset_postdata(); endif; ?>

		<!-- Division 1 riders -->
		<?php $div1 = tmc_get_riders('division-1'); ?>
		<?php if ($div1->have_posts()) : ?>
			<div class="grid grid--4">
				<?php while ($div1->have_posts()) : $div1->the_post();
					// Skip team leader (already shown above)
					if (get_post_meta(get_the_ID(), 'tmc_rider_is_leader', true)) continue;
					tmc_rider_card(get_the_ID());
				endwhile; wp_reset_postdata(); ?>
			</div>
		<?php else : ?>
			<p style="color:var(--color-grey-mid)"><?php esc_html_e('Inga Division 1-förare publicerade ännu.','tandstiftet'); ?></p>
		<?php endif; ?>

	</div>
</section>

<!-- ========================================================
     VUXNA
     ======================================================== -->
<section class="section riders-section" id="vuxna">
	<div class="container">

		<div class="riders-section-block__heading">
			<h2 class="section-title"><?php esc_html_e('Vuxna', 'tandstiftet'); ?></h2>
			<span class="riders-section-block__heading-line"></span>
			<a href="#division-1" class="riders-filter__btn" style="flex-shrink:0">↑ <?php esc_html_e('Division 1','tandstiftet'); ?></a>
		</div>

		<p style="color:var(--color-grey-light);max-width:640px;margin-bottom:var(--space-xl);">
			<?php echo esc_html( get_theme_mod('tmc_adults_bio', __('Alla vuxna förare som tränar och tävlar med Tändstiftet MC.','tandstiftet')) ); ?>
		</p>

		<?php $adults = tmc_get_riders('vuxen'); ?>
		<?php if ($adults->have_posts()) : ?>
			<div class="grid grid--4">
				<?php while ($adults->have_posts()) : $adults->the_post();
					tmc_rider_card(get_the_ID());
				endwhile; wp_reset_postdata(); ?>
			</div>
		<?php else : ?>
			<p style="color:var(--color-grey-mid)"><?php esc_html_e('Inga vuxenförare publicerade ännu.','tandstiftet'); ?></p>
		<?php endif; ?>

	</div>
</section>

<!-- ========================================================
     UNGDOMAR
     ======================================================== -->
<section class="section riders-section section--coal" id="ungdomar">
	<div class="container">

		<div class="riders-section-block__heading">
			<h2 class="section-title"><?php esc_html_e('Ungdomar', 'tandstiftet'); ?></h2>
			<span class="riders-section-block__heading-line"></span>
			<a href="#vuxna" class="riders-filter__btn" style="flex-shrink:0">↑ <?php esc_html_e('Vuxna','tandstiftet'); ?></a>
		</div>

		<p style="color:var(--color-grey-light);max-width:640px;margin-bottom:var(--space-xl);">
			<?php echo esc_html( get_theme_mod('tmc_youth_bio', __('Framtidens förare! Ungdomsgruppen tränar regelbundet och välkomnar nya talanger.','tandstiftet')) ); ?>
		</p>

		<?php $youth = tmc_get_riders('ungdom'); ?>
		<?php if ($youth->have_posts()) : ?>
			<div class="grid grid--4">
				<?php while ($youth->have_posts()) : $youth->the_post();
					tmc_rider_card(get_the_ID());
				endwhile; wp_reset_postdata(); ?>
			</div>
		<?php else : ?>
			<p style="color:var(--color-grey-mid)"><?php esc_html_e('Inga ungdomsförare publicerade ännu.','tandstiftet'); ?></p>
		<?php endif; ?>

	</div>
</section>

<div style="text-align:center;padding-block:var(--space-xl);background:var(--color-dark)">
	<a href="#division-1" class="btn btn--outline">↑ <?php esc_html_e('Tillbaka till toppen','tandstiftet'); ?></a>
</div>

<?php get_footer(); ?>
