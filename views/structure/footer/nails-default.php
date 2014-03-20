			<hr />
			<div class="row">
				<p class="text-center">
					<small>
						&copy; <?=APP_NAME?> <?=date( 'Y' ) == '2014' ? '2014' : '2014-' . date( 'Y' )?>
						<br />
						<?=lang( 'nails_footer_powered_by', NAILS_VERSION )?>
					</small>
				</p>
			</div><!-- /.row -->
		</div><!-- /.container -->
		<script type="text/javascript" href="<?=NAILS_ASSETS_URL . 'bower_components/jquery/dist/jquery.min.js'?>"></script>
		<script type="text/javascript" href="<?=NAILS_ASSETS_URL . 'bower_components/bootstrap/dist/js/bootstrap.min.js'?>"></script>
		<?php

			$this->asset->output( 'js' );
			$this->asset->output( 'js-inline' );

			if ( ENVIRONMENT == 'production' && site_setting( 'google_analytics_account' ) ) :

				?>
				<script type="text/javascript">
				<!--//

					var _gaq = _gaq || [];
					_gaq.push(['_setAccount', '<?=site_setting( 'google_analytics_account' )?>]);
					_gaq.push(['_trackPageview']);

					(function() {
						var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
						ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
						var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
					})();

				//-->
				</script>
				<?php

			endif;

		?>
	</body>
</html>