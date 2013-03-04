<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CORE_NAILS_Lang extends MX_Lang {
	
	/**
	 * Overriding the default line() method so that parameters can be specified
	 *
	 * @access	public
	 * @param	string	$line	the language line
	 * @param	array	$params	any parameters to sub in
	 * @return	string
	 */
	public function line( $line = '', $params = NULL )
	{
		if ( is_null( $params ) )
			return parent::line( $line );
		
		//	We have some parameters, sub 'em in or the unicorns will die.
		$line = parent::line( $line );
		
		if ( $line !== FALSE ) :
		
			if ( is_array( $params ) ) :
			
				$line = vsprintf( $line, $params );
				
			else :
			
				$line = sprintf( $line, $params );
				
			endif;
			
		endif;
		
		return $line;
	}
}

/* End of file NAILS_Lang.php */
/* Location: ./application/core/NAILS_Lang.php */