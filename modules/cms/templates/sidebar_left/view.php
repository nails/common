<?php

	echo $this->load->view( 'structure/header', get_controller_data() );

	// --------------------------------------------------------------------------

	echo '<h1>Sidebar content</h1>';
	echo $sidebar;

	echo '<h1>Mainbody content</h1>';
	echo $mainbody;

	// --------------------------------------------------------------------------

	echo $this->load->view( 'structure/footer', get_controller_data() );