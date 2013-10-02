<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Shop
 *
 * Description:	This controller handles the frontpage of the shop
 *
 **/

/**
 * OVERLOADING NAILS' SHOP MODULE
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

//	Include _shop.php; executes common functionality
require_once '_shop.php';

class NAILS_Shop extends NAILS_Shop_Controller
{
	protected $_action;
	protected $_slug;

	// --------------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		//	Load models
		$this->load->model( 'shop/shop_model',			'shop' );
		$this->load->model( 'shop/shop_category_model',	'category' );
		$this->load->model( 'shop/shop_product_model',	'product' );
	}


	// --------------------------------------------------------------------------


	/**
	 * Renders the shop's homepage
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 *
	 **/
	public function _home()
	{
		//	Page title
		$this->data['page']->title = 'Shop/home';

		// --------------------------------------------------------------------------

		//	Categories
		//	==========


		//	Products
		//	========

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'shop/front/home',	$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Renders the category listings
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 *
	 **/
	public function _category()
	{
		//	Page title
		$this->data['page']->title = 'Shop/category';

		// --------------------------------------------------------------------------

		//	Categories
		//	==========

		if ( $this->_slug ) :

			//	Find a specific category; placing into an array for consitency with the toplevel
			$this->data['categories']	= array( $this->category->get_by_slug( $this->_slug, TRUE, TRUE ) );

		else :

			//	Fetch top level categories
			$this->data['categories']	= $this->category->get_top_level( TRUE, TRUE );

		endif;

		// --------------------------------------------------------------------------

		//	Products
		//	========

		if ( $this->_slug ) :

			//	Find products for a specific category
			$this->data['products']	= $this->product->get_for_category( $this->_slug );

		else :

			//	Fetch top level categories
			$this->data['products']	= $this->product->get_all();

		endif;

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'shop/front/category',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Renders the tag page
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 *
	 **/
	public function _tag()
	{
		//	Page title
		$this->data['page']->title = 'Shop/tag';

		// --------------------------------------------------------------------------

		//	Categories
		//	==========


		//	Products
		//	========

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',	$this->data );
		$this->load->view( 'shop/front/tag',	$this->data );
		$this->load->view( 'structure/footer',	$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Renders the product page
	 *
	 * @access	public
	 * @return	void
	 * @author	Pablo
	 *
	 **/
	public function _product()
	{
		//	Page title
		$this->data['page']->title = 'Shop/product';

		// --------------------------------------------------------------------------

		//	Categories
		//	==========


		//	Products
		//	========

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'shop/front/product',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );
	}


	// --------------------------------------------------------------------------


	public function _remap( $method )
	{
		//	using rsegment in case the route has been re-written to something funky
		$this->_action	= $this->uri->rsegment( 2 );

		//	Quick bit of shifting to get the slug
		$this->_slug = $this->uri->rsegment_array();
		array_shift( $this->_slug );
		array_shift( $this->_slug );
		$this->_slug = implode( '/',  $this->_slug );

		// --------------------------------------------------------------------------

		//	Switcheroo
		switch( $this->_action ) :

			case 'index' :		$this->_home();		break;
			case 'category' :	$this->_category();	break;
			case 'tag' :		$this->_tag();		break;
			case 'product' :	$this->_product();	break;

			// --------------------------------------------------------------------------

			//	Boo.
			default :	show_404();	break;

		endswitch;
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' SHOP MODULE
 *
 * The following block of code makes it simple to extend one of the core shop
 * controllers. Some might argue it's a little hacky but it's a simple 'fix'
 * which negates the need to massively extend the CodeIgniter Loader class
 * even further (in all honesty I just can't face understanding the whole
 * Loader class well enough to change it 'properly').
 *
 * Here's how it works:
 *
 * CodeIgniter  instanciate a class with the same name as the file, therefore
 * when we try to extend the parent class we get 'cannot redeclre class X' errors
 * and if we call our overloading class something else it will never get instanciated.
 *
 * We solve this by prefixing the main class with NAILS_ and then conditionally
 * declaring this helper class below; the helper gets instanciated et voila.
 *
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 *
 **/

if ( ! defined( 'NAILS_ALLOW_EXTENSION_SHOP' ) ) :

	class Shop extends NAILS_Shop
	{
	}

endif;

/* End of file shop.php */
/* Location: ./application/modules/shop/controllers/shop.php */