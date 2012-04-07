<!--	CUSTOM JS	-->
<script type="text/javascript">
<!--// Hide from old browsers
	
	$(function() {
	
		$( 'a.log-everyone-out' ).click(
		
			function() {
				
				return confirm( 'This will log all users (except you, including other admins) out of the system. Effective immediately.\n\nContinue?' );
				
			}
		
		);
	
	});
	
//-->
</script>

<!--	PAGE TITLE	-->
<section>
	<h1>Session Management</h1>	
</section>

<p>
	Every user interacting with Intern Avenue generates a session; these are detailed below.
	<span class="right">
		<?=anchor( 'admin/accounts/sessions/force_logout', 'LOG ALL USERS OUT', 'class="log-everyone-out a-button a-button-red a-button-small"')?>
	</span>
</p>

<!--	IMPORT TABLE	-->
<?php $this->load->view( 'accounts/session/session_table' ); ?>