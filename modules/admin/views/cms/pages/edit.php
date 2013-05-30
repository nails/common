<div class="group-cms pages edit">
	<?=form_open()?>
	<fieldset id="cms-page-edit-meta">
		<legend>Meta Data</legend>
			<?php
			
			//	Title
			$_field					= array();
			$_field['key']			= 'title';
			$_field['label']		= 'Title';
			$_field['required']		= TRUE;
			$_field['default']		= $cmspage->title;
			$_field['placeholder']	= 'The title of the page';
			
			echo form_field( $_field );
			
			// --------------------------------------------------------------------------
			
			//	Slug
			$_field					= array();
			$_field['key']			= 'slug';
			$_field['label']		= 'Slug';
			$_field['required']		= TRUE;
			$_field['default']		= $cmspage->slug;
			$_field['placeholder']	= 'The Page\'s slug.';
			
			echo form_field( $_field );
			
			?>
	</fieldset>

	<fieldset id="cms-page-edit-layout" class="editor">
		<legend>Page Layout</legend>
	</fieldset>
	
	<fieldset id="cms-page-edit-body" class="editor">
		<legend>Page Body</legend>
		<p>
			Drag widgets to the right to build your page. Change the order of widgets by dragging the handle of the editor.
		</p>
		<div class="widgets">
		<?php
		
			//	Get the widget draggables
			foreach( $widgets AS $widget ) :
			
				$_class = $widgets[$widget->slug]->iam;
				
				echo '<li class="widget ' . $widget->slug . '" data-template="' . $widget->slug . '">';
				echo $_class::details()->name;
				echo '</li>';
			
			endforeach;	
		?>
		</div>
		<ul class="holders <?=$cmspage->widgets ? '' : 'empty'?>">
		<li class="empty">
			<p>Drag an item here from the sidebar to begin editing!</p>
		</li>
		<?php
		
			//	Get the widget editors, depending on the source, we need
			//	to loop and preparethe data.
			
			//	If post data has been defined, use that
			if ( is_array( $this->input->post( 'widgets' )  ) ) :
			
				$_widgets = array();
				
				//	Define the slug, key and data
				foreach( $this->input->post( 'widgets' ) AS $key => $data ) :
				
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
				foreach( $cmspage->widgets AS $widget ) :
				
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
					
						if ( preg_match( '/widgets\[' . $widget->key . '\]\[(.*)\]/', $field, $_matches ) ) :
						
							
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
					
					echo $this->cms_page->get_widget_editor( $widget->slug, $widget->data, 'widgets[' . $widget->key . ']' );
					
				echo '</div>';
				echo '<li>';
				
				$_counter++;
			
			endforeach;	
		?>
		</ul>
	</fieldset>
	
	<fieldset id="cms-page-edit-seo">
		<legend>Search Engine Optimisation</legend>
			<p>
				These fields are not visible anywhere but help Search Engines index and understand the page.
			</p>
			<?php
			
			//	Description
			$_field					= array();
			$_field['key']			= 'seo_description';
			$_field['type']			= 'textarea';
			$_field['label']		= 'Description';
			$_field['required']		= TRUE;
			$_field['default']		= $cmspage->seo_description;
			$_field['placeholder']	= 'The page\'s SEO description';
			
			echo form_field( $_field, 'This should be kept short (< 160 characters) and concise. It\'ll be shown in search result listings and search engines will use it to help determine the page\'s content.' );
			
			// --------------------------------------------------------------------------
			
			//	Keywords
			$_field					= array();
			$_field['key']			= 'seo_keywords';
			$_field['label']		= 'Keywords';
			$_field['required']		= TRUE;
			$_field['default']		= $cmspage->seo_keywords;
			$_field['placeholder']	= 'Comma separated keywords relating to the content of the page.';
			
			echo form_field( $_field, 'SEO good practice recommend keeping the number of keyword phrases below 10 and less than 160 characters in total.' );
			
			?>
	</fieldset>
	
	<p>
		<?php
		
			echo form_submit( 'submit', lang( 'action_save_changes' ) );
			echo form_close();
			
		?>
	</p>

</div>

<script style="text/javascript">
<!--//

	$(function(){
	
		var CMS_Pages = new NAILS_Admin_CMS_Pages;
		CMS_Pages.init_edit();
	
	});

//-->
</script>

<?php

	//	Get the widget templates and functions
	foreach( $widgets AS $widget ) :
	
		$_class = $widgets[$widget->slug]->iam;
		
		echo '<script type="text/template" id="' . $widget->slug . '">';
		echo '<h2 class="handle">';
		echo $_class::details()->name;
		echo '<small>' . $_class::details()->info . '</small>';
		echo '<a href="#" class="close">Close</a>';
		echo '</h2>';
		echo '<div class="editor-content">';
		echo $this->cms_page->get_widget_editor( $widget->slug, NULL, 'widgets[new-{{counter}}]' );
		echo '</div>';
		echo '</script>';
		
		echo '<script type="text/javascript">';
		echo $this->cms_page->get_widget_editor_functions( $widget->slug, NULL, 'widgets[new-{{counter}}]' );
		echo '</script>';
	
	endforeach;	
?>