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
	public function index()
	{
		//	Page title
		//	==========

		$this->data['page']->title = 'Shop/index';

		// --------------------------------------------------------------------------

		//	Categories
		//	==========

		$this->data['categories'] = array( 'categories' );


		//	Tags
		//	====

		$this->data['tags'] = array( 'tags' );


		//	Featured Products
		//	=================

		$this->data['products_featured'] = array( 'featured products' );

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',								$this->data );
		$this->load->view( 'shop/' . $this->_skin->dir . '/front/index',	$this->data );
		$this->load->view( 'structure/footer',								$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Detects the brand slug and loads the appropriate method
	 * @return void
	 */
	public function brand()
	{
		$_slug = preg_replace( '#' . app_setting( 'url', 'shop' ) . 'brand/?#', '', uri_string() );

		if ( $_slug ) :

			$this->_brand_single( $_slug );

		else :

			$this->_brand_index();

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Renders the list of categories
	 * @return void
	 */
	protected function _brand_index()
	{
		if ( ! app_setting( 'page_brand_listing', 'shop' ) ) :

		endif;

		// --------------------------------------------------------------------------

		//	Page title
		//	==========

		$this->data['page']->title = 'Brand: List';


		// --------------------------------------------------------------------------

		//	Brands
		//	======

		$this->data['brands'] = $this->brand->get_all();

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',									$this->data );
		$this->load->view( 'shop/' . $this->_skin->dir . '/front/brand/index',	$this->data );
		$this->load->view( 'structure/footer',									$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Renders a single brand
	 * @return void
	 */
	protected function _brand_single( $slug )
	{
		$this->data['brand'] = $this->brand->get_by_slug( $slug );

		if ( ! $this->data['brand' ] ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Page title
		//	==========

		$this->data['page']->title = 'Brand: "' . $this->data['brand']->label . '"';

		// --------------------------------------------------------------------------

		//	Brand's Products
		//	================

		//	TODO: Adopt the generic Nails Model approach and use a where conditional
		$this->data['products'] = $this->product->get_all();

		if ( empty( $this->data['products'] ) ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',									$this->data );
		$this->load->view( 'shop/' . $this->_skin->dir . '/front/brand/single',	$this->data );
		$this->load->view( 'structure/footer',									$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Detects the category slug and loads the appropriate method
	 * @return void
	 */
	public function category()
	{
		$_slug = preg_replace( '#' . app_setting( 'url', 'shop' ) . 'category/?#', '', uri_string() );

		if ( $_slug ) :

			$this->_category_single( $_slug );

		else :

			$this->_category_index();

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Renders the list of categories
	 * @return void
	 */
	protected function _category_index()
	{
		if ( ! app_setting( 'page_category_listing', 'shop' ) ) :

		endif;

		// --------------------------------------------------------------------------

		//	Page title
		//	==========

		$this->data['page']->title = 'Category: List';

		// --------------------------------------------------------------------------

		//	Categories
		//	==========

		$this->data['categories'] = $this->category->get_all();

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',										$this->data );
		$this->load->view( 'shop/' . $this->_skin->dir . '/front/category/index',	$this->data );
		$this->load->view( 'structure/footer',										$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Renders a single category
	 * @return void
	 */
	protected function _category_single( $slug )
	{
		$this->data['category'] = $this->category->get_by_slug( $slug );

		if ( ! $this->data['category' ] ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Page title
		//	==========

		$this->data['page']->title = 'Category: "' . $this->data['category']->label . '"';

		// --------------------------------------------------------------------------

		//	Category's Products
		//	===================

		//	TODO: Adopt the generic Nails Model approach and use a where conditional
		$this->data['products'] = $this->product->get_all();

		if ( empty( $this->data['products'] ) ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',										$this->data );
		$this->load->view( 'shop/' . $this->_skin->dir . '/front/category/single',	$this->data );
		$this->load->view( 'structure/footer',										$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Detects the collection slug and loads the appropriate method
	 * @return void
	 */
	public function collection()
	{
		$_slug = preg_replace( '#' . app_setting( 'url', 'shop' ) . 'collection/?#', '', uri_string() );

		if ( $_slug ) :

			$this->_collection_single( $_slug );

		else :

			$this->_collection_index();

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Renders the list of categories
	 * @return void
	 */
	protected function _collection_index()
	{
		if ( ! app_setting( 'page_collection_listing', 'shop' ) ) :

		endif;

		// --------------------------------------------------------------------------

		//	Page title
		//	==========

		$this->data['page']->title = 'Collection: List';

		// --------------------------------------------------------------------------

		//	Collections
		//	===========

		$this->data['collections'] = $this->collection->get_all();

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',										$this->data );
		$this->load->view( 'shop/' . $this->_skin->dir . '/front/collection/index',	$this->data );
		$this->load->view( 'structure/footer',										$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Renders a single collection
	 * @return void
	 */
	protected function _collection_single( $slug )
	{
		$this->data['collection'] = $this->collection->get_by_slug( $slug );

		if ( ! $this->data['collection' ] ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Page title
		//	==========

		$this->data['page']->title = 'Collection: "' . $this->data['collection']->label . '"';

		// --------------------------------------------------------------------------

		//	Collection's Products
		//	=====================

		//	TODO: Adopt the generic Nails Model approach and use a where conditional
		$this->data['products'] = $this->product->get_all();

		if ( empty( $this->data['products'] ) ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',											$this->data );
		$this->load->view( 'shop/' . $this->_skin->dir . '/front/collection/single',	$this->data );
		$this->load->view( 'structure/footer',											$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Detects the product slug and loads the appropriate method
	 * @return void
	 */
	protected function product()
	{
		$_slug = preg_replace( '#' . app_setting( 'url', 'shop' ) . 'product/?#', '', uri_string() );

		if ( $_slug ) :

			$this->_product_single( $_slug );

		else :

			show_404();

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Renders a single product
	 * @return void
	 */
	protected function _product_single( $slug )
	{
		$this->data['product'] = $this->product->get_by_slug( $slug );

		if ( ! $this->data['product' ] ) :

			show_404();

		endif;

		//	Generate missing SEO content
		$this->product->generate_seo_content( $this->data['product'] );

		// --------------------------------------------------------------------------

		//	SEO
		//	===

		$this->data['page']->title				= $this->data['product']->seo_title ? $this->data['product']->seo_title : $this->data['product']->label;
		$this->data['page']->seo->description	= $this->data['product']->seo_description;
		$this->data['page']->seo->keywords		= $this->data['product']->seo_keywords;

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',										$this->data );
		$this->load->view( 'shop/' . $this->_skin->dir . '/front/product/single',	$this->data );
		$this->load->view( 'structure/footer',										$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Detects the range slug and loads the appropriate method
	 * @return void
	 */
	public function range()
	{
		$_slug = preg_replace( '#' . app_setting( 'url', 'shop' ) . 'range/?#', '', uri_string() );

		if ( $_slug ) :

			$this->_range_single( $_slug );

		else :

			$this->_range_index();

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Renders the list of categories
	 * @return void
	 */
	protected function _range_index()
	{
		if ( ! app_setting( 'page_range_listing', 'shop' ) ) :

		endif;

		// --------------------------------------------------------------------------

		//	Page title
		//	==========

		$this->data['page']->title = 'Range: List';

		// --------------------------------------------------------------------------

		//	Ranges
		//	======

		$this->data['ranges'] = $this->range->get_all();

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',									$this->data );
		$this->load->view( 'shop/' . $this->_skin->dir . '/front/range/index',	$this->data );
		$this->load->view( 'structure/footer',									$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Renders a single range
	 * @return void
	 */
	protected function _range_single( $slug )
	{
		$this->data['range'] = $this->range->get_by_slug( $slug );

		if ( ! $this->data['range' ] ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Page title
		//	==========

		$this->data['page']->title = 'Range: "' . $this->data['range']->label . '"';

		// --------------------------------------------------------------------------

		//	Range's Products
		//	================

		//	TODO: Adopt the generic Nails Model approach and use a where conditional
		$this->data['products'] = $this->product->get_all();

		if ( empty( $this->data['products'] ) ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',									$this->data );
		$this->load->view( 'shop/' . $this->_skin->dir . '/front/range/single',	$this->data );
		$this->load->view( 'structure/footer',									$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Detects the sale slug and loads the appropriate method
	 * @return void
	 */
	public function sale()
	{
		$_slug = preg_replace( '#' . app_setting( 'url', 'shop' ) . 'sale/?#', '', uri_string() );

		if ( $_slug ) :

			$this->_sale_single( $_slug );

		else :

			$this->_sale_index();

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Renders the list of categories
	 * @return void
	 */
	protected function _sale_index()
	{
		if ( ! app_setting( 'page_sale_listing', 'shop' ) ) :

		endif;

		// --------------------------------------------------------------------------

		//	Page title
		//	==========

		$this->data['page']->title = 'Sale: List';

		// --------------------------------------------------------------------------

		//	Sales
		//	=====

		$this->data['sales'] = $this->sale->get_all();

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',									$this->data );
		$this->load->view( 'shop/' . $this->_skin->dir . '/front/sale/index',	$this->data );
		$this->load->view( 'structure/footer',									$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Renders a single sale
	 * @return void
	 */
	protected function _sale_single( $slug )
	{
		$this->data['sale'] = $this->sale->get_by_slug( $slug );

		if ( ! $this->data['sale' ] ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Page title
		//	==========

		$this->data['page']->title = 'Sale: "' . $this->data['sale']->label . '"';

		// --------------------------------------------------------------------------

		//	Sale's Products
		//	===============

		//	TODO: Adopt the generic Nails Model approach and use a where conditional
		$this->data['products'] = $this->product->get_all();

		if ( empty( $this->data['products'] ) ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',									$this->data );
		$this->load->view( 'shop/' . $this->_skin->dir . '/front/sale/single',	$this->data );
		$this->load->view( 'structure/footer',									$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Detects the tag slug and loads the appropriate method
	 * @return void
	 */
	public function tag()
	{
		$_slug = preg_replace( '#' . app_setting( 'url', 'shop' ) . 'tag/?#', '', uri_string() );

		if ( $_slug ) :

			$this->_tag_single( $_slug );

		else :

			$this->_tag_index();

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Renders the list of categories
	 * @return void
	 */
	protected function _tag_index()
	{
		if ( ! app_setting( 'page_tag_listing', 'shop' ) ) :

		endif;

		// --------------------------------------------------------------------------

		//	Page title
		//	==========

		$this->data['page']->title = 'Tag: List';

		// --------------------------------------------------------------------------

		//	Tags
		//	====

		$this->data['tags'] = $this->tag->get_all();

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',									$this->data );
		$this->load->view( 'shop/' . $this->_skin->dir . '/front/tag/index',	$this->data );
		$this->load->view( 'structure/footer',									$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Renders a single tag
	 * @return void
	 */
	protected function _tag_single( $slug )
	{
		$this->data['tag'] = $this->tag->get_by_slug( $slug );

		if ( ! $this->data['tag' ] ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Page title
		//	==========

		$this->data['page']->title = 'Tag: "' . $this->data['tag']->label . '"';

		// --------------------------------------------------------------------------

		//	Tag's Products
		//	==============

		//	TODO: Adopt the generic Nails Model approach and use a where conditional
		$this->data['products'] = $this->product->get_all();

		if ( empty( $this->data['products'] ) ) :

			show_404();

		endif;

		// --------------------------------------------------------------------------

		$this->load->view( 'structure/header',									$this->data );
		$this->load->view( 'shop/' . $this->_skin->dir . '/front/tag/single',	$this->data );
		$this->load->view( 'structure/footer',									$this->data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Manually remap the URL as CI's router has some issues resolving the index()
	 * route, especially when using a non-standard shop base URL
	 * @return void
	 */
	public function _remap()
	{
		$_method = $this->uri->rsegment( 2 ) ? $this->uri->rsegment( 2 ) : 'index';

		// --------------------------------------------------------------------------

		if ( method_exists( $this, $_method ) ) :

			$this->{$_method}();

		else :

			show_404();

		endif;
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
 * CodeIgniter instantiate a class with the same name as the file, therefore
 * when we try to extend the parent class we get 'cannot redeclare class X' errors
 * and if we call our overloading class something else it will never get instantiated.
 *
 * We solve this by prefixing the main class with NAILS_ and then conditionally
 * declaring this helper class below; the helper gets instantiated et voila.
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