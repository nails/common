<div class="group-cms pages edit">

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
	
	<fieldset id="cms-page-edit-seo" class="editor">
		<legend>Editor</legend>
		<p>
			Drag widgets to the right to build your page. Change the order of widgets by dragging the handle of the editor.
		</p>
		<div class="widgets">
		<?php
		
			//	Get the widget draggables
			foreach( $widgets AS $widget ) :
			
				echo $this->cms_page->get_widget_editor_draggable( $widget->slug );
			
			endforeach;	
		?>
		</div>
		<ul class="holders <?=$cmspage->widgets ? '' : 'empty'?>">
		<li class="empty">
			<p>Drag an item here from the sidebar to begin editing!</p>
		</li>
		<?php
		
			//	Get the widget editors
			$_counter = 0;
			foreach( $cmspage->widgets AS $widget ) :
			
				echo $this->cms_page->get_widget_editor( $widget->widget_class, $widget->widget_data, 'old_widget[' . $widget->id . ']' );
				
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

	//	Get the widget templates
	foreach( $widgets AS $widget ) :
	
		echo '<script type="text/template" id="' . $widget->slug . '">';
		echo $this->cms_page->get_widget_editor( $widget->slug, NULL, 'new_widget[{{counter}}]' );
		echo '</script>';
	
	endforeach;	
?>