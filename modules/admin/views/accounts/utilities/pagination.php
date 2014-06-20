<div class="pagination">
<?php

	$this->load->library('pagination');
	
	$_config							= array();
	
	$_config['base_url']				= site_url( uri_string() ) . '?';
	
	if ( $this->input->get( 'search' ) )
		$_config['base_url'] .= '&search=' . urlencode( $this->input->get( 'search' ) );
		
	if ( $this->input->get( 'sort' ) )
		$_config['base_url'] .= '&sort=' . urlencode( $this->input->get( 'sort' ) );
		
	if ( $this->input->get( 'order' ) )
		$_config['base_url'] .= '&order=' . urlencode( $this->input->get( 'order' ) );
		
	if ( $this->input->get( 'per_page' ) )
		$_config['base_url'] .= '&per_page=' . urlencode( $this->input->get( 'per_page' ) );	
		
	if ( $this->input->get( 'filter' ) )
		$_config['base_url'] .= '&filter=' . urlencode( $this->input->get( 'filter' ) );	
		
	$_config['total_rows']				= $users->pagination->total_results;
	$_config['per_page']				= $this->input->get( 'per_page' ) ? $this->input->get( 'per_page' ) : 50; 
	$_config['page_query_string']		= TRUE;
	$_config['query_string_segment']	= 'offset';
	$_config['num_links']				= 5;
	
	//	Customising
	$_config['full_tag_open']			= '<ul>';
	$_config['full_tag_close']			= '</ul>';
	
	$_config['first_link']				= lang( 'action_first' );
	$_config['first_tag_open']			= '<li class="page first">';
	$_config['first_tag_close']			= '</li>';
	
	$_config['prev_link']				= '&lsaquo;';
	$_config['prev_tag_open']			= '<li class="page previous">';
	$_config['prev_tag_close']			= '</li>';
	
	$_config['num_tag_open']			= '<li class="page">';
	$_config['num_tag_close']			= '</li>';
	
	$_config['cur_tag_open']			= '<li class="page current"><span class="current">';
	$_config['cur_tag_close']			= '</span></li>';
	
	$_config['next_link']				= '&rsaquo;';
	$_config['next_tag_open']			= '<li class="page next">';
	$_config['next_tag_close']			= '</li>';
	
	$_config['last_link']				= lang( 'action_last' );
	$_config['last_tag_open']			= '<li class="page last">';
	$_config['last_tag_close']			= '</li>';
	
	$this->pagination->initialize( $_config ); 
	
	echo $this->pagination->create_links();

?>
</div>