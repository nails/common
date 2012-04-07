<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Profanity
*
* Created:		01/02/2012
* Modified:		01/02/2012
*
* Description:	Quickly clean profanities from content
*
* Requirements:	-
*
* Change log:	-
* 
*/

class Profanity {
	
	private $ci;
	private $bad_words;
	private $replacement;
	private $use_stars;
	
	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 **/
	public function __construct()
	{
		$this->ci =& get_instance();
		$this->bad_words	= NULL;
		$this->replacement	= '[censored]';
		$this->use_stars	= FALSE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Clean a string from bad words
	 *
	 * @access	public
	 * @param	string	$str	The string to clean
	 * @return	string
	 * @author	Pablo
	 **/
	public function clean( $str )
	{
		if ( ! $this->bad_words )
			$this->_get_bad_words();
		
		// --------------------------------------------------------------------------
		
		$_pattern = '/\b(' . implode( $this->bad_words, '|' ) . ')\b/i';
		
		if ( $this->use_stars ) :
		
			return preg_replace( $_pattern . 'e', 'str_repeat( \'*\', strlen(\'$1\'))', $str  );
		
		else :
		
			return preg_replace( $_pattern, $this->replacement, $str  );
			
		endif;
		
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Determine wether a string is clean or not
	 *
	 * @access	public
	 * @param	string	$str	The string to clean
	 * @return	bool
	 * @author	Pablo
	 **/
	public function is_clean( $str )
	{
		if ( ! $this->bad_words )
			$this->_get_bad_words();
		
		// --------------------------------------------------------------------------
		
		$_pattern	= '/\b(' . implode( $this->bad_words, '|' ) . ')\b/i';
		$_match		= preg_match( $_pattern, $str  );
		
		return ( $_match ) ? FALSE : TRUE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Clean a string from bad words
	 *
	 * @access	private
	 * @param	none
	 * @return	void
	 * @author	Pablo
	 **/
	private function _get_bad_words()
	{
		$_bad_words = $this->ci->db->get( 'profanity_list' )->result();
		
		foreach ( $_bad_words AS $word ) :
		
			$word = trim( $word->word );
			$word = str_replace( '\\', '\\\\', $word );
			$word = str_replace( '.', '\.', $word );
			$word = str_replace( '(', '\(', $word );
			$word = str_replace( ')', '\)', $word );
			$word = str_replace( '[', '\[', $word );
			$word = str_replace( ']', '\]', $word );
			$word = str_replace( '-', '\-', $word );
			$word = str_replace( '*', '\*', $word );
			$word = str_replace( '+', '\+', $word );
			$word = str_replace( '/', '\/', $word );
			$word = str_replace( '|', '\|', $word );
			
			$this->bad_words[] = $word;
		
		endforeach;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Turn stars on or off
	 *
	 * @access	public
	 * @param	bool	$bool	True or false wether to use stars or a replacement
	 * @return	void
	 * @author	Pablo
	 **/
	public function use_stars( $bool )
	{
		$this->use_stars = (bool) $bool;
	}
	
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Set the replacement string
	 *
	 * @access	public
	 * @param	string	$str	The string to use as the replacement
	 * @return	void
	 * @author	Pablo
	 **/
	public function use_as_replacement( $str )
	{
		$this->replacement = $str;
	}
}

/* End of file profanity.php */
/* Location: ./application/libraries/profanity.php */