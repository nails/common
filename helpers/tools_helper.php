<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Re-maps a number from one range to another
 * See http://www.arduino.cc/en/Reference/Map
 *
 * @access	public
 * @param	float 	Number to map
 * @param	int 	Current low
 * @param	int 	Current high
 * @param	int 	New low
 * @param	int 	New high
 * @return	float
 */
if ( ! function_exists( 'map' ) )
{
	function map( $x, $in_min, $in_max, $out_min, $out_max )
	{
		return ( $x - $in_min ) * ( $out_max - $out_min ) / ( $in_max - $in_min ) + $out_min;
	}
}


// --------------------------------------------------------------------------


/**
 * Replaces special chars with their HTML counterpart
 *
 * @access	public
 * @param	string 	String to parse
 * @return	float
 */
if ( ! function_exists( 'special_chars' ) )
{
	function special_chars( $string )
	{
		/* Only do the slow convert if there are 8-bit characters */
		/* avoid using 0xA0 (\240) in ereg ranges. RH73 does not like that */
		if ( ! preg_match( "/[\200-\237]/", $string ) and ! preg_match( "/[\241-\377]/", $string ) )
			return $string;

		// decode three byte unicode characters
		$string = preg_replace( "/([\340-\357])([\200-\277])([\200-\277])/e",
		"'&#'.((ord('\\1')-224)*4096 + (ord('\\2')-128)*64 + (ord('\\3')-128)).';'",
		$string );

		// decode two byte unicode characters
		$string = preg_replace( "/([\300-\337])([\200-\277])/e",
		"'&#'.((ord('\\1')-192)*64+(ord('\\2')-128)).';'",
		$string );

		return $string;
	}
}


// --------------------------------------------------------------------------


/**
 * Format a filesize in bytes, kilobytes, megabytes, etc...
 *
 * @access	public
 * @param	string
 * @return	float
 */
if ( ! function_exists( 'format_bytes' ) )
{
	function format_bytes( $bytes, $precision = 2 )
	{
		$units = array( 'B', 'KB', 'MB', 'GB', 'TB' );

		$bytes = max( $bytes, 0);
		$pow = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
		$pow = min( $pow, count( $units ) - 1 );

		//	Uncomment one of the following alternatives
		//$bytes /= pow(1024, $pow);
		$bytes /= ( 1 << ( 10 * $pow ) );

		$var = round( $bytes, $precision ) . ' ' . $units[$pow];
		return preg_replace_callback( '/(.+?)\.(.*?)/', function( $matches ) { return number_format($matches[1]) . '.' . $matches[2]; }, $var );
	}
}


// --------------------------------------------------------------------------


/**
 * Formats a filesize as bytes (e.g max_upload_size)
 * hat-tip: http://php.net/manual/en/function.ini-get.php#96996
 *
 * @access	public
 * @param	string
 * @return	float
 */
if ( ! function_exists( 'return_bytes' ) )
{
	function return_bytes( $size_str )
	{
		switch ( substr( $size_str, -1 ) ) :

			case 'M': case 'm': return (int) $size_str * 1048576;
			case 'K': case 'k': return (int) $size_str * 1024;
			case 'G': case 'g': return (int) $size_str * 1073741824;
			default: return $size_str;

		endswitch;
	}
}


// --------------------------------------------------------------------------


/**
 * Converts a string to a boolean
 *
 * @access	public
 * @param	string
 * @return	float
 */
if ( ! function_exists( 'string_to_boolean' ) )
{
	function string_to_boolean( $string )
	{
		if ( $string && strtoupper( $string ) !== "FALSE") :

			return TRUE;

		else:

			return FALSE;

		endif;
	}
}


// --------------------------------------------------------------------------


/**
 * Match an IP to a given CIDR range
 *
 * @access	public
 * @param	string
 * @return	float
 */
if ( ! function_exists( 'ip_in_range' ) )
{
	function ip_in_range( $ip, $range )
	{
		if ( ! array( $range ) ) :

			if ( strpos( $range, ',' ) !== FALSE ) :

				$range = explode( ',', $range );

			else :

				$range = (array) $range;

			endif;

		endif;

		foreach ( $range AS $cidr_mask ) :

			if ( strpos( $cidr_mask, '/' ) !== FALSE ) :

				//	Hat tip: http://stackoverflow.com/a/594134/789224
				list ($subnet, $bits) = explode('/', $cidr_mask);
				$ip = ip2long($ip);
				$subnet = ip2long($subnet);
				$mask = -1 << (32 - $bits);
				$subnet &= $mask; # nb: in case the supplied subnet wasn't correctly aligned
				if ( ($ip & $mask) == $subnet ) :

					return TRUE;

				endif;

			else :

				if ( $ip == $cidr_mask ) :

					return TRUE;

				endif;

			endif;

		endforeach;

		return FALSE;
	}
}


/* End of file tools_helper.php */
/* Location: ./helpers/tools_helper.php */