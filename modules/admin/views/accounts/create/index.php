<?php

	echo form_open();

	echo '<p>' . lang( 'accounts_create_intro' ) .'</p>';
	
	$this->load->view( 'accounts/create/inc-basic' );
	
	echo '<p>' . form_submit( 'submit', lang( 'accounts_create_submit' ) ) . '</p>';
	
	echo form_close();