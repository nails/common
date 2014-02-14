<style type="text/css">
	div.ui-front
	{
		z-index:1000;
	}
</style>
<?php

	//	Set the default template; either POST data, the one being used by the page, or the first in the list.
	if ( $this->input->post( 'template' ) ) :

		$_default_template = $this->input->post( 'template' );

	elseif ( ! empty( $cmspage->draft->template ) ) :

		$_default_template = $cmspage->draft->template;

	else :

		reset( $templates );
		$_default_template = key( $templates );

	endif;

?>
<div class="group-cms pages edit">

	<?php

		switch( $this->input->get( 'message' ) ) :

			case 'saved' :

				echo '<p class="system-alert success no-close">';
					echo '<strong>Success!</strong> Your page was saved successfully. ' . anchor( 'cms/render/preview/' . $cmspage->id, 'Preview it here', 'class="main-action" data-action="preview" target="_blank"' );
				echo '</p>';

			break;

			// --------------------------------------------------------------------------

			case 'published' :

				echo '<p class="system-alert success no-close">';
					echo '<strong>Success!</strong> Your page was published successfully. ' . anchor( $cmspage->published->url, 'View it here', 'target="_blank"' );
				echo '</p>';

			break;

			// --------------------------------------------------------------------------

			case 'unpublished' :

				echo '<p class="system-alert success no-close">';
					echo '<strong>Success!</strong> Your page was unpublished successfully.';
				echo '</p>';

			break;

		endswitch;

	?>

	<div class="system-alert notice no-close" id="save-status">
		<p>
			<small>
				Last Saved: <span class="last-saved">Not Saved</span>
				<span class="ion-looping"></span>
			</small>
		</p>
	</div>

	<fieldset>
		<legend>Page Data</legend>
		<?php

			//	Title
			$_field					= array();
			$_field['key']			= 'title';
			$_field['label']		= 'Title';
			$_field['required']		= TRUE;
			$_field['default']		= isset( $cmspage->draft->title ) ? html_entity_decode( $cmspage->draft->title, ENT_COMPAT | ENT_HTML5, 'UTF-8' ) : '';
			$_field['placeholder']	= 'The title of the page';

			echo form_field( $_field );

			// --------------------------------------------------------------------------

			//	Parent ID
			$_field						= array();
			$_field['key']				= 'parent_id';
			$_field['label']			= 'Parent Page';
			$_field['placeholder']		= 'The Page\'s parent.';
			$_field['class']			= 'chosen';
			$_field['default']			= isset( $cmspage->draft->parent_id ) ? $cmspage->draft->parent_id : '';
			$_field['disabled_options']	= isset( $page_children ) ? $page_children : array();

			//	Remove this page from the available options; INFINITE LOOP
			//	We also need to remove any items which are

			if ( isset( $cmspage ) ) :

				foreach( $pages_nested_flat AS $id => $label ) :

					if ( $id == $cmspage->id ) :

						$_field['disabled_options'][] = $id;
						break;

					endif;

				endforeach;

			endif;

			if ( count( $pages_nested_flat ) && count( $_field['disabled_options'] ) < count( $pages_nested_flat ) ) :

				$pages_nested_flat = array( '' => 'No Parent Page' ) + $pages_nested_flat;

				// --------------------------------------------------------------------------

				if ( count( $_field['disabled_options'] ) ) :

					$_field['info']	= '<strong>Some options have been disabled.</strong> You cannot set the parent page to this page or any existing child of this page.';

				endif;

				echo form_field_dropdown( $_field, $pages_nested_flat );

			else :

				echo form_hidden( $_field['key'], '' );

			endif;

			// --------------------------------------------------------------------------

			//	SEO Description
			$_field					= array();
			$_field['key']			= 'seo_description';
			$_field['label']		= 'SEO Description';
			$_field['default']		= isset( $cmspage->draft->seo_description ) ? html_entity_decode( $cmspage->draft->seo_description, ENT_COMPAT | ENT_HTML5, 'UTF-8' ) : '';
			$_field['placeholder']	= 'The page\'s SEO description, keep this short and concise. Recommended to keep below 160 characters.';

			echo form_field( $_field, 'This should be kept short (< 160 characters) and concise. It\'ll be shown in search result listings and search engines will use it to help determine the page\'s content.' );

			// --------------------------------------------------------------------------

			//	SEO Keywords
			$_field					= array();
			$_field['key']			= 'seo_keywords';
			$_field['label']		= 'SEO Keywords';
			$_field['default']		= isset( $cmspage->draft->seo_keywords ) ? html_entity_decode( $cmspage->draft->seo_keywords, ENT_COMPAT | ENT_HTML5, 'UTF-8' ) : '';
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

	<?php

		if ( isset( $cmspage ) && $cmspage->is_published && $cmspage->published->hash !== $cmspage->draft->hash ) :

			echo '<p class="system-alert message no-close">';
				echo '<strong>You have unpublished changes.</strong><br />This version of the page is more recent than the version currently published on site. When you\'re done make sure you click "Publish Changes" below.';
			echo '</p>';

		endif;

	?>

	<p class="actions">
	<?php

		echo '<a href="#" data-action="save" class="main-action awesome orange large" rel="tipsy-top" title="Your changes will be saved so you can come back later, but won\'t be published on site.">Save Changes</a>';
		echo '<a href="#" data-action="publish" class="main-action awesome green large" rel="tipsy-top" title="Your changes will be published on site and will take hold immediately.">Publish Changes</a>';
		echo '<a href="#" data-action="preview" class="main-action awesome large launch-preview right">' . lang( 'action_preview' ) . '</a>';

	?>
	</p>

</div>

<script type="text/javascript">
<!--//

	$(function(){

		var CMS_PAGES = new NAILS_Admin_CMS_pages_Create_Edit;
		CMS_PAGES.init(<?=json_encode( $templates )?>, <?=json_encode( $widgets )?>, <?=isset( $cmspage->id ) ? $cmspage->id : 'null' ?>, <?=isset( $cmspage->draft->template_data ) ? json_encode( $cmspage->draft->template_data ) : 'null' ?> );

	});

//-->
</script>
<script type="text/template" id="template-loader">
	<span class="ion-looping"></span>
</script>
<script type="text/template" id="template-header">
	<ul>
		<li>
			Currently editing: {{active_area}}
		</li>
	</ul>
	<ul class="rhs">
		<li><a href="#" class="main-action" data-action="preview">Preview</a></li>
		<li><a href="#" class="action" data-action="close">Close</a></li>
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
	<form class="editor">
		<p style="text-align:center;">
			<span class="ion-looping"></span>
			<br />
			Please wait, loading widget
		</p>
	</form>
</script>