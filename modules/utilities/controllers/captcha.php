<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Captcha
*
* Docs:			http://nails.shedcollective.org/docs/utilities/captcha/
*
* Created:		05/06/2011
* Modified:		07/04/2012
*
* Description:	This class generates captcha images for checking a user is a humanoid.
*				It tries to keep load as low as possible so will only load the minimum
*				required resources (i.e not extend the main CI super object).
* 
*/


class Captcha {

	private $_ci;
	private $_font;
	private $_background;
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Construct the class; set defaults
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 * 
	 **/
	public function __construct()
	{
		$this->_ci =& get_instance();
		
		// --------------------------------------------------------------------------
		
		$this->_font		= NAILS_PATH . 'assets/fonts/rockwell.ttf';
		$this->_background	= NAILS_PATH . 'assets/img/captcha/captcha.png';
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Generate the captcha image
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 * 
	 **/
	public function index()
	{
		//	Generate word
		$_captcha = strtoupper( random_string( 'alnum', 6 ) );
		
		// --------------------------------------------------------------------------
		
		//	Add word to user's session
		$this->_ci->session->set_userdata( 'captcha', $_captcha );
		
		// --------------------------------------------------------------------------
		
		//	Create the image
		$_img = imagecreatefrompng( $this->_background );
		
		// --------------------------------------------------------------------------
		
		//	Randomly set the text colour
		$_color = imagecolorallocate( $_img, rand(0,100),rand(0,100),rand(0,100) );
		
		// --------------------------------------------------------------------------
		
		//	Add the text to the image;
		//	Img Handle, Text Size, Text Angle, X_pos, Y_pos, Color var, ttf Font var, Text var
		imagettftext ( $_img, 15, rand ( -7, 7 ), rand( 10, 40 ), rand ( 18, 24 ), $_color, $this->_font, $_captcha );
		
		// --------------------------------------------------------------------------
		
		//	Send output to browser
		header ( "Content-type: image/png" );
		imagepng ( $_img );
		
		// --------------------------------------------------------------------------
		
		//	Destory resource
		imagedestroy ( $_img );
	}

	
}

/* End of file captcha.php */
/* Location: ./application/modules/utilities/controllers/captcha.php */