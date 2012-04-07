<!--	PAGE TITLE	-->
<section>
	<h1><?=$page->title?></h1>	
</section>

<!--	IMPORT TABLE	-->
<?php $this->load->view( 'orders/orders_table' ); ?>