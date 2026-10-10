<footer class="site-footer" role="contentinfo">

	<div class="site-footer__main">

		<!-- Brand column -->
		<div class="footer-brand">
			<?php if ( has_custom_logo() ) : the_custom_logo(); else : ?>
				<p style="font-family:var(--font-display);font-size:2rem;color:var(--color-gold);"><?php bloginfo('name'); ?></p>
			<?php endif; ?>
			<p class="footer-brand__desc">
				<?php echo esc_html( get_theme_mod( 'tmc_footer_desc', __( 'Speedway &amp; flat track i Malmö sedan 1924. Svemo-ansluten klubb för förare i alla åldrar.', 'tandstiftet' ) ) ); ?>
			</p>
			<div class="footer-social">
				<?php
				$facebook  = get_theme_mod('tmc_facebook');
				$instagram = get_theme_mod('tmc_instagram');
				$youtube   = get_theme_mod('tmc_youtube');
				$svg_fb = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>';
				$svg_ig = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" style="fill:var(--color-black)"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5" style="stroke:var(--color-black);stroke-width:2"/></svg>';
				$svg_yt = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 0 0 1.46 6.42 29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58 2.78 2.78 0 0 0 1.95 1.96C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.96-1.96A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" style="fill:var(--color-black)"/></svg>';
				if ($facebook)  echo '<a href="' . esc_url($facebook) . '" class="footer-social__link" target="_blank" rel="noopener" aria-label="Facebook">' . $svg_fb . '</a>';
				if ($instagram) echo '<a href="' . esc_url($instagram) . '" class="footer-social__link" target="_blank" rel="noopener" aria-label="Instagram">' . $svg_ig . '</a>';
				if ($youtube)   echo '<a href="' . esc_url($youtube) . '" class="footer-social__link" target="_blank" rel="noopener" aria-label="YouTube">' . $svg_yt . '</a>';
				?>
			</div>
		</div>

		<!-- Footer nav columns -->
		<?php
		$footer_menus = [
			'footer-om'          => __( 'Om klubben', 'tandstiftet' ),
			'footer-trackracing' => __( 'Trackracing', 'tandstiftet' ),
			'footer-besok'       => __( 'Besök oss', 'tandstiftet' ),
		];
		foreach ( $footer_menus as $location => $heading ) :
			if ( has_nav_menu( $location ) ) : ?>
			<div class="footer-nav-col">
				<h3 class="footer-nav-col__title"><?php echo esc_html( $heading ); ?></h3>
				<?php wp_nav_menu( [
					'theme_location' => $location,
					'container'      => false,
					'depth'          => 1,
					'fallback_cb'    => false,
					'menu_class'     => 'footer-nav-col__list',
				] ); ?>
			</div>
		<?php endif;
		endforeach; ?>

		<!-- Kontaktinfo kolumn -->
		<div class="footer-nav-col">
			<h3 class="footer-nav-col__title"><?php esc_html_e('Kontakt', 'tandstiftet'); ?></h3>
			<ul class="footer-nav-col__list footer-contact-list">
				<?php
				$phone   = get_theme_mod('tmc_contact_phone');
				$email   = get_theme_mod('tmc_contact_email');
				$address = get_theme_mod('tmc_contact_address');
				if ($address) echo '<li><span>' . esc_html($address) . '</span></li>';
				if ($phone)   echo '<li><a href="tel:' . esc_attr(preg_replace('/\s+/','',$phone)) . '">' . esc_html($phone) . '</a></li>';
				if ($email)   echo '<li><a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a></li>';
				?>
			</ul>
		</div>

	</div><!-- /.site-footer__main -->

	<div class="site-footer__bottom">
		<div class="site-footer__bottom-inner">
			<span class="site-footer__copy">
				&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>.
				<?php esc_html_e('Alla rättigheter förbehållna.', 'tandstiftet'); ?>
				&nbsp;·&nbsp;
				<?php esc_html_e('Svemo-ansluten sedan 1924', 'tandstiftet'); ?>
			</span>
			<div class="footer-bottom-links">
				<a href="https://www.svemo.se" target="_blank" rel="noopener">Svemo</a>
				<a href="https://www.bingolotto.se/kop-bingolott/?a=f8fb3d86-334f-dc11-b781-005056a666aa" target="_blank" rel="noopener"><?php esc_html_e('Stöd via Bingolotto', 'tandstiftet'); ?></a>
			</div>
		</div>
	</div>

</footer><!-- /.site-footer -->

<?php wp_footer(); ?>
</body>
</html>
