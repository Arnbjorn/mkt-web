<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php
/* Header content — all editable under Utseende → Anpassa → Header & meny. */
$brand_name    = get_theme_mod( 'tmc_brand_short',   'Tändstiftet' );
$brand_tagline = get_theme_mod( 'tmc_brand_tagline', 'Malmö · Sedan 1924' );
$cta_text      = get_theme_mod( 'tmc_nav_cta_text',  'Prova på →' );
$cta_url       = get_theme_mod( 'tmc_nav_cta_url',   '' );
if ( $cta_text && ! $cta_url ) {
	$pp      = get_page_by_path( 'prova-pa' );
	$cta_url = $pp ? get_permalink( $pp->ID ) : home_url( '/prova-pa/' );
}
$logo_id = get_theme_mod( 'custom_logo' );
?>

<header class="site-header" role="banner">

	<div class="site-header__inner">

		<a href="<?php echo esc_url( home_url('/') ); ?>" class="site-branding" aria-label="<?php echo esc_attr( get_bloginfo('name') ); ?>">
			<?php if ( $logo_id ) {
				echo wp_get_attachment_image( $logo_id, 'full', false, [ 'class' => 'site-branding__logo', 'alt' => '' ] );
			} ?>
			<span class="site-branding__text">
				<span class="site-branding__name"><?php echo esc_html( $brand_name ?: get_bloginfo('name') ); ?></span>
				<?php if ( $brand_tagline ) : ?>
					<span class="site-branding__tagline"><?php echo esc_html( $brand_tagline ); ?></span>
				<?php endif; ?>
			</span>
		</a>

		<nav class="primary-nav" id="primary-nav" aria-label="<?php esc_attr_e('Primär navigation', 'tandstiftet'); ?>">
			<?php wp_nav_menu( [
				'theme_location' => 'primary',
				'container'      => false,
				'walker'         => new TMC_Walker_Nav(),
				'fallback_cb'    => function() {
					echo '<ul>';
					echo '<li><a href="' . esc_url(home_url('/')) . '">' . esc_html__('Start', 'tandstiftet') . '</a></li>';
					echo '<li><a href="' . esc_url(home_url('/forare-och-lag/')) . '">' . esc_html__('Förare & Lag', 'tandstiftet') . '</a></li>';
					echo '<li><a href="' . esc_url(home_url('/prova-pa/')) . '">' . esc_html__('Prova På', 'tandstiftet') . '</a></li>';
					echo '<li><a href="' . esc_url(home_url('/sliding/')) . '">' . esc_html__('Sliding', 'tandstiftet') . '</a></li>';
					echo '</ul>';
				},
			] ); ?>
		</nav>

		<?php if ( $cta_text && $cta_url ) : ?>
			<a href="<?php echo esc_url( $cta_url ); ?>" class="site-header__cta"><?php echo esc_html( $cta_text ); ?></a>
		<?php endif; ?>

		<button class="nav-toggle" id="nav-toggle" aria-label="<?php esc_attr_e('Öppna meny', 'tandstiftet'); ?>" aria-expanded="false" aria-controls="primary-nav">
			<span></span>
			<span></span>
			<span></span>
		</button>

	</div><!-- /.site-header__inner -->

</header><!-- /.site-header -->
