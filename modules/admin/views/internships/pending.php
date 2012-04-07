<h1>Internships Pending Approval</h1>

<section class="filter-box">
	<p style="margin:0;padding:0;">
		<form method="get" action="/admin/internships/<?=$this->uri->segment(3)?>/" class="form" style="margin:0;padding:0;">
		<label>Search:</label>
		<input type="text" name="search" value="<?php if ( $this->input->get('search') ) : echo $this->input->get('search'); endif;?>">
		<input type="image" src="/assets/app/img/icons/search.png" style="vertical-align:middle">
		</form>
	</p>
</section>

<hr>

<!--	IMPORT TABLE	-->
<?php $this->load->view( 'internships/internships_table' );	?>