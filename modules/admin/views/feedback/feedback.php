			<h1>Feedback</h1>
			
			<p class="notice"><strong>What is Feedback?</strong> This information is collected via the Feedback tab on the right hand side of the site for logged in members. These entries are one-way, and are not connected to any e-mail system or the 'contact form', and there is generally no need or mechanism for replying. Support requests or questions are generally submitted via the 'contact form', soon to be the 'ticketing system'.</p>

			
			<table>
			
				<thead>
				
					<tr>
						
						<th>ID</th>
						<th>Member</th>
						<th>Feedback</th>
						<th width="90">Date</th>
						<th width="100">Options</th>
					
					</tr>
				
				</thead>
				
				<tbody>
				
					<?php foreach ( $feedback AS $f ) : ?>
				
					<tr>
					
						<td><?=number_format( $f->id )?></td>
						<td>
							<?=anchor( 'admin/accounts/edit/' . $f->user_id, title_case( $f->first_name . ' ' . $f->last_name ) )?>
							<br>
							<small><?=$f->email?></small>
						</td>
						<td><?=$f->feedback?></td>
						<td><?=reformat_date($f->date)?></td>
						<td>
							
							<?=anchor( 'admin/feedback/delete/' . $f->id, 'Delete', 'class="a-button a-button-small"')?>
																
						</td>
					
					</tr>
					
					<?php endforeach; ?>
				
				</tbody>
			
			</table>