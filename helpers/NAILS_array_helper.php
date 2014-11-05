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


if ( ! function_exists( 'array_sort_multi' ) )
{
	function array_sort_multi( array &$array, $field )
	{
		usort( $array, function( $a, $b ) use ( $field )
		{
			//	Equal?
			if ( trim( $a->$field ) == trim( $b->$field ) ) :

				return 0;

			endif;

			//	Not equal, work out which takes precedence
			$_sort = array( $a->$field, $b->$field );
			sort( $_sort );

			return $_sort[0] == $a->$field ? -1 : 1;
		});
	}
}


if ( ! function_exists( 'array_search_multi' ) ) {

	function array_search_multi($value, $key, array $array)
	{
		foreach ($array as $k => $val) {

			if (is_array($val)) {

				if ($val[$key] == $value) {
					return $k;
				}

			} elseif (is_object($val)) {

				if ($val->$key == $value) {
					return $k;
				}
			}
		}
		return false;
	}
}


if ( ! function_exists( 'in_array_multi' ) ) {

	function in_array_multi($value, $key, array $array)
	{
		return array_search_multi($value, $key, $array) !== false;
	}
}


/* End of file array_helper.php */
/* Location: ./helpers/array_helper.php */