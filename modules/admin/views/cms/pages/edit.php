<div class="group-cms pages edit">
	<?=form_open()?>
	<fieldset id="cms-page-edit-meta" <?=$widgets_only ? 'style="display:none;"' : ''?>>
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

	<fieldset id="cms-page-edit-layout" class="layout" <?=$widgets_only ? 'style="display:none;"' : ''?>>
		<legend>Page Layout</legend>
		<ul>
			<?php

				$_layout = $this->input->post( 'layout' ) ? $this->input->post( 'layout' ) : $cmspage->layout;

			?>
			<li class="hero-sidebar-left">
				<label>
					<?=form_radio( 'layout', 'hero-sidebar-left', ( $_layout == 'hero-sidebar-left' ) )?>
				</label>
			</li>
			<li class="hero-sidebar-right">
				<label>
					<?=form_radio( 'layout', 'hero-sidebar-right', ( $_layout == 'hero-sidebar-right' ) )?>
				</label>
			</li>
			<li class="hero-full-width">
				<label>
					<?=form_radio( 'layout', 'hero-full-width', ( $_layout == 'hero-full-width' ) )?>
				</label>
			</li>
			<li class="no-hero-sidebar-left">
				<label>
					<?=form_radio( 'layout', 'no-hero-sidebar-left', ( $_layout == 'no-hero-sidebar-left' ) )?>
				</label>
			</li>
			<li class="no-hero-sidebar-right">
				<label>
					<?=form_radio( 'layout', 'no-hero-sidebar-right', ( $_layout == 'no-hero-sidebar-right' ) )?>
				</label>
			</li>
			<li class="no-hero-full-width">
				<label>
					<?=form_radio( 'layout', 'no-hero-full-width', ( $_layout == 'no-hero-full-width' ) )?>
				</label>
			</li>
		</ul>
		<p id="cms-page-edit-sidebar-width">
			<label>
				Sidebar Width:
				<?php

					$_columns = array();
					for( $i=1; $i<=8; $i++ ) :

						$_columns[$i] = $i . ' columns';

					endfor;
					echo form_dropdown( 'sidebar_width', $_columns, set_value( 'sidebar_width', $cmspage->sidebar_width ) );

				?>
			</label>
		</p>
	</fieldset>

	<?php

		//	Hero editor
		$_config = array( 'config' => array() );
		$_config['config']['area']					= 'hero';
		$_config['config']['accept_widgets_for']	= array( 'ALL', 'HERO', 'HERO_BODY', 'HERO_SIDEBAR' );
		$_config['config']['title']					= 'Page Hero';
		$_config['config']['description']			= 'Drag widgets to the right to build your page. Change the order of widgets by dragging the handle of the editor.';

		$this->load->view( 'admin/cms/pages/_editor', $_config  );

		// --------------------------------------------------------------------------

		//	Body editor
		$_config = array( 'config' => array() );
		$_config['config']['area']					= 'body';
		$_config['config']['accept_widgets_for']	= array( 'ALL', 'BODY', 'HERO_BODY', 'BODY_SIDEBAR' );
		$_config['config']['title']					= 'Page Body';
		$_config['config']['description']			= 'Drag widgets to the right to build your page. Change the order of widgets by dragging the handle of the editor.';

		$this->load->view( 'admin/cms/pages/_editor', $_config  );

		// --------------------------------------------------------------------------

		//	Sidebar editor
		$_config = array( 'config' => array() );
		$_config['config']['area']					= 'sidebar';
		$_config['config']['accept_widgets_for']	= array( 'ALL', 'SIDEBAR', 'HERO_SIDEBAR', 'BODY_SIDEBAR' );
		$_config['config']['title']					= 'Page Sidebar';
		$_config['config']['description']			= 'Drag widgets to the right to build your page. Change the order of widgets by dragging the handle of the editor.';

		$this->load->view( 'admin/cms/pages/_editor', $_config  );

	?>

	<fieldset id="cms-page-edit-seo" <?=$widgets_only ? 'style="display:none;"' : ''?>>
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

<script type="text/javascript">
<!--//

	$(function(){

		//	Hero
		var CMS_Pages_hero = new NAILS_Admin_CMS_Pages_Editor;
		CMS_Pages_hero.init( 'hero' );

		//	Body
		var CMS_Pages_body = new NAILS_Admin_CMS_Pages_Editor;
		CMS_Pages_body.init( 'body' );

		//	Sidebar
		var CMS_Pages_sidebar = new NAILS_Admin_CMS_Pages_Editor;
		CMS_Pages_sidebar.init( 'sidebar' );

		// --------------------------------------------------------------------------

		//	Handle the layout selector
		function sort_layout( type )
		{
			switch ( type )
			{
				case 'hero-sidebar-left' :
				case 'hero-sidebar-right' :

					$( '#cms-page-edit-hero' ).show();
					$( '#cms-page-edit-body' ).show();
					$( '#cms-page-edit-sidebar' ).show();
					$( '#cms-page-edit-sidebar-width' ).show();

				break;

				// --------------------------------------------------------------------------

				case 'hero-full-width' :

					$( '#cms-page-edit-hero' ).show();
					$( '#cms-page-edit-body' ).show();
					$( '#cms-page-edit-sidebar' ).hide();
					$( '#cms-page-edit-sidebar-width' ).hide();

				break;

				// --------------------------------------------------------------------------

				case 'no-hero-sidebar-left' :
				case 'no-hero-sidebar-right' :

					$( '#cms-page-edit-hero' ).hide();
					$( '#cms-page-edit-body' ).show();
					$( '#cms-page-edit-sidebar' ).show();
					$( '#cms-page-edit-sidebar-width' ).show();

				break;

				// --------------------------------------------------------------------------

				case 'no-hero-full-width' :

					$( '#cms-page-edit-hero' ).hide();
					$( '#cms-page-edit-body' ).show();
					$( '#cms-page-edit-sidebar' ).hide();
					$( '#cms-page-edit-sidebar-width' ).hide();

				break;

			}
		}

		//	Catch selections
		$( 'input[name=layout]' ).on( 'click', function()
		{
			sort_layout( $(this).val());
		});

		//	Process the current layout
		sort_layout( '<?=$_layout?>' );


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
		echo $this->cms_page->get_widget_editor( $widget->slug, NULL, 'widgets_{{key}}[new-{{counter}}]' );
		echo '</div>';
		echo '</script>';

		echo '<script type="text/javascript">';
		echo $this->cms_page->get_widget_editor_functions( $widget->slug, NULL, 'widgets__{{key}}[new-{{counter}}]' );
		echo '</script>';

	endforeach;
?>