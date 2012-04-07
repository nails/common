<!-- jQuery -->
<script type="text/javascript" charset="utf-8">
$(function () 
{

	$('.new_btn').click(function(){
	
		$('.new_frm').show();
		return false;
	
	});
	
	$( 'input[name=position]' ).keyup( function() {

		var search = $(this).val();
		var counter = 0;
		
		$( '#society_position-list' ).find( '.searchable' ).each( function() {
		
			var position = $(this).html();
			var pattern = new RegExp(search, "gi");
			
			if ( position.match( pattern ) ) {
				
				$(this).parent().parent().show();
				
			} else {
				
				$(this).parent().parent().hide();
				
			}
			
			if ( search == '' ) {
				$( this ).parent().parent().show();
			}
		
		});
		
		//	How many results
		$( '#society_position-list .searchable' ).each( function() {
		
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
		var ok = confirm("Are you sure you wish to delete this position? This cannot be undone.");
		if(ok==true){
			return true;
		}else{
			return false;
		}
	});
	
});
</script>


<h1>Manage Society Positions</h1>

<p>
	Positions are used and specified in user societies.  You cannot delete any
	position that is currently in use by a member within their profile. You should also take care when updating
	positions, any updates will be reflected in member profiles.
</p>

<hr>

<div class="left" style="margin-left:10px;margin-right:10px;width:400px;">

	<div class="box">
	
		<h2>Add New Position</h2>
		
			<div style="padding:0 12px;">
			
			<p>
				Add a new position below. Ensure the position you plan to add is not already listed. Tip: begin typing to filter
				the list of positions shown.
			</p>
				
			<?=form_open_multipart( 'admin/lists/society_position' )?>
			<?=form_hidden( 'save', TRUE )?>
			
			<?php
			
				if ( form_error( 'position' ) ) :
				
					echo '<div class="error" style="text-align:center">';
					echo form_error( 'position' );
					echo $error;
					echo '</div>';
				
				endif;
			
			?>
			
				<table class="blank" style="width:auto;">
				
					<tr>
					
						<td align="right"><strong>Position</strong>*:</td>
						<td>
							<input type="text" name="position" value="">
						</td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong>Description</strong>:</td>
						<td>
							<textarea name="description"></textarea>
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
		$top_society_position 	= array();
		
		//	For each society_position result, pull the stats
		foreach ( $society_positions->result() AS $i ) :
			$top_society_position[$i->position] = $i->u_count;
		endforeach;
		
		//	Sort by value, high to low
		arsort($top_society_position);
		$top_society_position = array_slice($top_society_position, 0, 5, TRUE);
				
	?>
	
	<div class="box" style="margin-top:20px;padding:">
	
		<h2>Stats: Most Common Positions</h2>

		<?php
						
			echo '<ul>';
			foreach ( $top_society_position AS $tsi => $val ) :
				echo '<li>' . $tsi . ' <span class="indicator">' . $val . '</span></li>';
			endforeach;
			echo '</ul>';
			
		?>
					
	</div>
		
</div>

<div class="left box" style="margin-left:10px;margin-right:10px;width:auto;">
	
	<h2>Listed Positions</h2>
	
	<table id="society_position-list" style="padding:0 10px 10px;">
			
		<tbody>
		
			<?php $temp = array(); ?>
			<?php foreach ( $society_positions->result() AS $i ) : ?>
			<?php $temp[$i->id] = $i->position; ?>
			<?php endforeach; ?>
		
			<?php foreach ( $society_positions->result() AS $i ) : ?>
		
			<tr>
			
				<td class="search_me">
					<span class="searchable"><?=$i->position?></span>
					<br>
					<small class=""><?=$i->description?></small>
					<br><small style="color:#999;">Dependencies: <?=$i->u_count?> Members</small>
				</td>
				<td width="88">
					
					<?=anchor( 'admin/lists/society_position_edit/' . $i->id, 'Edit', 'class="a-button a-button-small"' )?>
					<?php if ( $i->u_count == 0 ) : ?>
						<?=anchor( 'admin/lists/society_position_delete/' . $i->id, 'Delete', 'class="a-button a-button-small confirm-delete"' )?>
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

