<?php

	echo form_open();

	echo '<p>Create a new user by completing the following basic information and clicking \'Create User\' below. You will be given the opportunity to edit the user once the basic account has been created.</p>';
	
	$this->load->view( 'accounts/create/inc-basic' );
	
	echo '<p>' . form_submit( 'submit', 'Create User' ) . '</p>';
	
	echo form_close();