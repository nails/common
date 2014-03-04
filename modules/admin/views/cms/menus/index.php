<div class="group-cms menus overview">

	<p>
		Listed below are all the editable menus on site.
		<?php

			if ( user_has_permission( 'admin.cms.can_create_menu' ) ) :

				echo anchor( 'admin/cms/menus/create', 'Add New Menu', 'class="awesome small green right"' );

			endif;

		?>
	</p>

	<hr />

	<div class="search">
		<div class="search-text">
			<input type="text" name="search" value="" autocomplete="off" placeholder="Search menu titles by typing in here...">
		</div>
	</div>

	<hr />

	<table>
		<thead>
			<tr>
				<th class="title">Menu</th>
				<th class="user">Modified By</th>
				<th class="datetime">Modified</th>
				<th class="actions">Actions</th>
			</tr>
		</thead>
		<tbody>
		<?php

			if ( $menus ) :

				foreach ( $menus AS $menu ) :

					echo '<tr class="menu" data-label="' . $menu->label . '">';
						echo '<td class="label">';
							echo $menu->label;
							echo $menu->description ? '<small>' . $menu->description . '</small>' : '';
						echo '</td>';

						$this->load->view( 'admin/_utilities/table-cell-user',		$menu->modified_by );
						$this->load->view( 'admin/_utilities/table-cell-datetime',	array( 'datetime' => $menu->modified ) );

						echo '<td class="actions">';

							if ( user_has_permission( 'admin.cms.can_edit_menu' ) ) :

								echo anchor( 'admin/cms/menus/edit/' . $menu->id, lang( 'action_edit' ), 'class="awesome small"' );

							endif;

							if ( user_has_permission( 'admin.cms.can_delete_menu' ) ) :

								echo anchor( 'admin/cms/menus/delete/' . $menu->id, lang( 'action_delete' ), 'data-title="Are you sure?" data-body="This will remove the menu from the site. This action cannot be undone." class="confirm awesome small red"' );

							endif;

						echo '</td>';
					echo '</tr>';

				endforeach;

			else :

					echo '<tr>';
					echo '<td colspan="4" class="no-data">';
					echo 'No editable menus found';
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

		var CMS_Menus = new NAILS_Admin_CMS_Menus;
		CMS_Menus.init_search();

	});

//-->
</script>