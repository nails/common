				<footer>
					<small rel="tooltip-r" title="<?=lang( 'admin_rendered_in_tip' )?>">
						<?=lang( 'admin_rendered_in', '{elapsed_time}' )?>
					</small>
					<small class="right">
						<?=lang( 'admin_powered_by', 'http://nailsapp.co.uk' )?>
					</small>
				</footer>

			</div><!--	/.content_inner	-->
		</div>

		<!--	CLEARFIX	-->
		<div class="clear"></div>
		<div class="background">
			<div class="sidebar admin-branding-background-primary"></div>
		</div>

		<!--	GLOBAL JS	-->
		<?php $this->asset->output( 'JS-INLINE' ); ?>
	</body>
</html>