<!-- jQuery -->
<script type="text/javascript" charset="utf-8">
$(function () 
{

	$('.new_btn').click(function(){
	
		$('.new_frm').show();
		return false;
	
	});
	
	$( 'input[name=title]' ).keyup( function() {

		var search = $(this).val();
		var counter = 0;
		
		$( '#location-list' ).find( '.searchable' ).each( function() {
		
			var location = $(this).html();
			var pattern = new RegExp(search, "gi");
			
			if ( location.match( pattern ) ) {
				
				$(this).parent().parent().show();
				
			} else {
				
				$(this).parent().parent().hide();
				
			}
			
			if ( search == '' ) {
				$( this ).parent().parent().show();
			}
		
		});
		
		//	How many results
		$( '#location-list .searchable' ).each( function() {
		
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
		var ok = confirm("Are you sure you wish to delete this location? This cannot be undone.");
		if(ok==true){
			return true;
		}else{
			return false;
		}
	});
	
});
</script>


<h1>Manage Locations</h1>

<p>
	Locations are available to users via their profile (i.e. desired locations) and to internships 
	(i.e. internships must have a location specified). You cannot delete locations that are currently in use by one of the
	dependencies described. You must also take care when updating locations; any updates will be reflected in both saved and listed
	information displayed on the website.
</p>

<hr>

<div class="left" style="margin-left:10px;margin-right:10px;width:400px;">

	<div class="box">
	
		<h2>Add New Location</h2>
		
			<div style="padding:0 12px;">
			
			<p>
				Add a new location below. Ensure the location you plan to add is not already listed. Tip: begin typing to filter
				the list of locations shown.
			</p>
			<p>
				Please bear in mind the hierarchical structure of the locations table. Every location must have a parent.
				Be sure to place all new locations in the right place. Here is an example of where West London would live
				i.e. West London's parent would be London.
			</p>
			<p style="font-weight:bold;text-align:center;">
				United Kingdom &rsaquo; England &rsaquo; South East &rsaquo; London &rsaquo; West London
			</p>
				
			<?=form_open_multipart( 'admin/lists/location' )?>
			<?=form_hidden( 'save', TRUE )?>
			
			<?php
			
				if ( form_error( 'title' ) || form_error( 'parent' ) ) :
				
					echo '<div class="error" style="text-align:center">';
					echo form_error( 'title' );
					echo form_error( 'parent' );
					echo $error;
					echo '</div>';
				
				endif;
			
			?>
			
				<table class="blank" style="width:auto;">
				
					<tr>
					
						<td align="right"><strong>Title</strong>*:</td>
						<td>
							<input type="text" name="title" value="">
						</td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong>Parent</strong>*:</td>
						<td>
							<select name="parent">
								<option value="">Please Select...</option>
								<?php
									foreach ( $locations->result() AS $ll ) :
										echo '<option value="'.$ll->id.'">'.$ll->title.'</option>';
									endforeach;
								?>
							</select>
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
		$top_location_interns 	= array();
		$top_location_internships	= array();
		
		//	For each location result, pull the stats
		foreach ( $locations->result() AS $s ) :
			$top_location_interns[$s->title] 		= $s->u_count;
			$top_location_internships[$s->title] 	= $s->i_count;
		endforeach;
		
		//	Sort by value, high to low
		arsort($top_location_interns);
		arsort($top_location_internships);
		$top_location_interns = array_slice($top_location_interns, 0, 5, TRUE);
		$top_location_internships = array_slice($top_location_internships, 0, 5, TRUE);
				
	?>
	
	<div class="box" style="margin-top:20px;padding:">
	
		<h2>Stats: Intern Preferences, Most Popular</h2>

		<?php
						
			echo '<ul>';
			foreach ( $top_location_interns AS $tsi => $val ) :
				echo '<li>' . $tsi . ' <span class="indicator">' . $val . '</span></li>';
			endforeach;
			echo '</ul>';
			
		?>
					
	</div>
	
	<div class="box" style="margin-top:20px;padding:">
	
		<h2>Stats: Internships, Most Common</h2>

		<?php
						
			echo '<ul>';
			foreach ( $top_location_internships AS $tsi => $val ) :
				echo '<li>' . $tsi . ' <span class="indicator">' . $val . '</span></li>';
			endforeach;
			echo '</ul>';
			
		?>
					
	</div>
	
</div>

<div class="left box" style="margin-left:10px;margin-right:10px;width:auto;">
	
	<h2>Listed Locations</h2>
	
	<table id="location-list" style="padding:0 10px 10px;">
			
		<tbody>
		
			<?php $temp = array(); ?>
			<?php foreach ( $locations->result() AS $s ) : ?>
			<?php $temp[$s->id] = $s->title; ?>
			<?php endforeach; ?>
		
			<?php foreach ( $locations->result() AS $s ) : ?>
		
			<tr>
			
				<td class="search_me">
					<span class="searchable"><?=$s->title?></span> <?php if ( $s->parent != '0' ) : echo ' (within ' . $temp[$s->parent] . ' [' . $s->parent . '])'; else: echo '<small>[TOP LEVEL LOCATION]</small>'; endif; ?>
					<br><a href="<?=site_url( 'admin/accounts/index?filter[location]=' . $s->id )?>" class="inline_indicator"><?=$s->u_count?> Interns</a> <span class="inline_indicator"><?=$s->i_count?> Internships</span>
				</td>
				<td width="88">
					
					<?=anchor( 'admin/lists/location_edit/'.$s->id, 'Edit', 'class="a-button a-button-small"')?>
					<?php if ( $s->u_count == 0 && $s->i_count == 0 ) : ?>
						<?=anchor( 'admin/lists/location_delete/'.$s->id, 'Delete', 'class="a-button a-button-small confirm-delete"')?>
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

