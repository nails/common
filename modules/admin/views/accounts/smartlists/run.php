<!--	PAGE TITLE	-->
<section>
	<h1>
		Smart Lists &rsaquo; Run Smart List &rsaquo; <?=$smartlist->title?>
		<span class="right">
			<a href="<?=site_url().uri_string()?>?download=true" class="a-button">Download Raw Data</a>
		</span>
	</h1>	
	
</section>

<!--	IMPORT TABLE	-->
<?php $this->load->view( 'accounts/account_table' ); ?>