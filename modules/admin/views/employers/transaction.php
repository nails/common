<h1>Transactions</h1>
<p>
	This page shows the last 100 transactions to occur on the site. Search to find a specific transaction.
</p>

<hr />

<?php if ( $this->uri->segment( 3 ) != 'smart_lists' ) : ?>
<section class="filter-box">
	<p style="margin:0;padding:0;">
		<form method="get" action="<?=site_url( 'admin/employers/transaction' )?>" class="form" style="margin:0;padding:0;">
		<label>Search:</label>
		<input type="text" name="search" value="<?php if ( $this->input->get('search') ) : echo $this->input->get('search'); endif;?>">
		<input type="image" src="/assets/app/img/icons/search.png" style="vertical-align:middle">
		</form>
	</p>
</section>
<?php endif; ?>

<hr />

<table>
	<thead>
		<tr>
			<th>Ref</th>
			<th>Employer</th>
			<th>Date</th>
			<th>Type</th>
			<th>Value</th>
			<th>Note</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ( $transactions AS $transaction ) : ?>
		<tr>
			<td><?=$transaction->ref?></td>
			<td>
				<?=$transaction->employer_name?>
				<small style="display:block;color:#666">
				<?php
					if ( $transaction->user_id ) :
					
						echo anchor( 'admin/accounts/edit/' . $transaction->user_id , $transaction->first_name . ' ' . $transaction->last_name, 'style="color:#666"' );
						
					else :
						
						echo '&mdash;';
						
					endif;
				
				?>
				</small>
			</td>
			<td style="width:100px;">
				<?=nice_time( strtotime( $transaction->created) )?>
				<small style="display:block;color:#666"><?=reformat_date( $transaction->created )?></small>
			</td>
			<td><?php
			
				switch( $transaction->type ) :
				
					case '':
					
						echo '';
					
					break;
					
					default:
					
						echo ucwords( str_replace( '_', ' ', $transaction->type ) );
					
					break;
				
				endswitch;
				
			?></td>
			<td>&pound;<?=number_format( $transaction->value, 2 )?></td>
			<td><?=$transaction->note?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>