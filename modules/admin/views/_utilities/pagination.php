<div class="pagination">
<?php

	//	Load the library, muy importante!
	$this->load->library('pagination');

	//	Configure the Pagination Library
	//	================================

	$_config = array();

	//	The base URL is the current uri plus any existing GET params
	$_config['base_url'] = site_url( uri_string() ) . '?';

	//	Build the parameters array, use any existing GET params as the base
	parse_str( $this->input->server( 'QUERY_STRING' ), $_params );

	//	Filter out the useless ones and append to the base URL
	$_params = array_filter( $_params );
	unset( $_params['page'] );
	$_config['base_url'] .= http_build_query( $_params );

	//	Other customisations
	$_config['total_rows']				= $pagination->total_rows;
	$_config['per_page']				= isset( $pagination->per_page ) ? $pagination->per_page : 50;
	$_config['page_query_string']		= TRUE;
	$_config['query_string_segment']	= 'page';
	$_config['num_links']				= 5;
	$_config['use_page_numbers']		= TRUE;

	// --------------------------------------------------------------------------

	//	Styling and markup
	//	==================

	//	Surrounding HTML
	$_config['full_tag_open']			= '<ul>';
	$_config['full_tag_close']			= '</ul>';

	//	"First" link
	$_config['first_link']				= lang( 'action_first' );
	$_config['first_tag_open']			= '<li class="page first">';
	$_config['first_tag_close']			= '</li>';

	//	"Previous" link
	$_config['prev_link']				= '&lsaquo;';
	$_config['prev_tag_open']			= '<li class="page previous">';
	$_config['prev_tag_close']			= '</li>';

	//	"Next" link
	$_config['next_link']				= '&rsaquo;';
	$_config['next_tag_open']			= '<li class="page next">';
	$_config['next_tag_close']			= '</li>';

	//	"Last" link
	$_config['last_link']				= lang( 'action_last' );
	$_config['last_tag_open']			= '<li class="page last">';
	$_config['last_tag_close']			= '</li>';


	//	Number link markup
	$_config['num_tag_open']			= '<li class="page">';
	$_config['num_tag_close']			= '</li>';

	//	Current page markup
	$_config['cur_tag_open']			= '<li class="page current"><span class="current">';
	$_config['cur_tag_close']			= '</span></li>';

	$this->pagination->initialize( $_config );

	echo $this->pagination->create_links();

?>
<div style="clear:both"></div>
</div>