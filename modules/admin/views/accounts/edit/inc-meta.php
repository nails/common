<fieldset id="edit-user-meta">
	<legend><?=lang( 'accounts_edit_meta_legend' )?></legend>
	<?php
		
		if ( $user_meta ) :
		
			foreach ( $user_meta AS $field => $value ) :
			
				//	Always ignore some fields
				if ( array_search( $field, $ignored_fields ) !== FALSE )
					continue;
				
				// --------------------------------------------------------------------------
					
				$_datatype = isset( $user_meta_cols[$field]['datatype'] ) ? $user_meta_cols[$field]['datatype'] : 'string';
				
				$_field						= array();
				$_field['key']				= $field;
				$_field['type']				= isset( $user_meta_cols[$field]['type'] ) ? $user_meta_cols[$field]['type'] : 'text';
				$_field['label']			= isset( $user_meta_cols[$field]['label'] ) ? $user_meta_cols[$field]['label'] : ucwords( str_replace( '_', ' ', $field ) );
				$_field['required']			= isset( $user_meta_cols[$field]['required'] ) ? $user_meta_cols[$field]['required'] : FALSE;
				$_field['default']			= $value;
				
				switch ( $_datatype ) :
				
					case 'bool':
					case 'boolean' :
					
						$_options = array(
							lang( 'no' ),
							lang( 'yes' )
						);
						echo form_field_dropdown( $_field, $_options );
					
					break;
					
					// --------------------------------------------------------------------------
					
					case 'date' :
					
						echo form_field_date( $_field );
					
					break;
					
					// --------------------------------------------------------------------------
					
					case 'id' :
					
						//	Fetch items from the joining table
						
						if ( isset( $user_meta_cols[$field]['join'] ) ) :
						
							$_table			= isset( $user_meta_cols[$field]['join']['table'] ) 	?  $user_meta_cols[$field]['join']['table']		: NULL;
							$_select_id		= isset( $user_meta_cols[$field]['join']['id'] )		?  $user_meta_cols[$field]['join']['id']		: NULL;
							$_select_name	= isset( $user_meta_cols[$field]['join']['name'] )		?  $user_meta_cols[$field]['join']['name']		: NULL;
							$_order_col		= isset( $user_meta_cols[$field]['join']['order_col'] )	?  $user_meta_cols[$field]['join']['order_col']	: NULL;
							$_order_dir		= isset( $user_meta_cols[$field]['join']['order_dir'] )	?  $user_meta_cols[$field]['join']['order_dir']	: 'ASC';
							
							if ( $_table && $_select_id && $_select_name ) :
							
								$this->db->select( $_select_id . ',' . $_select_name );
								
								if ( $_order_col ) :
								
									$this->db->order_by( $_order_col, $_order_dir );
								
								endif;
								
								$_results = $this->db->get( $_table )->result();
								$_options = array();
								
								foreach ( $_results AS $row ) :
								
									$_options[$row->{$_select_id}] = $row->{$_select_name};
								
								endforeach;
	
								echo form_field_dropdown( $_field, $_options );
							
							else :
							
								echo form_field( $_field );
							
							endif;
						
						else :
						
							echo form_field( $_field );
							
						endif;
					
					break;
					
					// --------------------------------------------------------------------------
					
					case 'file' :
					case 'upload' :
					
						$_field['bucket'] = isset( $user_meta_cols[$field]['bucket'] ) ? $user_meta_cols[$field]['bucket'] : FALSE;
						if ( isset( ${'upload_error_' . $_field['key']} )) :
						
							$_field['error'] = implode( ' ', ${'upload_error_' . $_field['key']} );
						
						endif;
						
						echo form_field( $_field );
					
					break;
					
					// --------------------------------------------------------------------------
					
					case 'string':
					default:
					
						echo form_field( $_field );
					
					break;
				
				endswitch;
			
			endforeach;
		
		else :
		
			echo '<p>' . lang( 'accounts_edit_meta_noeditable' ) . '</p>';
		
		endif;
	
	?>
</fieldset>