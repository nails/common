<?php

	//	Set the default template; either POST data, the one being used by the page, or the first in the list.
	if ( $this->input->post( 'template' ) ) :

		$_default_template = $this->input->post( 'template' );

	elseif ( isset( $cmspage->template->slug ) ) :

		$_default_template = $cmspage->template->slug;

	else :

		reset( $templates );
		$_default_template = key( $templates );

	endif;

?>
<div class="group-cms pages edit">
	<?=form_open()?>

	<fieldset>
		<legend>Page Data</legend>
		<?php

			//	Title
			$_field					= array();
			$_field['key']			= 'title';
			$_field['label']		= 'Title';
			$_field['required']		= TRUE;
			$_field['default']		= isset( $cmspage->title ) ? $cmspage->title : '';
			$_field['placeholder']	= 'The title of the page';

			echo form_field( $_field );

			// --------------------------------------------------------------------------

			//	Title
			$_field					= array();
			$_field['key']			= 'is_published';
			$_field['label']		= 'Published';
			$_field['required']		= TRUE;
			$_field['default']		= isset( $cmspage->is_published ) ? $cmspage->is_published : '';
			$_field['placeholder']	= 'The title of the page';

			echo form_field_boolean( $_field );

			// --------------------------------------------------------------------------

			//	Slug
			$_field					= array();
			$_field['key']			= 'parent_id';
			$_field['label']		= 'Parent Page';
			$_field['placeholder']	= 'The Page\'s parent.';
			$_field['class']		= 'chosen';
			$_field['default']		= isset( $cmspage->parent_id ) ? $cmspage->parent_id : '';

			$pages_nested_flat = array( '' => 'No Parent Page' ) + $pages_nested_flat;

			echo form_field_dropdown( $_field, $pages_nested_flat );

			// --------------------------------------------------------------------------

			//	Description
			$_field					= array();
			$_field['key']			= 'seo_description';
			$_field['label']		= 'SEO Description';
			$_field['default']		= isset( $cmspage->seo_description ) ? $cmspage->seo_description : '';
			$_field['placeholder']	= 'The page\'s SEO description, keep this short and concise. Recommended to keep below 160 characters.';

			echo form_field( $_field, 'This should be kept short (< 160 characters) and concise. It\'ll be shown in search result listings and search engines will use it to help determine the page\'s content.' );

			// --------------------------------------------------------------------------

			//	Keywords
			$_field					= array();
			$_field['key']			= 'seo_keywords';
			$_field['label']		= 'SEO Keywords';
			$_field['default']		= isset( $cmspage->seo_keywords ) ? $cmspage->seo_keywords : '';
			$_field['placeholder']	= 'Comma separated keywords relating to the content of the page. A maximum of 10 keywords is recommended.';

			echo form_field( $_field, 'SEO good practice recommend keeping the number of keyword phrases below 10 and less than 160 characters in total.' );

		?>
	</fieldset>

	<fieldset>
		<legend>Template</legend>
		<ul class="templates">
		<?php

			foreach( $templates AS $template ) :

				echo '<li>';

					//	This template selected?
					$_selected = $_default_template == $template->slug ? TRUE : FALSE;

					//	Define attributes
					$_attr							= array();
					$_attr['class']					= $_selected ? 'template selected' : 'template';
					$_attr['data-template-slug']	= $template->slug;

					//	Glue together
					$_attr_str = '';
					foreach ( $_attr AS $key => $value ) :

						$_attr_str .= $key . '="' . $value . '" ';

					endforeach;

					echo '<label ' . trim( $_attr_str ) . '>';

						echo form_radio( 'template', $template->slug, set_radio( 'template', $template->slug, $_selected ) );

						$_background = $template->img->icon ? 'style="background-image:url(' . $template->img->icon . ');background-position:center top;"' : '';
						echo '<span class="icon" ' . $_background . '></span>';
						echo '<span class="newrow"></span>';
						echo '<span class="name">';
							echo '<span class="checkmark ion-checkmark-circled"></span>';
							echo '<span>' . $template->label . '</span>';
						echo '</span>';
					echo '</label>';
				echo '</li>';

			endforeach;

		?>
		</ul>
	</fieldset>

	<fieldset>
		<legend>Page Content</legend>
		<p>
			Choose which area of the page you'd like to edit.
		</p>
		<p>
		<?php

			foreach( $templates AS $template ) :

				//	This template selected?
				$_selected = $_default_template == $template->slug ? TRUE : FALSE;

				foreach ( $template->widget_areas AS $slug => $area ) :

					//	Define attributes
					$_data				= array();
					$_data['area-slug']	= $slug;

					$_attr = '';
					foreach ( $_data AS $key => $value ) :

						$_attr .= 'data-' . $key . '="' . $value . '" ';

					endforeach;

					//	Define attributes
					$_attr					= array();
					$_attr['class']			= 'awesome launch-editor template-' . $template->slug;
					$_attr['style']			= $_selected ? 'display:inline-block;' : 'display:none;';
					$_attr['data-template']	= $template->slug;
					$_attr['data-area']		= $slug;

					//	Glue together
					$_attr_str = '';
					foreach ( $_attr AS $key => $value ) :

						$_attr_str .= $key . '="' . $value . '" ';

					endforeach;

					echo '<a href="#" ' . trim( $_attr_str ) . '>' . $area->title . '</a>';

				endforeach;

			endforeach;

		?>
		</p>
	</fieldset>

	<p>
		<?php

			echo form_submit( 'submit', lang( 'action_save_changes' ) );
			echo form_close();

		?>
	</p>

</div>

<script type="text/javascript">
<!--//

	$(function(){

		var CMS_PAGES = new NAILS_Admin_CMS_pages_Create_Edit;
		CMS_PAGES.init(<?=json_encode( $templates )?>, <?=json_encode( $widgets )?>);

	});

//-->
</script>
<script type="text/template" id="template-header">
	<ul>
		<li>
			Currently editing: {{active_area}}
		</li>
	</ul>
	<ul class="rhs">
		<li><a href="#" data-action="apply">Apply Changes</a></li>
		<li><a href="#" data-action="preview">Preview</a></li>
		<li><a href="#" data-action="help">Help</a></li>
		<li><a href="#" data-action="close">Close</a></li>
	</ul>
</script>
<script type="text/template" id="template-widget-search">
	<input type="search" placeholder="Search widget library" />
	<a href="#" class="minimiser">
		<span class="ion-navicon-round"></span>
	</a>
</script>
<script type="text/template" id="template-widget-grouping">
	<li class="grouping open" data-group="{{group}}">
		<span class="icon ion-ios7-folder"></span>
		<span class="label">{{name}}</span>
		<span class="toggle-open right ion-arrow-down-b"></span>
		<span class="toggle-closed right ion-arrow-right-b"></span>
	</li>
</script>
<script type="text/template" id="template-widget">
	<li class="widget {{group}} {{slug}}" data-slug="{{slug}}" data-title="{{name}} Widget" data-keywords="{{keywords}}">
		<span class="icon ion-arrow-move"></span>
		<span class="label">{{name}}</span>
		{{#description}}<span class="description">{{description}}</span>{{/description}}
	</li>
</script>
<script type="text/template" id="template-dropzone-empty">
	<li class="empty">
		<div class="valigned">
			<p class="title">No widgets</p>
			<p class="label">Drag widgets from the left to start building your page.</p>
		</div>
		<div class="valigned-helper"></div>
	</li>
</script>
<script type="text/template" id="template-dropzone-widget">
	<div class="header-bar">
		<span class="sorter">
			<span class="ion-arrow-move"></span>
		</span>
		<span class="label">{{label}}</span>
		<span class="closer ion-trash-a"></span>
		{{#description}}<span class="description">{{description}}</span>{{/description}}
	</div>
	<div class="editor">
		<p style="text-align:center;">
			<span class="ion-looping"></span>
			<br />
			Please wait, loading widget
		</p>
	</div>
</script>