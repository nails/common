<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			shop_basket_model.php
 *
 * Description:		This model handles everything to do with the user's basket
 * 
 **/

class Shop_basket_model extends NAILS_Model
{
	private $_items;
	
	
	// --------------------------------------------------------------------------
	
	
	public function __construct()
	{
		parent::__construct();
		
		// --------------------------------------------------------------------------
		
		$_mock_item = new stdClass();
		$_mock_item->id				= '13';
		$_mock_item->type			= new stdClass;
		$_mock_item->type->slug		= 'download';
		$_mock_item->type->label	= 'Download';
		$_mock_item->title			= 'How to excel at everything you do, a guide';
		$_mock_item->quantity		= 2;
		$_mock_item->price			= '&pound;34.99';
		$_mock_item->shipping		= 'Free';
		$_mock_item->total			= '&pound;34.99';
		
		$this->_items = array( $_mock_item, $_mock_item, $_mock_item);
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_basket()
	{
		$_basket					= new stdClass();
		$_basket->items				= array();
		$_basket->totals			= new stdClass;
		$_basket->totals->net		= 0.00;
		$_basket->totals->gross		= 0.00;
		$_basket->totals->shipping	= 0.00;
		$_basket->totals->tax		= 0.00;
		
		// --------------------------------------------------------------------------
		
		for ( $_i = 0; $_i < count( $this->_items ); $_i++ ) :
		
			//	Fetch details about product
			
			$_item = $this->_items[$_i];
			
			// --------------------------------------------------------------------------
			
			$_basket->items[] = $_item;
		
		endfor;
		
		// --------------------------------------------------------------------------
		
		return $_basket;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_basket_count( $respect_quantity = TRUE )
	{
		if ( $respect_quantity ) :
		
			$_count = 0;
			
			for ( $_i = 0; $_i < count( $this->_items ); $_i++ ) :
			
				$_count += $this->_items[$_i]->quantity;
			
			endfor;
			
			return $_count;
		
		else:
		
			return count( $this->_items );
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function increment( $key )
	{
		return TRUE;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function decrement( $key )
	{
		return TRUE;
	}
}

/* End of file shop_basket_model.php */
/* Location: ./application/models/shop_basket_model.php */