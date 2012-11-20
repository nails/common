<?php

	echo form_open_multipart( 'admin/accounts/edit/' . $user_edit->id . $return_string );
	echo form_hidden( 'id', $user_edit->id );
	echo form_hidden( 'email_orig', $user_edit->email );
	echo form_hidden( 'username_orig', $user_edit->username );
	
	echo '<h1>Manage Account &rsaquo; ' . $user_edit->first_name . ' ' . $user_edit->last_name .'</h1>';

	$this->load->view( 'accounts/edit/inc-actions' );
	$this->load->view( 'accounts/edit/inc-basic' );
	$this->load->view( 'accounts/edit/inc-meta' );
	$this->load->view( 'accounts/edit/inc-profile-img' );
	$this->load->view( 'accounts/edit/inc-social-media' );
	
	
	echo '<p>' . form_submit( 'submit', 'Save Changes' ) . '</p>';
	
	echo form_close();