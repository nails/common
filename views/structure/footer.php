			</div>
		</div>

		<!--	FOOTER	-->
		<div class="row" id="nails-default-footer">
			<div class="four columns nails-footer-1">
				<small>
					Powered by <?=anchor( 'http://nailsapp.co.uk', 'Nails. v'. NAILS_VERSION )?>
				</small>
			</div>
			<div class="four columns nails-footer-2">&nbsp;</div>
			<div class="four columns nails-footer-3">&nbsp;</div>
			<div class="four columns nails-footer-4">
				<small>
					Developed by <?=anchor( 'http://shedcollective.org', 'Shed Collective' ) ?> &copy; <?=date( 'Y' )?>
				</small>
			</div>
		</div>
		
	</div><!--	/.container	-->
	<script tyle="text/javascript">
	<!--//
	
		var _nails;
		
		$(function(){
		
			//	Initialise Nails_JS
			_nails = new Nails_JS();
			_nails.init();
		
		});
	
	//-->
	</script>
</body>
</html>