<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Nails_CMS_Widget_richtext extends Nails_CMS_Widget
{
	static function details()
	{
		$_d			= parent::details();
		$_d->name	= 'Rich Text';
		$_d->iam	= 'Nails_CMS_Widget_richtext';
		$_d->slug	= 'Widget_richtext';
		$_d->info	= 'Build beautiful pages using the rich text editor; embed images, links and more.';
		
		return $_d;
	}
	
	// --------------------------------------------------------------------------
	
	
	private $_body;
	private $_key;
	
	// --------------------------------------------------------------------------
	
	public function __construct()
	{
		$this->_body	= '';
		$this->_key		= 'richtext';
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function setup( $data )
	{
		if ( isset( $data['body'] ) ) :
		
			$this->_body = $data['body'];
		
		endif;
		
		// --------------------------------------------------------------------------
		
		if ( isset( $data['key'] ) && ! is_null( $data['key'] ) ) :
		
			$this->_key = $data['key'];
		
		endif;
	}
	
	// --------------------------------------------------------------------------
	
	public function render()
	{
		return $this->_body;
	}
	
	// --------------------------------------------------------------------------
	
	public function get_editor_html()
	{
		$_details	= self::details();
		
		//	Include the slug as a hidden field, required for form rebuilding
		$_out = form_hidden( $this->_key . '[slug]', $_details->slug );
		
		// --------------------------------------------------------------------------
		
		//	Return editor HTML
		$_out .= form_textarea( $this->_key . '[body]', set_value( $this->_key . '[body]', $this->_body ), 'class="ckeditor"' );
		
		// --------------------------------------------------------------------------
		
		return $_out;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	protected function _editor_function_start()
	{
	
$_out  = <<<EOT
for ( var name in CKEDITOR.instances )
{
	var _data = CKEDITOR.instances[name].getData();
	CKEDITOR.instances[name].destroy(true);
	$( '.ckeditor[name="'+name+'"]' ).html(_data);
}
EOT;
		
		return $_out;
	}
	
	
	// --------------------------------------------------------------------------
	
	protected function _editor_function_stop()
	{
	
$_out  = <<<EOT
CKEDITOR.replaceAll( 'ckeditor' );
EOT;
		
		return $_out;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_validation_rules( $field )
	{
		$_rules = '';
		
		switch( $field ) :
		
			case 'body' :	$_rules = 'required';	break;
		
		endswitch;
		
		// --------------------------------------------------------------------------
		
		return $_rules;
	}
}