				<hr />

				<small class="footer" rel="tooltip-r" title="<?=lang( 'admin_rendered_in_tip' )?>">
					<?=lang( 'admin_rendered_in', '{elapsed_time}' )?>
				</small>
				<small class="footer right" rel="tooltip-l" title="<?=lang( 'admin_powered_by_tip' )?>">
					<?=lang( 'admin_powered_by', array( 'http://nailsapp.co.uk', NAILS_VERSION . ' (revision '. NAILS_REVISION . ')' ) )?>
				</small>

			</div><!--	/.content_inner	-->
		</div>

		<!--	CLEARFIX	-->
		<div class="clear"></div>

		<!--	GLOBAL JS	-->
		<?php $this->asset->output( 'js-inline' ); ?>
	</body>
</html>