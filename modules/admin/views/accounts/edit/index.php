
<script type="text/javascript">
<!--// Hide from old browsers
	
	$(function() {
		
		//	clicky click fun fun
		$('.reset_pass_check').click(function(e) {
			
			if ($(this).attr('checked')) {
				
				$('#user_form .define_password').css('display','table-row');
				$('#user_form .define_password_span').css('display', 'inline');
				
			} else {
				$('#user_form .define_password').css('display','none');
				$('#user_form .define_password_span').css('display', 'none');
			}
			
		});
		
		//	Decide the starting state
		
		
		$('.reset_pass_check').each(function() {
			var id = $(this).attr('rel');
			if ($(this).attr('checked')) {
				
				$('#user_form_' + id + ' .define_password').css('display','table-row');
				$('#user_form_' + id + ' .define_password_span').css('display', 'inline');
				
			} else {
				$('#user_form_' + id + ' .define_password').css('display','none');
				$('#user_form_' + id + ' .define_password_span').css('display', 'none');
			}
		});
	
	});
	
	function generate_pw(obj, link)
	{
	
		var chars = new Array(	0,1,2,3,4,5,6,7,8,9,
								'a','b','c','d','e',
								'f','g','h','i','j',
								'k','l','m','n','o',
								'p','q','r','s','t',
								'u','v','w','x','y',
								'z','A','B','C','D',
								'E','F','G','H','I',
								'J','K','L','M','N',
								'O','P','Q','R','S',
								'T','U','V','W','X',
								'Y','Z','!',',','@',
								'$','&','*','[',']',
								'(',')','{','}','?'
							  );

		
		var str = "";
		var len = chars.length;
		for(var i=1; i<=8; i++)
		{
			str += chars[Math.floor(Math.random()*len)];
		}
		
		obj = '#' + obj;
		$(obj).val(str);
		$(link).find('span').text(': ' + str).html();
		
		//	Elegance at it's highest.... </sarcasm>
		$(link).parent().parent().next().find('input[type=checkbox]').attr('checked','checked');
		return false;
	}
	
//-->
</script>


<noscript>
	<style type="text/css">
		.define_password { display:table-row !important; }
		.define_password_span { display:inline !important; }
	</style>
</noscript>



<!--	TABLE STYLES	-->
<style type="text/css">

	#preview_image
	{
		box-shadow: 0px 0px 8px #ccc;
		margin-left: 2px;
		margin-bottom:10px;
	}
	
	.mceEditor { 
		margin-bottom:10px;
		display:block;
	}
	.specific input[type=text], textarea {
		width:192px;
	}
	.specific textarea[name=description] {
		height:192px;
	}

</style>

<!--	START TABLE	-->


<?php

	echo form_open( 'admin/accounts/edit/' . $user_edit->id . $return_string );
	echo form_hidden( 'id', $user_edit->id );
	echo form_hidden( 'email_orig', $user_edit->email );
	echo form_hidden( 'username_orig', $user_edit->username );
	
?>

<h1>Manage Account &rsaquo; <?=$user_edit->first_name?> <?=$user_edit->last_name?></h1>

<p>
	You can edit some of this member's details below.
</p>

<hr>

<div style="margin-left:10px;margin-right:10px;" id="user_form">
	
	
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
		 
		$this->load->view( 'accounts/edit/inc-basic' );
		$this->load->view( 'accounts/edit/inc-meta' );
	
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
			 
			$this->load->view( 'accounts/edit/inc-profile-img' );
			$this->load->view( 'accounts/edit/inc-login-as' );
			$this->load->view( 'accounts/edit/inc-banunban' );
		
		?>
		
	</div>
	
	<!--	CLEARFIX	-->
	<div class="clear"></div>

</div>
			

<?=form_close()?>