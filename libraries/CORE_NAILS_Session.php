<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CORE_NAILS_Session extends CI_Session
{

	/**
	 * Keeps existing flashdata available to next request.
	 * 
	 * http://codeigniter.com/forums/viewthread/104392/#917834
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 **/
	public function keep_flashdata( $key = NULL )
	{
		// 'old' flashdata gets removed.  Here we mark all
		// flashdata as 'new' to preserve it from _flashdata_sweep()
		// Note the function will NOT return FALSE if the $key
		// provided cannot be found, it will retain ALL flashdata
		
		if ( $key === NULL ) :
		
			foreach ( $this->userdata as $k => $v ) :
			
				$old_flashdata_key = $this->flashdata_key . ':old:';
				
				if ( strpos( $k, $old_flashdata_key ) !== FALSE ) :
				
					$new_flashdata_key = $this->flashdata_key . ':new:';
					$new_flashdata_key = str_replace( $old_flashdata_key, $new_flashdata_key, $k );
					$this->set_userdata( $new_flashdata_key, $v );
					
				endif;
				
			endforeach;
			
			return TRUE;
			
		elseif ( is_array( $key ) ) :
		
			foreach ( $key as $k ) :
			
				$this->keep_flashdata( $k );
				
			endforeach;
			
		endif;
		
		// --------------------------------------------------------------------------
		
		$old_flashdata_key = $this->flashdata_key.':old:' . $key;
		$value = $this->userdata( $old_flashdata_key );
		
		// --------------------------------------------------------------------------
		
		$new_flashdata_key = $this->flashdata_key.':new:' . $key;
		$this->set_userdata( $new_flashdata_key, $value );
	}
	
}

/* End of file NAILS_Session.php */
/* Location: ./application/libraries/NAILS_Session.php */