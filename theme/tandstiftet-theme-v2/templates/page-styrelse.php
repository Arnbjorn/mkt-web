<?php
/**
 * Template Name: Styrelse
 * Description: Styrelsemedlemmar med ordförande/vice högst upp, övrig styrelse i grid, valberedning utan foto längst ned.
 *
 * @package tandstiftet
 */
get_header();
tmc_page_header( __( 'Styrelse', 'tandstiftet' ), '', 'STYRELSE' );

// Fetch all board members ordered by tmc_board_order
$all_members = new WP_Query( [
	'post_type'      => 'tmc_board',
	'posts_per_page' => -1,
	'meta_key'       => 'tmc_board_order',
	'orderby'        => 'meta_value_num',
	'order'          => 'ASC',
] );

// Separate into three groups
$featured    = []; // Ordförande / Vice
$board       = []; // Övrig styrelse
$nomination  = []; // Valberedning

if ( $all_members->have_posts() ) {
	while ( $all_members->have_posts() ) {
		$all_members->the_post();
		$type = get_post_meta( get_the_ID(), 'tmc_board_type', true ) ?: 'board';
		if ( $type === 'featured' )   $featured[]   = get_the_ID();
		elseif ( $type === 'nomination' ) $nomination[] = get_the_ID();
		else                          $board[]       = get_the_ID();
	}
	wp_reset_postdata();
}
?>

<div style="background:var(--color-dark)">

<?php if ( $featured ) : ?>
<!-- ═══════════════════════════════════════════════════
     ORDFÖRANDE / VICE — stor featured layout
     ═══════════════════════════════════════════════════ -->
<section class="section" style="padding-bottom:0">
	<div class="container">
		<div class="section-header">
			<div class="label" style="margin-bottom:0.4rem"><?php esc_html_e( 'Ledarskap', 'tandstiftet' ); ?></div>
			<h2 class="section-title"><?php esc_html_e( 'Ordförande & Vice', 'tandstiftet' ); ?></h2>
		</div>
		<div class="board-featured-grid">
			<?php foreach ( $featured as $pid ) :
				$role  = get_post_meta( $pid, 'tmc_board_role',  true );
				$email = get_post_meta( $pid, 'tmc_board_email', true );
				$phone = get_post_meta( $pid, 'tmc_board_phone', true );
				$bio   = get_post_field( 'post_content', $pid );
			?>
			<article class="board-featured-card corner-bracket">
				<div class="board-featured-card__photo-wrap">
					<?php if ( has_post_thumbnail( $pid ) ) :
						echo get_the_post_thumbnail( $pid, 'rider-portrait', [ 'class' => 'board-featured-card__photo', 'loading' => 'lazy' ] );
					else : ?>
						<div class="board-featured-card__photo board-featured-card__photo--placeholder">
							<svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="rgba(212,160,23,0.3)" stroke-width="1"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
						</div>
					<?php endif; ?>
				</div>
				<div class="board-featured-card__body">
					<?php if ( $role ) : ?>
						<div class="label" style="margin-bottom:0.5rem"><?php echo esc_html( $role ); ?></div>
					<?php endif; ?>
					<h3 class="board-featured-card__name"><?php echo esc_html( get_the_title( $pid ) ); ?></h3>
					<?php if ( $bio ) : ?>
						<p class="board-featured-card__bio"><?php echo wp_kses_post( wpautop( $bio ) ); ?></p>
					<?php endif; ?>
					<?php if ( $email || $phone ) : ?>
						<div class="board-featured-card__contact">
							<?php if ( $email ) : ?>
								<a href="mailto:<?php echo esc_attr( $email ); ?>" class="board-contact-link">
									<span class="board-contact-link__icon">✉</span> <?php echo esc_html( $email ); ?>
								</a>
							<?php endif; ?>
							<?php if ( $phone ) : ?>
								<a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $phone ) ); ?>" class="board-contact-link">
									<span class="board-contact-link__icon">☎</span> <?php echo esc_html( $phone ); ?>
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>


<?php if ( $board ) : ?>
<!-- ═══════════════════════════════════════════════════
     ÖVRIG STYRELSE — kortare grid med foto
     ═══════════════════════════════════════════════════ -->
<section class="section section--coal">
	<div class="container">
		<div class="section-header">
			<div class="label" style="margin-bottom:0.4rem"><?php esc_html_e( 'Funktionärer', 'tandstiftet' ); ?></div>
			<h2 class="section-title"><?php esc_html_e( 'Övrig styrelse', 'tandstiftet' ); ?></h2>
		</div>
		<div class="board-grid">
			<?php foreach ( $board as $pid ) :
				$role  = get_post_meta( $pid, 'tmc_board_role',  true );
				$email = get_post_meta( $pid, 'tmc_board_email', true );
				$phone = get_post_meta( $pid, 'tmc_board_phone', true );
				$bio   = get_post_field( 'post_content', $pid );
			?>
			<article class="board-card">
				<div class="board-card__photo-wrap">
					<?php if ( has_post_thumbnail( $pid ) ) :
						echo get_the_post_thumbnail( $pid, 'rider-card', [ 'class' => 'board-card__photo', 'loading' => 'lazy' ] );
					else : ?>
						<div class="board-card__photo board-card__photo--placeholder">
							<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="rgba(212,160,23,0.2)" stroke-width="1"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
						</div>
					<?php endif; ?>
				</div>
				<div class="board-card__body">
					<?php if ( $role ) : ?>
						<div class="label" style="font-size:0.7rem;margin-bottom:0.3rem"><?php echo esc_html( $role ); ?></div>
					<?php endif; ?>
					<h3 class="board-card__name"><?php echo esc_html( get_the_title( $pid ) ); ?></h3>
					<?php if ( $bio ) : ?>
						<p class="board-card__bio"><?php echo esc_html( wp_trim_words( $bio, 20 ) ); ?></p>
					<?php endif; ?>
					<?php if ( $email ) : ?>
						<a href="mailto:<?php echo esc_attr( $email ); ?>" class="board-contact-link board-contact-link--sm">
							✉ <?php echo esc_html( $email ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $phone ) : ?>
						<a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $phone ) ); ?>" class="board-contact-link board-contact-link--sm">
							☎ <?php echo esc_html( $phone ); ?>
						</a>
					<?php endif; ?>
				</div>
			</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>


<?php if ( $nomination ) : ?>
<!-- ═══════════════════════════════════════════════════
     VALBEREDNING — ingen bild, enkel lista
     ═══════════════════════════════════════════════════ -->
<section class="section">
	<div class="container">
		<div class="section-header">
			<div class="label" style="margin-bottom:0.4rem"><?php esc_html_e( 'Val', 'tandstiftet' ); ?></div>
			<h2 class="section-title"><?php esc_html_e( 'Valberedning', 'tandstiftet' ); ?></h2>
		</div>
		<div class="nomination-list">
			<?php foreach ( $nomination as $pid ) :
				$role  = get_post_meta( $pid, 'tmc_board_role',  true );
				$email = get_post_meta( $pid, 'tmc_board_email', true );
				$phone = get_post_meta( $pid, 'tmc_board_phone', true );
			?>
			<div class="nomination-item">
				<div class="nomination-item__left">
					<span class="nomination-item__name"><?php echo esc_html( get_the_title( $pid ) ); ?></span>
					<?php if ( $role ) : ?>
						<span class="nomination-item__role"><?php echo esc_html( $role ); ?></span>
					<?php endif; ?>
				</div>
				<div class="nomination-item__right">
					<?php if ( $email ) : ?>
						<a href="mailto:<?php echo esc_attr( $email ); ?>" class="board-contact-link board-contact-link--sm">
							✉ <?php echo esc_html( $email ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $phone ) : ?>
						<a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $phone ) ); ?>" class="board-contact-link board-contact-link--sm">
							☎ <?php echo esc_html( $phone ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if ( ! $featured && ! $board && ! $nomination ) : ?>
<section class="section">
	<div class="container" style="text-align:center">
		<p style="color:var(--color-grey-mid)"><?php esc_html_e( 'Inga styrelsemedlemmar publicerade ännu.', 'tandstiftet' ); ?></p>
	</div>
</section>
<?php endif; ?>

</div><!-- /background wrapper -->

<?php get_footer(); ?>
