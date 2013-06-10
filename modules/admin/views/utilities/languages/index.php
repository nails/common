<div class="group-utilities languages">

	<?php

		echo '<p>' . lang( 'utilities_languages_intro' ) . '</p>';

		if ( APP_MULTI_LANG ) :

			echo '<p>' . lang( 'utilities_languages_multilang_on' ) . '</p>';

		else :

			echo '<p>' . lang( 'utilities_languages_multilang_off' ) . '</p>';

		endif;

	?>
	
	<hr />
	
	<table>

		<thead>
			<tr>
				<th><?=lang( 'utilities_languages_th_lang' )?></th>
				<th><?=lang( 'utilities_languages_th_supported' )?></th>
				<th><?=lang( 'utilities_languages_th_actions' )?></th>
			</tr>
		</thead>
		
		<tbody>
		
		<?php foreach ( $languages AS $lang ) : ?>
		
			<tr>
				<td>
					<?=$lang->name?>
				</td>
				<td>
					<?=$lang->supported ? lang( 'yes' ) : lang( 'no' )?>
				</td>
				<td>
					<?php

						if ( $lang->supported ) :

							echo anchor( 'admin/utilities/mark_lang_unsupported/' . $lang->id, lang( 'utilities_languages_action_mark_unsupported' ), 'class="awesome small"' );

						else :

							echo anchor( 'admin/utilities/mark_lang_supported/' . $lang->id, lang( 'utilities_languages_action_mark_supported' ), 'class="awesome small"' );

						endif;

					?>
				</td>
			</tr>
		
		<?php endforeach; ?>
		
		</tbody>
	
	</table>
	
</div>