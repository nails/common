<div class="group-cms pages create">
	<?=form_open()?>
	<ul class="tabs" data-tabgroup="page">
		<li class="tab active">
			<a href="#" data-tab="tab-basics">Basic Info</a>
		</li>

		<li class="tab">
			<a href="#" id="tabber-layout" data-tab="tab-layout">Layout</a>
		</li>

		<li class="tab">
			<a href="#" id="tabber-widgets" class="disabled" rel="tipsy" title="You must create the page before you can edit widgets." data-tab="tab-widgets">Widgets</a>
		</li>

		<li class="tab">
			<a href="#" id="tabber-seo" data-tab="tab-seo">SEO</a>
		</li>
	</ul>
	<section class="tabs pages page">

		<div class="tab page basics active fieldset" id="tab-basics">
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
				$_field['key']			= 'parent_id';
				$_field['label']		= 'Parent Page';
				$_field['placeholder']	= 'The Page\'s parent.';
				$_field['class']		= 'chosen';

				$pages_nested_flat = array( '' => 'No Parent Page' ) + $pages_nested_flat;

				echo form_field_dropdown( $_field, $pages_nested_flat );

			?>
		</div>

		<div class="tab page layout fieldset" id="tab-layout">
			<ul>
				<li class="hero-sidebar-left">
					<label>
						<?=form_radio( 'layout', 'hero-sidebar-left', TRUE )?>
					</label>
				</li>
				<li class="hero-sidebar-right">
					<label>
						<?=form_radio( 'layout', 'hero-sidebar-right', FALSE )?>
					</label>
				</li>
				<li class="hero-full-width">
					<label>
						<?=form_radio( 'layout', 'hero-full-width', FALSE )?>
					</label>
				</li>
				<li class="no-hero-sidebar-left">
					<label>
						<?=form_radio( 'layout', 'no-hero-sidebar-left', FALSE )?>
					</label>
				</li>
				<li class="no-hero-sidebar-right">
					<label>
						<?=form_radio( 'layout', 'no-hero-sidebar-right', FALSE )?>
					</label>
				</li>
				<li class="no-hero-full-width">
					<label>
						<?=form_radio( 'layout', 'no-hero-full-width', FALSE )?>
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
		</div>

		<div class="tab page seo fieldset" id="tab-seo">
			<p>
				These fields are not visible anywhere but help Search Engines index and understand the page.
			</p>
			<?php

			//	Description
			$_field					= array();
			$_field['key']			= 'seo_description';
			$_field['type']			= 'textarea';
			$_field['label']		= 'Description';
			$_field['placeholder']	= 'The page\'s SEO description';

			echo form_field( $_field, 'This should be kept short (< 160 characters) and concise. It\'ll be shown in search result listings and search engines will use it to help determine the page\'s content.' );

			// --------------------------------------------------------------------------

			//	Keywords
			$_field					= array();
			$_field['key']			= 'seo_keywords';
			$_field['label']		= 'Keywords';
			$_field['placeholder']	= 'Comma separated keywords relating to the content of the page.';

			echo form_field( $_field, 'SEO good practice recommend keeping the number of keyword phrases below 10 and less than 160 characters in total.' );

			?>
		</div>

	</section>
	<p>
		<?=form_submit( 'submit', 'Proceed to widget editing' )?>
	</p>
	<?=form_close()?>
</div>