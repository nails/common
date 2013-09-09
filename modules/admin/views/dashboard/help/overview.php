<div class="group-dashboard help">

	<p><?=lang( 'dashboard_help_intro' )?></p>

	<hr />

	<table>
		<thead>
			<tr>
				<th class="id"><?=lang( 'dashboard_help_th_id' )?></th>
				<th class="name-desc"><?=lang( 'dashboard_help_th_name' )?></th>
				<th class="actions"><?=lang( 'dashboard_help_th_actions' )?></th>
			</tr>
		</thead>
		<tbody>
			<?php

				if ( $videos ) :

					foreach ( $videos AS $v ) :

					echo '<tr>';
					echo '<td class="id">' . $v->id . '</td>';
					echo '<td class="name-desc">';
					echo $v->title;
					echo '<small>' . $v->description . '</small>';
					echo '</td>';
					echo '<td class="actions">';
					echo '<a href="http://player.vimeo.com/video/' . $v->vimeo_id . '?autoplay=true" class="awesome small video-button">' . lang( 'action_view' ) . '</a>';
					echo '</td>';
					echo '</tr>';

					endforeach;

				else :

					echo '<tr>';
					echo '<td id="no_records" colspan="3"><p>' . lang( 'no_records_found' ) . '</p></td>';
					echo '</tr>';

				endif;

			?>
		</tbody>
	</table>

	<script style="text/javascript">
	<!--//

		$(function(){

			$( 'a.video-button' ).fancybox({ type : 'iframe' });

		});

	//-->
	</script>
</div>