<!--	CUSTOM JS	-->
<script type="text/javascript">
<!--// Hide from old browsers
	
	$(function() {
	
		$( '.add-credits' ).click( function() {
			
			$( '.add-credits-box' ).show();
			$(this).hide();
			
			return false;
		
		});
	
	});	
	
//-->
</script>

<style type="text/css">

	.specific input[type=text], textarea {
		width:250px;
	}
	.specific textarea[name=description] {
		height:250px;
	}
	
</style>

<?=form_open_multipart( 'admin/employers/edit/' . $employer->id . '/' )?>
<?=form_hidden( 'update', TRUE )?>
<?=form_hidden( 'id', $id )?>

<h1>Manage Employers &rsaquo; Edit Employer</h1>

<p>
	You can edit this employer using the form below. Please note that any modification of this employer will be shown across the website
	and will affect the internships listed under it.
</p>

<hr>

<div style="margin-left:10px;margin-right:10px;">


	<?php
	
		/**
		 * 
		 * Header boxes
		 * 
		 **/
		 
		$this->load->view( 'employers/edit/inc-notes' );
	
	?>
	
	
	<!--	LEFT HAND COLUMN	-->
	<div style="width:650px;float:left;margin-right:20px;padding-bottom:10px;">

	<?php
	
		/**
		 * 
		 * Left hand column
		 * 
		 * This content may be loaded depending on the type of user being viewed as
		 * not all information applies to all user types.
		 * 
		 **/
		 
		$this->load->view( 'employers/edit/inc-basic' );
		$this->load->view( 'employers/edit/inc-intern-reqs' );
	
	?>
	
	</div>
	
	
	<!--	RIGHT HAND COLUMN	-->
	<div style="float:left;width:280px;">
	
	
		<?php
		
			/**
			 * 
			 * Right hand column
			 * 
			 **/
			 
			$this->load->view( 'employers/edit/inc-admins' );
			$this->load->view( 'employers/edit/inc-credits' );
			$this->load->view( 'employers/edit/inc-sectors' );
		
		?>
		
	</div>
	
	<!--	CLEARFIX	-->
	<div class="clear"></div>

	</form>	
	
</div>
