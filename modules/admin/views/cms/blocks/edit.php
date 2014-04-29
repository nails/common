<div class="group-cms blocks edit">

	<div class="details">
		<p>
			<strong>Title:</strong> <?=$block->title?>
		</p>
		<p>
			<strong>Slug:</strong> <?=$block->slug?>
		</p>
		<p>
			<strong>Description:</strong> <?=$block->description?>
		</p>
		<p>
			<strong>Located:</strong> <?=$block->located?>
		</p>
		<p>
			<strong>Located:</strong> <?=$block_types[$block->type]?>
		</p>
	</div>

	<?=form_open()?>
	<fieldset>
	<?php if ( APP_MULTI_LANG && count( $languages ) > 1 ) : ?>
		<legend>Translations</legend>
		<p class="system-alert message no-close">
			<strong>Note:</strong> Every block is required to have an <?=APP_DEFAULT_LANG_NAME?> version, however more translations can be
			added if needed. Translations will only be used when viewing the site in a particular language (if supported).
			If no translation is available the system will fall back to <?=APP_DEFAULT_LANG_NAME?>.
		</p>


		<!--	DEFAULT LANG	-->
		<fieldset class="translation" data-lang_id="<?=$default_id?>">
			<legend><?=APP_DEFAULT_LANG_NAME?></legend>

	<?php else : ?>
		<legend>Value</legend>
		<div class="translation" data-lang_id="<?=$default_id?>">

	<?php endif; ?>

			<?=form_hidden( 'translation[0][lang_id]', $default_id )?>
			<div class="system-alert error no-close">
				<strong>Oops!</strong> Please ensure a value is set.
			</div>
			<?php

				//	Render the correct display
				switch ( $block->type ) :

					case 'plaintext' :

						echo '<textarea name="translation[0][value]">' . $block->default_value . '</textarea>';

					break;

					// --------------------------------------------------------------------------

					case 'richtext' :

						echo form_textarea( 'translation[0][value]',  $block->default_value, 'class="wysiwyg"' );

						echo '<p class="system-alert notice no-close">';
						echo '<strong>Note:</strong> The editor\'s display might not be a true representation of the final layout';
						echo 'due to application stylesheets on the front end which are not loaded here.';
						echo '</p>';

					break;

				endswitch;

				// --------------------------------------------------------------------------

				//	Revisions
				if ( $block->default_value_revisions ) :

					?>
					<ul class="revisions">
						<li class="summary">
							<?=count( $block->default_value_revisions )?> Revisions
							<a href="#" class="toggle-revisions right">Show/Hide</a>
						</li>
						<?php foreach ( $block->default_value_revisions AS $revision ) : ?>
							<li class="revision">
								<span class="revision-content" rel="tipsy-left" title="<?=$revision->created?> by <?=$revision->user->id ? $revision->user->first_name . ' ' . $revision->user->last_name : 'Unknown'?>">
									<?=$revision->value ? $revision->value : '<span class="no-data">No Value</span>'?>
								</span>
							</li>
						<?php endforeach; ?>
					</ul>
					<?php

				endif;

			?>
		<?php if ( APP_MULTI_LANG && count( $languages ) > 1 ) : ?>
		</fieldset>
		<?php else : ?>
		</div>
		<?php endif; ?>

		<!--	OTHER LANGUAGES	-->
		<?php

			$_counter = 1;
			foreach ( $block->translations AS $translation ) :

				if ( $translation->lang->slug == APP_DEFAULT_LANG_SLUG )
					continue;

				?>
				<fieldset class="translation" data-lang_id="<?=$translation->lang->id?>">
					<legend>
						<?=$translation->lang->name?>
						<a href="#" class="remove-translation">Remove Translation</a>
					</legend>
					<?=form_hidden( 'translation[' . $_counter . '][lang_id]', $translation->lang->id )?>
					<div class="system-alert error no-close">
						<strong>Oops!</strong> Please ensure a value is set.
					</div>
					<?php

						//	Render the correct display
						switch ( $block->type ) :

							case 'plaintext' :

								echo '<textarea name="translation[' . $_counter . '][value]">' . $translation->value . '</textarea>';

							break;

							// --------------------------------------------------------------------------

							case 'richtext' :

								echo form_textarea( 'translation[' . $_counter . '][value]',  $translation->value, 'class="wysiwyg"' );

								if ( $block->type == 'richtext' ) :

									echo '<p class="system-alert notice no-close">';
									echo '<strong>Note:</strong> The editor\'s display might not be a true representation of the final layout';
									echo 'due to application stylesheets on the front end which are not loaded here.';
									echo '</p>';

								endif;

							break;

						endswitch;

					?>
					<?php if ( $translation->revisions ) : ?>
					<ul class="revisions">
						<li class="summary">
							<?=count( $translation->revisions )?> Revisions
							<a href="#" class="toggle-revisions right">Show/Hide</a>
						</li>
						<?php foreach ( $translation->revisions AS $revision ) : ?>
							<li class="revision">
								<span class="revision-content" rel="tipsy-left" title="<?=$revision->created?> by <?=$revision->user->id ? $revision->user->first_name . ' ' . $revision->user->last_name : 'Unknown'?>">
									<?=$revision->value?>
								</span>
							</li>
						<?php endforeach; ?>
					</ul>
					<?php endif; ?>
				</fieldset>
				<?php

			$_counter++;
			endforeach;

		?>


		<?php if ( APP_MULTI_LANG && count( $languages ) > 1 ) : ?>
		<!--	ACTIONS	-->
		<p class="add-translation">
			<a href="#" class="awesome small right" id="new-translation">Add Translation</a>
		</p>
		<?php endif; ?>

	</fieldset>

	<p>
		<?=form_submit( 'submit', lang( 'action_save_changes' ), 'class="awesome"' )?>
	</p>

	<?=form_close()?>

</div>

<script type="text/template" id="template-translation">
	<legend>
		<?=form_dropdown( 'new_translation[{{new_count}}][lang_id]', $languages )?>
		<a href="#" class="remove-translation">Remove Translation</a>
	</legend>
	<div class="system-alert error no-close">
		<strong>Oops!</strong> Please ensure a language and value is set.
	</div>
	<textarea name="new_translation[{{new_count}}][value]" id="translation_{{new_count}}"></textarea>
	<?php if ( $block->type == 'richtext' ) : ?>
	<p class="system-alert notice no-close">
		<strong>Note:</strong> The editor's display might not be a true representation of the final layout
		due to application stylesheets on the front end which are not loaded here.
	</p>
	<?php endif; ?>
</script>

<script type="text/javascript">
<!--//

	$(function(){

		var CMS_Blocks = new NAILS_Admin_CMS_Blocks;
		CMS_Blocks.init_edit( '<?=$block->type?>' );

	});

//-->
</script>