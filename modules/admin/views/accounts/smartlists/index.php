<style type="text/css">
	
	.list
	{
		border:1px solid #EEE;
		background:#F9F9F9;
		padding:7px;
		text-decoration: none;
		color: inherit;
		border-radius: 5px;
		float:left;
		width:223px;
		margin-right:15px;
	}	
	
	#list_action p
	{
		text-align:center;
	}
	
	#list_action strong
	{
		display:block;
		font-size:1.2em;
		padding:20px 0 20px 0;
		margin:5px 0 10px 0;
		text-transform: uppercase;
		background:#f7f7f7;
		border-top:1px dashed #ececec;
		border-bottom:1px dashed #ececec;
	}
	
	.list .actions
	{
		text-align: center;
	}

</style>
<script type="text/javascript">
<!--// Hide from old browsers
	
	$(function() {
	
		$( '.fancybox' ).fancybox( { 'onStart' : function( obj ) { $( '#list_action' ).find( 'strong' ).html( $(obj).find( 'strong' ).html() ) } } );
	
		$( '.select' ).change(function(){
			
			var sector = $( this ).attr('name');
			var value = $( this ).val();
			var query = 'filter[' + sector + ']=' + value + '&';
			var current = $( '.query' ).text();
			
			
			
			$( '.query' ).append( 'filter[' + $( this ).attr('name') + ']=' + $( this ).val() + '&' );
			$( '.query_link' ).attr('href', $( '.query' ).text() );
		
		});
		
		$( 'a.delete' ).click( function() {
			return confirm( 'Delete "' + $(this).parents( '.list' ).find( '.title' ).html() + '"?\n\nThis will permanently delete this smart list.\n\nTHERE IS NO UNDO.\n\nContinue?' );
		});
			
	});
	
//-->
</script>

<section>
	<h1>Smart Lists</h1>
</section>

<p>
	Smart lists allow you to search for groups of interns based on specific criteria, and allows you to view a list of matches, download their details or e-mail them via the site.
</p>

<hr>

	<p style="text-align:center;margin-bottom:0;padding-bottom:0;">
		<?=anchor( 'admin/accounts/smart_lists/create', 'New Smart List', 'class="a-button a-button-large"' )?>
	</p>

<hr style="margin-top:8px;">

<section>

<?php foreach ( $smartlists AS $s ) : ?>

	<div class="list">
		
		<!--	DETAILS	-->
		<p class="meta">
			<strong class="title"><?=$s->title?></strong>
		</p>
		
		<!--	ACTIONS	-->
		<p class="actions">
			<?=anchor( 'admin/accounts/smart_lists/run/' . $s->id, 'Run Search & View Results', 'class="a-button a-button-blue"' )?>
			<br>
			<?=anchor( 'admin/accounts/smart_lists/email/' . $s->id, 'Send Mass Email', 'class="a-button a-button-small"' )?>
			<?=anchor( 'admin/accounts/smart_lists/delete/' . $s->id, 'Delete', 'class="a-button a-button-small a-button-red delete"' )?>
		</p>
		
	
	</div>
	
	<?php unset($q); ?>
	
<?php endforeach; ?>

<div class="clear"></div>
	
</section>