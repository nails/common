<div class="group-cms sliders overview">

	<p>
		Listed below are all the editable sliders on site.
		<?php

			if ( user_has_permission( 'admin.cms.can_create_slider' ) ) :

				echo anchor( 'admin/cms/sliders/create', 'Add New Slider', 'class="awesome small green right"' );

			endif;

		?>
	</p>

	<hr />

	<div class="search">
		<div class="search-text">
			<input type="text" name="search" value="" autocomplete="off" placeholder="Search slider titles by typing in here...">
		</div>
	</div>

	<hr />

	<table>
		<thead>
			<tr>
				<th class="title">Slider</th>
				<th class="user">Modified By</th>
				<th class="datetime">Modified</th>
				<th class="actions">Actions</th>
			</tr>
		</thead>
		<tbody>
		<?php

			if ( $sliders ) :

				foreach ( $sliders AS $slider ) :

					echo '<tr class="slider" data-label="' . $slider->label . '">';
						echo '<td class="label">';
							echo $slider->label;
							echo $slider->description ? '<small>' . $slider->description . '</small>' : '';
						echo '</td>';

						$this->load->view( 'admin/_utilities/table-cell-user',		$slider->modified_by );
						$this->load->view( 'admin/_utilities/table-cell-datetime',	array( 'datetime' => $slider->modified ) );

						echo '<td class="actions">';

							if ( user_has_permission( 'admin.cms.can_edit_slider' ) ) :

								echo anchor( 'admin/cms/sliders/edit/' . $slider->id, lang( 'action_edit' ), 'class="awesome small"' );

							endif;

							if ( user_has_permission( 'admin.cms.can_delete_slider' ) ) :

								echo anchor( 'admin/cms/sliders/delete/' . $slider->id, lang( 'action_delete' ), 'data-title="Are you sure?" data-body="This will remove the slider from the site. This action can be undone." class="confirm awesome small red"' );

							endif;

						echo '</td>';
					echo '</tr>';

				endforeach;

			else :

					echo '<tr>';
					echo '<td colspan="4" class="no-data">';
					echo 'No editable sliders found';
					echo '</td>';
					echo '</tr>';

			endif;

		?>
		</tbody>
	</table>

</div>

<script type="text/javascript">
<!--//

	$(function(){

		var CMS_Sliders = new NAILS_Admin_CMS_Sliders;
		CMS_Sliders.init_search();

	});

//-->
</script>