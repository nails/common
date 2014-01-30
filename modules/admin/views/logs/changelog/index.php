<div class="group-stats browse">

	<p>
		Whenever an administrator makes a change to any data the change log is updated.
	</p>

	<?php

		$this->load->view( 'admin/logs/changelog/utilities/search' );
		$this->load->view( 'admin/_utilities/pagination' );

	?>

	<table>
		<thead>
			<tr>
				<th class="user">user</th>
				<th class="changes">Changes</th>
				<th class="datetime">Date</th>
			</tr>
		</thead>
		<tbody>
		<?php

			if ( $items ) :

				foreach ( $items AS $item ) :

					echo '<tr>';

					$this->load->view( 'admin/_utilities/table-cell-user',	$item->user );

					echo '<td class="changes">';

					$_sentance		= array();
					if ( ! empty( $item->user->id ) ) :

						$_sentance[] = $item->user->first_name;

					else :

						$_sentance[] = 'Someone';

					endif;
					$_sentance[]	= $item->verb;
					$_sentance[]	= $item->article;
					$_sentance[]	= $item->title ? $item->item . ',' : $item->item;

					if ( $item->title ) :

						if ( $item->url ) :

							$_sentance[] = '<strong>' . anchor( $item->url, $item->title ) . '</strong>';

						else :

							$_sentance[] = $item->title;

						endif;

					endif;

					echo implode( ' ', $_sentance );

					echo '<hr style="margin:0.5em 0;" />';
					echo '<small>';
					echo '<ul>';
					foreach ( $item->changes AS $change ) :

						echo '<li>';
						echo '<strong>' . $change->field . '</strong>: ';
						echo '<em>' . $change->old_value . '</em>';
						echo '&nbsp;&rarr;&nbsp;';
						echo '<em>' . $change->new_value . '</em>';
						echo '</li>';

					endforeach;
					echo '</ul>';
					echo '<small>';

					echo '</td>';

					$this->load->view( 'admin/_utilities/table-cell-datetime',	array( 'datetime' => $item->created ) );

					echo '</tr>';

				endforeach;

			else :

				echo '<tr>';
				echo '<td colspan="5" class="no-data">';
				echo 'No changelog items found';
				echo '</td>';
				echo '</tr>';

			endif;

		?>
		</tbody>
	</table>

	<?php

		$this->load->view( 'admin/_utilities/pagination' );

	?>
</div>