			</div>
		</div>

		<!--	FOOTER	-->
		<div class="row" id="nails-default-footer">
			<div class="four columns nails-footer-1">
				<small>
					<?=lang( 'nails_footer_powered_by', NAILS_VERSION )?>
				</small>
			</div>
			<div class="four columns nails-footer-2">&nbsp;</div>
			<div class="four columns nails-footer-3">&nbsp;</div>
			<div class="four columns nails-footer-4">
				<small>
					<?=lang( 'nails_footer_developed_by' )?>
				</small>
			</div>
		</div>

	</div><!--	/.container	-->

	<!-- JS HOOK -->
	<?php $this->asset->output( 'js-inline' ); ?>

	<script type="text/javascript">
	<!--//

		<?php if ( ENVIRONMENT == 'production' && site_setting( 'google_analytics_account' ) ) : ?>

		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', '<?=site_setting( 'google_analytics_account' )?>]);
		_gaq.push(['_trackPageview']);

		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();

		<?php endif; ?>

	//-->
	</script>
</body>
</html>