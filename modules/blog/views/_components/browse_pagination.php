<?php

	$this->load->library('pagination');

	$_config						= array();
	$_config['base_url']			= site_url( app_setting( 'url', 'blog' ) );
	$_config['total_rows']			= $pagination->total;
	$_config['per_page']			= $pagination->per_page;
	$_config['use_page_numbers']	= TRUE;
	$_config['use_rsegment']		= TRUE;
	$_config['uri_segment']			= 2;
	$_config['full_tag_open']		= '<li class="pagination">';
	$_config['full_tag_close']		= '</li>';

	$this->pagination->initialize( $_config );

	echo '<li>' . $this->pagination->create_links() . '</li>';