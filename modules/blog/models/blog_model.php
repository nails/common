<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			blog_model.php
 *
 * Description:		This model primarily handles blog settings
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Blog_model extends NAILS_Model
{
	protected $_settings;


	// --------------------------------------------------------------------------


	public function __construct()
	{
		$this->config->load( 'blog', FALSE, TRUE );
	}


	// --------------------------------------------------------------------------


	public function get_associations( $post_id = NULL )
	{
		$_associations	= $this->config->item( 'blog_post_associations' );

		if ( ! $_associations )
			return array();

		foreach( $_associations AS &$assoc ) :

			//	Fetch the association data from the source, fail ungracefully - the dev should have this configured correctly.
			//	Fetch current associations if a post_id has been supplied

			if ( $post_id ) :

				$this->db->where( 'post_id', $post_id );
				$assoc->current = $this->db->get( $assoc->target )->result();

			else :

				$assoc->current = array();

			endif;

			//	Fetch the raw data
			$this->db->select( $assoc->source->id . ' id, ' . $assoc->source->label . ' label' );
			$this->db->order_by( 'label' );

			if ( isset( $assoc->source->where ) && $assoc->source->where ) :

				$this->db->where( $assoc->source->where  );

			endif;
			$assoc->data = $this->db->get( $assoc->source->table )->result();

		endforeach;

		return $_associations;
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' MODELS
 *
 * The following block of code makes it simple to extend one of the core Nails
 * models. Some might argue it's a little hacky but it's a simple 'fix'
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_BLOG_MODEL' ) ) :

	class Blog_model extends NAILS_Blog_model
	{
	}

endif;

/* End of file blog_model.php */
/* Location: ./modules/blog/models/blog_model.php */