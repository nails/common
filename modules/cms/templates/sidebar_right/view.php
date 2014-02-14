<?php

	echo $this->load->view( 'structure/header',	$view_data );

	// --------------------------------------------------------------------------

	echo '<h1>Mainbody content</h1>';
	echo $mainbody;

	echo '<h1>Sidebar content</h1>';
	echo $sidebar;

	// --------------------------------------------------------------------------

	echo $this->load->view( 'structure/footer',	$view_data );