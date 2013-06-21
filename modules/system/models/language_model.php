<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		CORE_NAILS_Language_Model
 *
 * Description:	This model contains all methods for handling languages
 * 
 **/

/**
 * OVERLOADING NAILS' MODELS
 * 
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 * 
 **/

class NAILS_Language_model extends NAILS_Model
{
	private $_default_lang;
	
	// --------------------------------------------------------------------------
	
	
	public function get_all()
	{
		if ( ! NAILS_DB_ENABLED ) :
		
			return array();
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->db->select( 'l.id,l.name,l.slug,l.priority,l.supported' );
		$this->db->order_by( 'l.name' );
		return $this->db->get( 'language l' )->result();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_all_supported()
	{
		if ( ! NAILS_DB_ENABLED ) :
		
			return array();
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->db->where( 'l.supported', TRUE );
		return $this->get_all();
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_all_flat()
	{
		$_out	= array();
		$_langs	= $this->get_all();
		
		for( $i=0; $i<count( $_langs ); $i++ ) :
		
			$_out[$_langs[$i]->id] = $_langs[$i]->name;
		
		endfor;
		
		// --------------------------------------------------------------------------
		
		return $_out;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_all_supported_flat()
	{
		$_out	= array();
		$_langs	= $this->get_all_supported();
		
		for( $i=0; $i<count( $_langs ); $i++ ) :
		
			$_out[$_langs[$i]->id] = $_langs[$i]->name;
		
		endfor;
		
		// --------------------------------------------------------------------------
		
		return $_out;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_by_id( $id )
	{
		if ( ! NAILS_DB_ENABLED ) :
		
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->db->where( 'l.id', $id );
		$_result = $this->get_all();
		
		// --------------------------------------------------------------------------
		
		if ( ! $_result )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		return $_result[0];
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_by_slug( $slug )
	{
		if ( ! NAILS_DB_ENABLED ) :
		
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->db->where( 'l.slug', $slug );
		$_result = $this->get_all();
		
		// --------------------------------------------------------------------------
		
		if ( ! $_result )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		return $_result[0];
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_default_id()
	{
		if ( ! NAILS_DB_ENABLED ) :
		
			return NULL;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		if ( $this->_default_lang )
			return $this->_default_lang->id;
		
		// --------------------------------------------------------------------------
		
		//	Fetch and cache
		$_default = $this->get_by_slug( APP_DEFAULT_LANG_SLUG );
		
		if ( isset( $_default->id ) ) :
		
			$this->_defaut_lang = $_default;
			return $_default->id;
			
		else :
		
			return NULL;
			
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_default_name()
	{
		if ( ! NAILS_DB_ENABLED ) :
		
			return NULL;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		if ( $this->_default_lang )
			return $this->_default_lang->name;
		
		// --------------------------------------------------------------------------
		
		//	Fetch and cache
		$_default = $this->get_by_slug( APP_DEFAULT_LANG_SLUG );
		
		if ( isset( $_default->id ) ) :
		
			$this->_defaut_lang = $_default;
			return $_default->name;
			
		else :
		
			return NULL;
			
		endif;
	}


	// --------------------------------------------------------------------------


	public function mark_supported( $id )
	{
		$_data				= array();
		$_data['supported']	= TRUE;
		return $this->update( $id, $_data );
	}


	// --------------------------------------------------------------------------


	public function mark_unsupported( $id )
	{
		$_data				= array();
		$_data['supported']	= FALSE;
		return $this->update( $id, $_data );	
	}


	// --------------------------------------------------------------------------


	public function update( $id, $data )
	{
		$this->db->set( $data );
		$this->db->where( 'id', $id );
		return $this->db->update( 'language' );
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' MODELS
 * 
 * The following block of code makes it simple to extend one of the core
 * models. Some might argue it's a little hacky but it's a simple 'fix'
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
 
if ( ! defined( 'NAILS_ALLOW_EXTENSION_LANGUAGE_MODEL' ) ) :

	class Language_model extends NAILS_Language_model
	{
	}

endif;


/* End of file language_model.php */
/* Location: ./system/application/models/language_model.php */