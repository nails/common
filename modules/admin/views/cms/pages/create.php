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
			$_field['placeholder']	= 'The title of the page';

			echo form_field( $_field );

			// --------------------------------------------------------------------------

			//	Slug
			$_field					= array();
			$_field['key']			= 'slug';
			$_field['label']		= 'Slug';
			$_field['required']		= TRUE;
			$_field['placeholder']	= 'The Page\'s slug.';

			echo form_field( $_field );

			?>
	</fieldset>

	<fieldset id="cms-page-edit-layout" class="layout">
		<legend>Page Layout</legend>
		<ul>
			<?php

				$_layout = $this->input->post( 'layout' );

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
					echo form_dropdown( 'sidebar_width', $_columns, set_value( 'sidebar_width' ) );

				?>
			</label>
		</p>
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
			$_field['placeholder']	= 'The page\'s SEO description';

			echo form_field( $_field, 'This should be kept short (< 160 characters) and concise. It\'ll be shown in search result listings and search engines will use it to help determine the page\'s content.' );

			// --------------------------------------------------------------------------

			//	Keywords
			$_field					= array();
			$_field['key']			= 'seo_keywords';
			$_field['label']		= 'Keywords';
			$_field['required']		= TRUE;
			$_field['placeholder']	= 'Comma separated keywords relating to the content of the page.';

			echo form_field( $_field, 'SEO good practice recommend keeping the number of keyword phrases below 10 and less than 160 characters in total.' );

			?>
	</fieldset>

	<p>
		<?php

			echo form_submit( 'submit', 'Continue to Widget Editing' );
			echo form_close();

		?>
	</p>

</div>