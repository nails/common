<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Possessionise
 *
 * Correctly adds possession to a word
 *
 * @access	public
 * @param	string
 * @return	string
 */
if ( ! function_exists( 'possessionise' ) )
{
	function possessionise( $str )
	{
		
		return ( substr( $str, -1 ) == 's' ) ? $str . '\'' : $str . '\'s';
	
	}
}


/**
 * Genderise
 *
 * Performs a basic 'genderisation' of a string to the correct gender, based on $gender
 *
 * @access	public
 * @param	mixed
 * @return	string
 */
if ( ! function_exists( 'genderise' ) )
{
	function genderise( $gender, $str )
	{
		$pattern = NULL;
		$replace = NULL;
		
		//	Rules
		switch ( $gender ) :
		
			//	Male
			case 'm' :
			case 'male' :
			
				$pattern[] = '/([^a-z])?(her)([^a-z])|([^a-z])?(their)([^a-z])/ei';
				$replace[] = '_genderise( \'\\1\\4\', \'\\2\\5\', \'\\3\\6\', \'his\')';
				
				$pattern[] = '/([^a-z])?(her)([^a-z])|([^a-z])?(them)([^a-z])/ei';
				$replace[] = '_genderise( \'\\1\\4\', \'\\2\\5\', \'\\3\\6\', \'him\')';
				
				$pattern[] = '/([^a-z])?(she)([^a-z])|([^a-z])?(they)([^a-z])/ei';
				$replace[] = '_genderise( \'\\1\\4\', \'\\2\\5\', \'\\3\\6\', \'he\')';
				
			break;
			
			//	Female
			case 'f' :
			case 'female' :
				
				$pattern[] = '/([^a-z])?(his)([^a-z])|([^a-z])?(their)([^a-z])/ei';
				$replace[] = '_genderise( \'\\1\\4\', \'\\2\\5\', \'\\3\\6\', \'her\')';
				
				$pattern[] = '/([^a-z])?(him)([^a-z])|([^a-z])?(them)([^a-z])/ei';
				$replace[] = '_genderise( \'\\1\\4\', \'\\2\\5\', \'\\3\\6\', \'her\')';
				
				$pattern[] = '/([^a-z])?(he)([^a-z])|([^a-z])?(they)([^a-z])/ei';
				$replace[] = '_genderise( \'\\1\\4\', \'\\2\\5\', \'\\3\\6\', \'she\')';
			
			break;
			
			//	Unisex
			default :
				
				$pattern[] = '/([^a-z])?(his)([^a-z])|([^a-z])?(her)([^a-z])/ei';
				$replace[] = '_genderise( \'\\1\\4\', \'\\2\\5\', \'\\3\\6\', \'their\')';
				
				$pattern[] = '/([^a-z])?(him)([^a-z])|([^a-z])?(her)([^a-z])/ei';
				$replace[] = '_genderise( \'\\1\\4\', \'\\2\\5\', \'\\3\\6\', \'them\')';
				
				$pattern[] = '/([^a-z])?(he)([^a-z])|([^a-z])?(she)([^a-z])/ei';
				$replace[] = '_genderise( \'\\1\\4\', \'\\2\\5\', \'\\3\\6\', \'their\')';

			
			break;
		
		endswitch;
		
		//	Modify string
		return preg_replace( $pattern, $replace, $str );
	
	}
	
	//	Helper func to maintain case
	function _genderise( $old_pre, $old, $old_post, $new )
	{
		//	Determine case
		$case = NULL;
		
		// work it out here...
		if ( ctype_upper( $old ) )
			$case = 'upper';
			
		if ( ctype_lower( $old ) )
			$case = 'lower';
			
		if ( preg_match( '/[A-Z][a-z]+/', $old ) )
			$case = 'title';
		
		//	Transform string
		switch ( $case ) :
		
			case 'lower' :
			
				return $old_pre . strtolower( $new ) . $old_post;
			
			break;
			
			case 'upper' :
			
				return $old_pre . strtoupper( $new ) . $old_post;
			
			break;
			
			case 'title' :
			
				return $old_pre . title_case( $new ) . $old_post;
			
			break;
		
		endswitch;
		
		return $old_pre . $new . $old_post;
	}
}


/* End of file NAILS_inflector_helper.php */
/* Location: ./application/helpers/NAILS_inflector_helper.php */