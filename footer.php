<?php
/**
 * Global footer.
 *
 * @package Raider
 */
?>
</main>

<footer class="site-footer">
	<div class="container site-footer__grid">
		<div class="site-footer__logo">
			<?php
			raider_picture(
				array(
					'name'         => 'logo',
					'widths'       => array( 240, 440 ),
					'sizes'        => '220px',
					'alt'          => 'Raider Production',
					'fallback_ext' => 'png',
					'dir'          => 'images/optimized',
					'width'        => 440,
					'height'       => 298,
				)
			);
			?>
			<p style="margin-top: 1rem; color:#bbb;"><?php esc_html_e( 'Lyhytvideot, mainosvideot ja someapu yrityksille.', 'raider' ); ?></p>
		</div>
		<div>
			<h3><?php esc_html_e( 'Sivut', 'raider' ); ?></h3>
			<ul>
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Etusivu', 'raider' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/hinnoittelu/' ) ); ?>"><?php esc_html_e( 'Hinnoittelu', 'raider' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/meista/' ) ); ?>"><?php esc_html_e( 'Meistä', 'raider' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/ota-yhteytta/' ) ); ?>"><?php esc_html_e( 'Ota yhteyttä', 'raider' ); ?></a></li>
			</ul>
		</div>
		<div>
			<h3>Raider Production</h3>
			<address style="font-style: normal; line-height: 1.7;">
				Päivämiehenkuja 10<br>
				41340 Laukaa<br>
				Y – 3539320-3<br>
				<a href="tel:+358407245604">040 7245604</a><br>
				<a href="mailto:tommi.parhiala@gmail.com">tommi.parhiala@gmail.com</a>
			</address>
			<div class="site-footer__social" aria-label="<?php esc_attr_e( 'Sosiaalinen media', 'raider' ); ?>">
				<a href="https://www.facebook.com/share/19aFTqKdME/" target="_blank" rel="noopener" aria-label="Facebook">
					<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z"/></svg>
				</a>
				<a href="https://www.tiktok.com/@raider.production0" target="_blank" rel="noopener" aria-label="TikTok">
					<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5.8 20.1a6.34 6.34 0 0 0 10.86-4.43V8.55a8.16 8.16 0 0 0 4.77 1.52V6.69h-1.84z"/></svg>
				</a>
				<a href="https://www.instagram.com/raider_videot" target="_blank" rel="noopener" aria-label="Instagram">
					<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.2c3.2 0 3.6 0 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.42.36 1.06.41 2.23.06 1.26.07 1.64.07 4.85s0 3.6-.07 4.85c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.42.16-1.06.36-2.23.41-1.26.06-1.64.07-4.85.07s-3.6 0-4.85-.07c-1.17-.05-1.8-.25-2.23-.41a3.7 3.7 0 0 1-1.38-.9 3.7 3.7 0 0 1-.9-1.38c-.16-.42-.36-1.06-.41-2.23C2.2 15.6 2.2 15.2 2.2 12s0-3.6.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.42-.16 1.06-.36 2.23-.41C8.4 2.2 8.8 2.2 12 2.2zm0 2.16c-3.15 0-3.5 0-4.74.07-1.07.05-1.65.23-2.04.38a3.4 3.4 0 0 0-1.26.82c-.39.39-.63.76-.82 1.26-.15.39-.33.97-.38 2.04C2.7 8.96 2.7 9.31 2.7 12c0 2.69 0 3.04.06 4.27.05 1.07.23 1.65.38 2.04.19.5.43.87.82 1.26.39.39.76.63 1.26.82.39.15.97.33 2.04.38C8.5 20.83 8.85 20.84 12 20.84s3.5 0 4.74-.06c1.07-.05 1.65-.23 2.04-.38.5-.19.87-.43 1.26-.82.39-.39.63-.76.82-1.26.15-.39.33-.97.38-2.04.06-1.23.07-1.58.07-4.27s0-3.04-.07-4.27c-.05-1.07-.23-1.65-.38-2.04a3.4 3.4 0 0 0-.82-1.26 3.4 3.4 0 0 0-1.26-.82c-.39-.15-.97-.33-2.04-.38-1.23-.06-1.58-.07-4.74-.07zm0 3.68a3.96 3.96 0 1 1 0 7.92 3.96 3.96 0 0 1 0-7.92zm0 6.53a2.57 2.57 0 1 0 0-5.14 2.57 2.57 0 0 0 0 5.14zm5.04-6.7a.93.93 0 1 1-1.86 0 .93.93 0 0 1 1.86 0z"/></svg>
				</a>
				<a href="https://www.youtube.com/@t.parhiala" target="_blank" rel="noopener" aria-label="YouTube">
					<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.6 3.6 12 3.6 12 3.6s-7.6 0-9.4.5A3 3 0 0 0 .5 6.2 31 31 0 0 0 0 12a31 31 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.1c1.8.5 9.4.5 9.4.5s7.6 0 9.4-.5a3 3 0 0 0 2.1-2.1 31 31 0 0 0 .5-5.8 31 31 0 0 0-.5-5.8zM9.6 15.6V8.4l6.3 3.6-6.3 3.6z"/></svg>
				</a>
			</div>
		</div>
	</div>
	<p class="container site-footer__legal">© <?php echo esc_html( gmdate( 'Y' ) ); ?> Raider Production Oy. <?php esc_html_e( 'Kaikki oikeudet pidätetään.', 'raider' ); ?></p>
</footer>

<script>
(function(){
  var btn=document.querySelector('.nav-toggle'),nav=document.getElementById('primary-nav');
  if(!btn||!nav)return;
  btn.addEventListener('click',function(){
    var o=nav.getAttribute('data-open')==='true';
    nav.setAttribute('data-open',String(!o));
    btn.setAttribute('aria-expanded',String(!o));
  });
})();
</script>
<?php wp_footer(); ?>
</body>
</html>
