<?php

	echo $this->load->view( 'structure/header',	$view_data );
	echo $mainbody;
	echo $this->load->view( 'structure/footer',	$view_data );