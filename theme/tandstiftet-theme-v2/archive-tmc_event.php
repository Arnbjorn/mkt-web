<?php
/**
 * Archive template for tmc_event
 *
 * @package tandstiftet
 */
get_header();

tmc_page_header(
	__('Tävlingar & Evenemang', 'tandstiftet'),
	__('Fullständig kalender för säsongen.', 'tandstiftet'),
	'KALENDER'
);
?>

<div class="section section--dark">
<div class="container">

	<?php
	// Split into upcoming and past
	$today     = date('Y-m-d');
	$upcoming  = new WP_Query([
		'post_type'      => 'tmc_event',
		'posts_per_page' => -1,
		'meta_key'       => 'tmc_event_date',
		'orderby'        => 'meta_value',
		'order'          => 'ASC',
		'meta_query'     => [[ 'key' => 'tmc_event_date', 'value' => $today, 'compare' => '>=', 'type' => 'DATE' ]],
	]);
	$past = new WP_Query([
		'post_type'      => 'tmc_event',
		'posts_per_page' => -1,
		'meta_key'       => 'tmc_event_date',
		'orderby'        => 'meta_value',
		'order'          => 'DESC',
		'meta_query'     => [[ 'key' => 'tmc_event_date', 'value' => $today, 'compare' => '<', 'type' => 'DATE' ]],
	]);
	?>

	<!-- UPCOMING -->
	<h2 class="section-title" style="margin-bottom:var(--space-xl)"><?php esc_html_e('Kommande', 'tandstiftet'); ?></h2>

	<?php if ($upcoming->have_posts()) : ?>
	<table class="events-table">
		<thead>
			<tr>
				<th><?php esc_html_e('Datum', 'tandstiftet'); ?></th>
				<th><?php esc_html_e('Tävling', 'tandstiftet'); ?></th>
				<th><?php esc_html_e('Plats', 'tandstiftet'); ?></th>
				<th><?php esc_html_e('Typ', 'tandstiftet'); ?></th>
				<th><?php esc_html_e('Info', 'tandstiftet'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php while ($upcoming->have_posts()) : $upcoming->the_post();
			$date     = get_post_meta(get_the_ID(), 'tmc_event_date', true);
			$time     = get_post_meta(get_the_ID(), 'tmc_event_time', true);
			$location = get_post_meta(get_the_ID(), 'tmc_event_location', true);
			$price    = get_post_meta(get_the_ID(), 'tmc_event_price', true);
			$types    = get_the_terms(get_the_ID(), 'tmc_event_type');
			$type_str = ($types && !is_wp_error($types)) ? implode(', ', wp_list_pluck($types, 'name')) : '';
		?>
			<tr>
				<td data-label="<?php esc_attr_e('Datum','tandstiftet'); ?>">
					<?php if ($date) echo esc_html(date_i18n('j M Y', strtotime($date))); ?>
					<?php if ($time) echo '<br><small style="color:var(--color-grey-mid)">' . esc_html($time) . '</small>'; ?>
				</td>
				<td data-label="<?php esc_attr_e('Tävling','tandstiftet'); ?>">
					<strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong>
				</td>
				<td data-label="<?php esc_attr_e('Plats','tandstiftet'); ?>"><?php echo esc_html($location); ?></td>
				<td data-label="<?php esc_attr_e('Typ','tandstiftet'); ?>"><?php echo esc_html($type_str); ?></td>
				<td data-label="<?php esc_attr_e('Info','tandstiftet'); ?>">
					<span style="color:var(--color-grey-light)"><?php if ($price) echo esc_html($price) . ' · '; esc_html_e('säljs på plats','tandstiftet'); ?></span>
				</td>
			</tr>
		<?php endwhile; wp_reset_postdata(); ?>
		</tbody>
	</table>
	<?php else : ?>
		<p style="color:var(--color-grey-mid);text-align:center;padding-block:var(--space-xl);">
			<?php esc_html_e('Inga kommande tävlingar publicerade än. Håll utkik!','tandstiftet'); ?>
		</p>
	<?php endif; ?>

	<!-- PAST -->
	<?php if ($past->have_posts()) : ?>
	<h2 class="section-title" style="margin-top:var(--space-2xl);margin-bottom:var(--space-xl);opacity:0.5"><?php esc_html_e('Resultat & Tidigare tävlingar', 'tandstiftet'); ?></h2>
	<table class="events-table" style="opacity:0.6">
		<thead>
			<tr>
				<th><?php esc_html_e('Datum','tandstiftet'); ?></th>
				<th><?php esc_html_e('Tävling','tandstiftet'); ?></th>
				<th><?php esc_html_e('Plats','tandstiftet'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php while ($past->have_posts()) : $past->the_post();
			$date     = get_post_meta(get_the_ID(), 'tmc_event_date', true);
			$location = get_post_meta(get_the_ID(), 'tmc_event_location', true);
		?>
			<tr>
				<td data-label="<?php esc_attr_e('Datum','tandstiftet'); ?>"><?php if($date) echo esc_html(date_i18n('j M Y', strtotime($date))); ?></td>
				<td data-label="<?php esc_attr_e('Tävling','tandstiftet'); ?>"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></td>
				<td data-label="<?php esc_attr_e('Plats','tandstiftet'); ?>"><?php echo esc_html($location); ?></td>
			</tr>
		<?php endwhile; wp_reset_postdata(); ?>
		</tbody>
	</table>
	<?php endif; ?>

</div>
</div>

<?php get_footer(); ?>
