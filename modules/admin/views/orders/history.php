
<!-- jQuery -->
<script type="text/javascript" charset="utf-8">
$(function () 
{

	
});
</script>


<h1>Orders: History</h1>

<hr>

<div class="" style="margin-left:10px;margin-right:10px;">

	<div class="box">
	
		<h2>Order History (<?=$order->ref?>)</h2>
		
		<div style="padding:0 12px;">
					
			<table>
			
				<?php foreach ( $history->result() AS $h ): ?>
				
					<tr>
					
<!-- 						<td><?=$h->type?></td> -->
						<td width="120"><?=reformat_date( $h->date, 'jS F Y H:i' )?></td>
						<td><?=$h->note?></td>
					
					</tr>
				
				<?php endforeach; ?>	
								
			</table>									

		</div>
	
	</div>

</div>

