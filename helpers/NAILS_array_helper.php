<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists( 'array_unique_multi' ) )
{
	function array_unique_multi( array $array )
	{
		//	Hat-tip: http://phpdevblog.niknovo.com/2009/01/using-array-unique-with-multidimensional-arrays.html

		// Unique Array for return
		$array_rewrite = array();

		// Array with the md5 hashes
		$array_hashes = array();

		foreach ( $array AS $key => $item ) :

			// Serialize the current element and create a md5 hash
			$hash = md5( serialize( $item ) );

			// If the md5 didn't come up yet, add the element to
			// to array_rewrite, otherwise drop it

			if ( ! isset( $array_hashes[$hash] ) ) :

				// Save the current element hash
				$array_hashes[$hash] = $hash;

				// Add element to the unique Array
				$array_rewrite[$key] = $item;

			endif;

		endforeach;

		unset( $array_hashes );
		unset( $key );
		unset( $item );
		unset( $hash );

		return $array_rewrite;
	}
}


/* End of file array_helper.php */
/* Location: ./helpers/array_helper.php */