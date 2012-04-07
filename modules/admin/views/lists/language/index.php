<!-- jQuery -->
<script type="text/javascript" charset="utf-8">
$(function () 
{

	$('.new_btn').click(function(){
	
		$('.new_frm').show();
		return false;
	
	});
	
	$( 'input[name=name]' ).keyup( function() {

		var search = $(this).val();
		var counter = 0;
		
		$( '#language-list' ).find( '.searchable' ).each( function() {
		
			var name = $(this).html();
			var pattern = new RegExp(search, "gi");
			
			if ( name.match( pattern ) ) {
				
				$(this).parent().parent().show();
				
			} else {
				
				$(this).parent().parent().hide();
				
			}
			
			if ( search == '' ) {
				$( this ).parent().parent().show();
			}
		
		});
		
		//	How many results
		$( '#language-list .searchable' ).each( function() {
		
			if ( $( this ).parent().parent().css( 'display' ) != 'none' )
				counter++
		
		});
		var verb = (counter == 1) ? ' conflict' : ' potential conflicts';
		$( '#search_warning' ).html( '<p>' + counter + verb +' &rarr;</p>' );
		$( '#search_warning' ).show();
		//$( '#add_button' ).hide();
		
		if ( search == '' ||  counter == 0) {
			$( '#search_warning' ).hide();
			//$( '#add_button' ).show();
		}
	
	});
	
	$('.confirm-delete').click(function(){
		var ok = confirm("Are you sure you wish to delete this language? This cannot be undone.");
		if(ok==true){
			return true;
		}else{
			return false;
		}
	});
	
});
</script>


<h1>Manage Languages</h1>

<p>
	Languages are used and specified in user languages.  You cannot delete any
	language that is currently in use by a member within their profile. You should also take care when updating
	languages, any updates will be reflected in member profiles.
</p>

<hr>

<div class="left" style="margin-left:10px;margin-right:10px;width:400px;">

	<div class="box">
	
		<h2>Add New Language</h2>
		
			<div style="padding:0 12px;">
			
			<p>
				Add a new language below. Ensure the language you plan to add is not already listed. Tip: begin typing to filter
				the list of languages shown.
			</p>
				
			<?=form_open_multipart( 'admin/lists/language' )?>
			<?=form_hidden( 'save', TRUE )?>
			
			<?php
			
				if ( form_error( 'name' ) ) :
				
					echo '<div class="error" style="text-align:center">';
					echo form_error( 'name' );
					echo $error;
					echo '</div>';
				
				endif;
			
			?>
			
				<table class="blank" style="width:auto;">
				
					<tr>
					
						<td align="right"><strong>Name</strong>*:</td>
						<td>
							<input type="text" name="name" value="">
						</td>
					
					</tr>
										
					<tr>
					
						<td></td>
						<td><div id="search_warning" style="color:#ff0000;font-weight:bold"></div></td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong></strong></td>
						<td><span id="add_button"><input type="submit" value="Add"></span></td>
					
					</tr>
				
				</table>
			
			</form>
	
		</div>
	
	</div>
	
	<?php
		
		//	Get top most popular of each stat type
		
		//	Set up arrays
		$top_language 	= array();
		
		//	For each language result, pull the stats
		foreach ( $languages->result() AS $i ) :
			$top_language[$i->name] = $i->u_count;
		endforeach;
		
		//	Sort by value, high to low
		arsort($top_language);
		$top_language = array_slice($top_language, 0, 5, TRUE);
				
	?>
	
	<div class="box" style="margin-top:20px;padding:">
	
		<h2>Stats: Most Popular Languages</h2>

		<?php
						
			echo '<ul>';
			foreach ( $top_language AS $tsi => $val ) :
				echo '<li>' . $tsi . ' <span class="indicator">' . $val . '</span></li>';
			endforeach;
			echo '</ul>';
			
		?>
					
	</div>
		
</div>

<div class="left box" style="margin-left:10px;margin-right:10px;width:auto;">
	
	<h2>Listed Languages</h2>
	
	<table id="language-list" style="padding:0 10px 10px;">
			
		<tbody>
		
			<?php $temp = array(); ?>
			<?php foreach ( $languages->result() AS $i ) : ?>
			<?php $temp[$i->id] = $i->name; ?>
			<?php endforeach; ?>
		
			<?php foreach ( $languages->result() AS $i ) : ?>
		
			<tr>
			
				<td class="search_me">
					<span class="searchable"><?=$i->name?></span>
					<br><small style="color:#999;">Dependencies: <?=$i->u_count?> Members</small>
				</td>
				<td width="88">
					
					<?=anchor( 'admin/lists/language_edit/' .$i->id, 'Edit', 'class="a-button a-button-small"')?>
					<?php if ( $i->u_count == 0 ) : ?>
						<?=anchor( 'admin/lists/language_delete/' . $i->id, 'Delete', 'class="a-button a-button-small confirm-delete"' )?>
					<?php endif; ?>						
				</td>
			
			</tr>
			
			<?php endforeach; ?>
		
		</tbody>
	
	</table>

</div>

<div class="clear">

	<!-- Clear -->

</div>

