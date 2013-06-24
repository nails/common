<fieldset id="cms-page-edit-<?=$config['area']?>" class="editor">
	<legend><?=$config['title']?></legend>
	<p>
		<?=$config['description']?>
	</p>
	<div class="widgets">
	<?php
	
		//	Get the widget draggables

		foreach( $widgets AS $widget ) :

			if ( array_search( $widget->restrict_area, $config['accept_widgets_for'] ) !== FALSE ) :
			
				$_class = $widgets[$widget->slug]->iam;
				
				echo '<li class="widget ' . $widget->slug . '" data-template="' . $widget->slug . '">';
				echo $_class::details()->name;
				echo '</li>';

			endif;
		
		endforeach;	
	?>
	</div>

	<?php

		//	If post data has been defined, use that
		if ( is_array( $this->input->post( 'widgets_' . $config['area'] ) ) ) :

			$_empty = $this->input->post( 'widgets_' . $config['area'] ) ? '' : 'empty';

		else :

			$_empty = $cmspage->{'widgets_' . $config['area']} ? '' : 'empty';

		endif;

	?>
	<ul class="holders <?=$_empty?>">
	<li class="empty">
		<p>Drag an item here from the sidebar to begin editing!</p>
	</li>
	<?php
	
		//	Get the widget editors, depending on the source, we need
		//	to loop and prepare the data.
		
		//	If post data has been defined, use that
		if ( is_array( $this->input->post( 'widgets_' . $config['area'] ) ) ) :
		
			$_widgets = array();
			
			//	Define the slug, key and data
			foreach( $this->input->post( 'widgets_' . $config['area'] ) AS $key => $data ) :
			
				$_temp = new stdClass();
				
				//	Slug
				$_temp->slug	= $data['slug'];
				unset( $data['slug'] );
				
				//	Key
				$_temp->key		= $key;
				
				//	Data
				$_temp->data	= serialize( $data );
				
				// --------------------------------------------------------------------------
				
				$_widgets[]		= $_temp;
			
			endforeach;
		
		else :
		
			$_widgets = array();
							
			//	Define the slug, key and data
			foreach( $cmspage->{'widgets_' . $config['area']} AS $widget ) :

				$_temp = new stdClass();
				
				//	Slug
				$_temp->slug	= $widget->widget_class;
				
				//	Key
				$_temp->key		= 'old-' . $widget->id;
				
				//	Data
				$_temp->data	= $widget->widget_data;
				
				// --------------------------------------------------------------------------
				
				$_widgets[]		= $_temp;
			
			endforeach;
		
		endif;
		
		$_counter = 0;
		foreach( $_widgets AS $widget ) :
		
			$_class = $widgets[$widget->slug]->iam;
			
			//	Handle
			echo '<li class="holder ' . $widget->slug . '" data-template="' . $widget->slug . '">';
			echo '<h2 class="handle">';
			echo $_class::details()->name;
			echo '<small>' . $_class::details()->info . '</small>';
			echo '<a href="#" class="close">Close</a>';
			echo '</h2>';
			
			//	Editor
			echo '<div class="editor-content">';
			
				//	Any errors?
				$_errors = '';
				foreach ( $this->form_validation->get_error_array() AS $field => $message ) :
				
					if ( preg_match( '/widgets_' . $config['area'] . '\[' . $widget->key . '\]\[(.*)\]/', $field, $_matches ) ) :
					
						
						$_errors .= '<br />&rsaquo; ' . ucwords( str_replace( '_', ' ', $_matches[1] ) ) .' - ' . $message;
					
					endif;
				
				endforeach;
				
				if ( $_errors ) :
				
					echo '<p class="system-alert error no-close">';
					echo '<strong>There are errors in this widget:</strong>';
					echo $_errors;
					echo '</p>';
				
				endif;
				
				// --------------------------------------------------------------------------
				
				echo $this->cms_page->get_widget_editor( $widget->slug, $widget->data, 'widgets_' . $config['area'] . '[' . $widget->key . ']' );
				
			echo '</div>';
			echo '<li>';
			
			$_counter++;
		
		endforeach;	
	?>
	</ul>
</fieldset>