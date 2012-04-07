<!--	CUSTOM JS	-->
<script type="text/javascript">
<!--// Hide from old browsers
	
	$(function() {
	
		$( 'a.activate' ).click( function() {
		
			return confirm( 'This will activate the employer. No credits will be issued automatically. You must do this manually by editing their account.\n\nContinue?' );
		
		});
	
	});	
	
//-->
</script>


<h1>
	All Employers
	<?=( isset( $page->search ) && $page->search !== FALSE) ? ' (search for "' . $page->search . '" returned ' . count( $employers ) . ' results)' : NULL ?>
	
</h1>

<!--	LOAD EMPLOYER TABLE	-->
<?php $this->load->view( 'employers/employer_table' )?>