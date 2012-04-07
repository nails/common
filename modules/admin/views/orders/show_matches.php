<!--	PAGE TITLE	-->
<section>
	<h1>Orders &rsaquo; Show Matches (<?=$order->ref?>, <?=count( $users )?> interns matched)</h1>	
</section>

<p>
	The list below shows all users which have been matched to this order.
</p>

<!--	IMPORT TABLE	-->
<?php $this->load->view( 'orders/matches_table' ); ?>