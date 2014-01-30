<?php

/**
* Attempts to fetch the real domain from a URL
*
* Attempts to get the top level part of a URL (i.e example.tld from sub.domains.example.tld).
*
* Hat tip: http://uk1.php.net/parse_url#104874
*
* @access	public
* @param	string
* @return	string	The real domain, or FALSE on error
*/
if ( ! function_exists('get_domain_from_url')) :

	function get_domain_from_url( $url )
	{
		$_bits = explode( '/', $url );

		if ( $_bits[0] == 'http:' || $_bits[0] == 'https:' ) :

			$_domain = $_bits[2];

		else :

			$_domain = $_bits[0];

		endif;

		unset( $_bits );

		$_bits	= explode( '.', $_domain );
		$_idz	= count( $_bits );
		$_idz	-=3;

		if ( ! isset( $_bits[($_idz+2)] ) ) :

			$_url = FALSE;

		elseif ( strlen( $_bits[($_idz+2)] ) == 2 ) :

			$_url = $_bits[$_idz] . '.' . $_bits[($_idz+1)] . '.' . $_bits[($_idz+2)];

		elseif ( strlen( $_bits[($_idz+2)] ) == 0 ) :

			$_url = $_bits[($_idz)] . '.' . $_bits[($_idz+1)];

		elseif ( isset( $_bits[($_idz+1)] ) ) :

			$_url = $_bits[($_idz+1)] . '.' . $_bits[($_idz+2)];

		else :

			$_url = FALSE;

		endif;

		return $_url;
	}

endif;


// --------------------------------------------------------------------------


/**
* Fetches the relative path between two directories
*
* Hat tip: Thanks to Gordon for this one; http://stackoverflow.com/a/2638272/789224
*
* @access	public
* @param	string
* @param	string
* @return	string	The relative path between the two directories
*/
if ( ! function_exists( 'get_relative_path' ) ) :

	function get_relative_path( $from, $to )
	{
		$from     = explode( '/', $from );
		$to       = explode( '/', $to );
		$relPath  = $to;

		foreach( $from AS $depth => $dir ) :

			//	Find first non-matching dir
			if( $dir === $to[$depth] ) :

				//	Ignore this directory
				array_shift( $relPath );

			else :

			//	Get number of remaining dirs to $from
			$remaining = count( $from ) - $depth;

				if ( $remaining > 1 ) :

					// add traversals up to first matching dir
					$padLength = ( count( $relPath ) + $remaining - 1 ) * -1;
					$relPath = array_pad( $relPath, $padLength, '..' );
					break;

				else :

					$relPath[0] = './' . $relPath[0];

				endif;

			endif;

		endforeach;

		return implode( '/', $relPath );
	}

endif;

/* End of file CORE_NAILS_Common.php */
/* Location: ./core/CORE_NAILS_Common.php */