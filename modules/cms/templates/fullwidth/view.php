<?php

	echo $this->load->view( 'structure/header', get_controller_data() );
	echo $mainbody;
	echo $this->load->view( 'structure/footer', get_controller_data() );