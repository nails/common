				<hr />
				
				<small class="footer" rel="tooltip-r" title="<?=lang( 'admin_rendered_in_tip' )?>">
					<?=lang( 'admin_rendered_in', '{elapsed_time}' )?>
				</small>
				<small class="footer right" rel="tooltip-l" title="<?=lang( 'admin_powered_by_tip' )?>">
					<?=lang( 'admin_powered_by', array( 'http://nailsapp.co.uk', NAILS_VERSION ) )?>
				</small>
				
			</div><!--	/.content_inner	-->
			</div><!--	/.padder	-->
		
		</div>
		
		<!--	CLEARFIX	-->
		<div class="clear"></div>
		
		<!--	GLOBAL JS	-->
		<script tyle="text/javascript">
		<!--//
		
			var _nails,_nails_admin;
			
			$(function(){
			
				//	Initialise NAILS_JS
				_nails = new NAILS_JS();
				_nails.init();
				
				//	Initialise NAILS_Admin
				_nails_admin = new NAILS_Admin();
				_nails_admin.init();
			
			});
		
		//-->
		</script>
	</body>
</html>