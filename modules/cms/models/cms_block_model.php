<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			cms_block_model.php
 *
 * Description:		This model handles everything to do with CMS blocks
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Cms_block_model extends NAILS_Model
{
	/**
	 * Creates a new block object
	 *
	 * @access public
	 * @param string $type The type of content the bock is
	 * @param string $slug The slug to use for the block
	 * @param string $title The title of the block
	 * @param string $description The description of the block (i.e what it should represent)
	 * @param string $located A description of where the block is intended to be seen
	 * @param string $default_value The value of the block in the app's default langauge
	 * @param bool $return_object Whether or not to return just the ID of the newly created object (FALSE) or the entire object (TRUE)
	 * @return mixed
	 **/
	public function create_block( $type, $slug, $title, $description, $located, $default_value, $return_object = FALSE )
	{
		//	Test the slug
		if ( $this->get_by_slug( $slug ) ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$this->db->set( 'type', $type );
		$this->db->set( 'slug', $slug );
		$this->db->set( 'title', $title );
		$this->db->set( 'description', $description );
		$this->db->set( 'located', $located );
		$this->db->set( 'created', 'NOW()', FALSE );
		$this->db->set( 'modified', 'NOW()', FALSE );

		if ( active_user( 'id' ) ) :

			$this->db->set( 'created_by', active_user( 'id' ) );
			$this->db->set( 'modified_by', active_user( 'id' ) );

		endif;

		$this->db->insert( NAILS_DB_PREFIX . 'cms_block' );

		if ( $this->db->affected_rows() ) :

			$_id = $this->db->insert_id();

			$this->db->set( 'block_id', $_id );
			$this->db->set( 'language', $this->language_model->get_default_code() );
			$this->db->set( 'value', $default_value );
			$this->db->set( 'created', 'NOW()', FALSE );
			$this->db->set( 'modified', 'NOW()', FALSE );

			if ( active_user( 'id' ) ) :

				$this->db->set( 'created_by', active_user( 'id' ) );
				$this->db->set( 'modified_by', active_user( 'id' ) );

			endif;

			$this->db->insert( NAILS_DB_PREFIX . 'cms_block_translation' );

			if ( $this->db->affected_rows() ) :

				if ( $return_object ) :

					return $this->get_by_id( $_id );

				else :

					return $_id;

				endif;

			else :

				$this->db->where( 'id', $_id );
				$this->db->delete( NAILS_DB_PREFIX . 'cms_block' );
				return FALSE;

			endif;

		else :

			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Updates an existing block object
	 *
	 * @access public
	 * @param int $id The ID of the block
	 * @param mixed $data The fields to update
	 * @return bool
	 **/
	public function update_block( $id, $data = array() )
	{
		//	Can't change some things
		unset( $data['id'] );
		unset( $data['created'] );
		unset( $data['created_by'] );

		$this->db->set( $data );
		$this->db->set( 'modified', 'NOW()', FALSE );

		if ( active_user( 'id' ) ) :

			$this->db->set( 'modified_by', active_user( 'id' ) );

		else :

			$this->db->set( 'modified_by', NULL );

		endif;

		$this->db->where( 'id', $id );
		$this->db->update( NAILS_DB_PREFIX . 'cms_block' );

		return (bool) $this->db->affected_rows();
	}


	// --------------------------------------------------------------------------


	/**
	 * Delete a block object
	 *
	 * @access public
	 * @param mixed $id_slug The ID, or slug, of the block to delete
	 * @return bool
	 **/
	public function delete_block( $id_slug )
	{
		if ( is_numeric( $id_slug ) ) :

			$this->db->where( 'id', $id_slug );

		else :

			$this->db->where( 'slug', $id_slug );

		endif;

		$this->db->delete( NAILS_DB_PREFIX . 'cms_block' );

		return (bool) $this->db->affected_rows();
	}


	// --------------------------------------------------------------------------


	/**
	 * Creates a new translation object
	 *
	 * @access public
	 * @param int $block_id The ID of the block this translation belongs to
	 * @param int $language The ID of the language this block is written in
	 * @param string $value The contents of this translation
	 * @return mixed
	 **/
	public function create_translation( $block_id, $language, $value )
	{
		$this->db->set( 'block_id', $block_id );
		$this->db->set( 'language', $language );
		$this->db->set( 'value', trim( $value ) );
		$this->db->set( 'created', 'NOW()', FALSE );
		$this->db->set( 'modified', 'NOW()', FALSE );

		if ( active_user( 'id' ) ) :

			$this->db->set( 'created_by', active_user( 'id' ) );
			$this->db->set( 'modified_by', active_user( 'id' ) );

		else :

			$this->db->set( 'created_by', NULL );
			$this->db->set( 'modified_by', NULL );

		endif;

		$this->db->insert( NAILS_DB_PREFIX . 'cms_block_translation' );

		if ( $this->db->affected_rows() ) :

			//	Upate the main block's modified date and user
			$this->update_block( $block_id );

			return TRUE;

		else :

			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Updates an existing translation object
	 *
	 * @access public
	 * @param int $block_id The ID of the block this translation belongs to
	 * @param int $language The ID of the language this block is written in
	 * @param string $value The contents of this translation
	 * @return bool
	 **/
	public function update_translation( $block_id, $language, $value )
	{
		//	Get existing translation
		$this->db->where( 'block_id', $block_id );
		$this->db->where( 'language', $language );
		$_old = $this->db->get( NAILS_DB_PREFIX . 'cms_block_translation' )->row();

		if ( ! $_old )
			return FALSE;

		// --------------------------------------------------------------------------

		//	If the value hasn't changed then don't do anything
		if ( $_old->value == trim( $value ) )
			return FALSE;

		// --------------------------------------------------------------------------

		$this->db->set( 'value', trim( $value ) );
		$this->db->set( 'modified', 'NOW()', FALSE );

		if ( active_user( 'id' ) ) :

			$this->db->set( 'modified_by', active_user( 'id' ) );

		else :

			$this->db->set( 'modified_by', NULL );

		endif;

		$this->db->where( 'block_id', $block_id );
		$this->db->where( 'language', $language );
		$this->db->update( NAILS_DB_PREFIX . 'cms_block_translation' );

		if ( $this->db->affected_rows() ) :

			//	Create a new revision if value has changed
			$this->db->select( 'id' );
			$this->db->where( 'block_id', $block_id );
			$this->db->where( 'language', $language );
			$_block_translation = $this->db->get( NAILS_DB_PREFIX . 'cms_block_translation' )->row();

			if ( $_block_translation ) :

				$this->db->set( 'block_translation_id', $_block_translation->id );
				$this->db->set( 'value', $_old->value );
				$this->db->set( 'created', $_old->modified );
				$this->db->set( 'created_by', $_old->modified_by );
				$this->db->insert( NAILS_DB_PREFIX . 'cms_block_translation_revision' );

				//	Upate the main block's modified date and user
				$this->update_block( $_old->block_id );

			endif;

			return TRUE;

		else :

			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Updates an existing object
	 *
	 * @access public
	 * @param int $block_id The ID of the block the translation belongs to
	 * @param int $language The language ID of the block
	 * @return bool
	 **/
	public function delete_translation( $block_id, $language )
	{
		$this->db->where( 'block_id', $block_id );
		$this->db->where( 'language', $language );
		$this->db->delete( NAILS_DB_PREFIX . 'cms_block_translation' );

		if ( $this->db->affected_rows() ) :

			//	Upate the main block's modified date and user
			$this->update_block( $block_id );

			return TRUE;

		else :

			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetches all objects
	 *
	 * @access public
	 * @param bool $include_revisions Whether to include translation revisions
	 * @return array
	 **/
	public function get_all( $include_revisions = FALSE )
	{
		$this->db->select( 'cb.type, cb.slug, cb.title, cb.description, cb.located, cbv.*, u.first_name, ue.email, u.last_name, u.gender, u.profile_img' );

		$this->db->join( NAILS_DB_PREFIX . 'cms_block cb', 'cb.id = cbv.block_id' );
		$this->db->join( NAILS_DB_PREFIX . 'user u', 'u.id = cbv.created_by', 'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'user_email ue', 'ue.user_id = u.id AND ue.is_primary = 1', 'LEFT' );

		$this->db->order_by( 'cb.title' );

		$_blocks = $this->db->get( NAILS_DB_PREFIX . 'cms_block_translation cbv' )->result();

		$_out = array();

		for ( $i=0; $i < count( $_blocks ); $i++ ) :

			if ( ! isset( $_out[$_blocks[$i]->block_id] ) ) :

				 $_out[$_blocks[$i]->block_id]				= new stdClass();
				 $_out[$_blocks[$i]->block_id]->id			= $_blocks[$i]->block_id;
				 $_out[$_blocks[$i]->block_id]->type		= $_blocks[$i]->type;
 				 $_out[$_blocks[$i]->block_id]->slug		= $_blocks[$i]->slug;
 				 $_out[$_blocks[$i]->block_id]->title		= $_blocks[$i]->title;
 				 $_out[$_blocks[$i]->block_id]->description	= $_blocks[$i]->description;
 				 $_out[$_blocks[$i]->block_id]->located		= $_blocks[$i]->located;
 				 $_out[$_blocks[$i]->block_id]->translations	= array();

			endif;

			$_temp						= new stdClass();
			$_temp->id					= (int) $_blocks[$i]->id;
			$_temp->value				= $_blocks[$i]->value;
			$_temp->language			= $_blocks[$i]->language;
			$_temp->created				= $_blocks[$i]->created;
			$_temp->modified			= $_blocks[$i]->modified;
			$_temp->user				= new stdClass();
			$_temp->user->id			= $_blocks[$i]->created_by ? (int) $_blocks[$i]->created_by : NULL;
			$_temp->user->email			= $_blocks[$i]->email;
			$_temp->user->first_name	= $_blocks[$i]->first_name;
			$_temp->user->last_name		= $_blocks[$i]->last_name;
			$_temp->user->gender		= $_blocks[$i]->gender;
			$_temp->user->profile_img	= $_blocks[$i]->profile_img;

			// --------------------------------------------------------------------------

			//	Save the default version
			if ( $_blocks[$i]->language == APP_DEFAULT_LANG_CODE ) :

				$_out[$_blocks[$i]->block_id]->default_value = $_blocks[$i]->value;

			endif;

			// --------------------------------------------------------------------------

			//	Are we including revisions?
			if ( $include_revisions ) :

				$this->db->select( 'cbtr.*, ue.email, u.first_name, u.last_name, u.gender, u.profile_img' );
				$this->db->where( 'cbtr.block_translation_id', $_blocks[$i]->id );
				$this->db->join( NAILS_DB_PREFIX . 'user u', 'u.id = cbtr.created_by', 'LEFT' );
				$this->db->join( NAILS_DB_PREFIX . 'user_email ue', 'ue.user_id = u.id AND ue.is_primary = 1', 'LEFT' );
				$this->db->order_by( 'created', 'DESC' );
				$_temp->revisions = $this->db->get( NAILS_DB_PREFIX . 'cms_block_translation_revision cbtr' )->result();

				foreach( $_temp->revisions AS $revision ) :

					$revision->user					= new stdClass();
					$revision->user->id				= $revision->created_by ? (int) $revision->created_by : NULL;
					$revision->user->email			= $revision->email;
					$revision->user->first_name		= $revision->first_name;
					$revision->user->last_name		= $revision->last_name;
					$revision->user->gender			= $revision->gender;
					$revision->user->profile_img	= $revision->profile_img;

					unset( $revision->created_by );
					unset( $revision->email );
					unset( $revision->first_name );
					unset( $revision->last_name );
					unset( $revision->gender );
					unset( $revision->profile_img );

				endforeach;

				if ( $_blocks[$i]->language == APP_DEFAULT_LANG_CODE) :

					$_out[$_blocks[$i]->block_id]->default_value_revisions = $_temp->revisions;

				endif;

			endif;

			$_out[$_blocks[$i]->block_id]->translations[] = $_temp;

		endfor;

		// --------------------------------------------------------------------------

		return array_values( $_out );
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetch an object by it's ID
	 *
	 * @access public
	 * @param int $id The ID of the object to fetch
	 * @param bool $include_revisions Whether to include translation revisions
	 * @return stdClass
	 **/
	public function get_by_id( $id, $include_revisions = FALSE )
	{
		$this->db->where( 'cb.id', $id );
		$_result = $this->get_all( $include_revisions );

		// --------------------------------------------------------------------------

		if ( ! $_result )
			return FALSE;

		// --------------------------------------------------------------------------

		return $_result[0];
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetch an object by it's slug
	 *
	 * @access public
	 * @param string $slug The slug of the object to fetch
	 * @param bool $include_revisions Whether to include translation revisions
	 * @return stdClass
	 **/
	public function get_by_slug( $slug, $include_revisions = FALSE )
	{
		$this->db->where( 'cb.slug', $slug );
		$_result = $this->get_all( $include_revisions );

		// --------------------------------------------------------------------------

		if ( ! $_result )
			return FALSE;

		// --------------------------------------------------------------------------

		return $_result[0];
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

if ( ! defined( 'NAILS_ALLOW_EXTENSION_CMS_BLOCK_MODEL' ) ) :

	class Cms_block_model extends NAILS_Cms_block_model
	{
	}

endif;

/* End of file cms_block_model.php */
/* Location: ./application/models/cms_block_model.php */