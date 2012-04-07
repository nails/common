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
		
		$( '#qualification-list' ).find( '.searchable' ).each( function() {
		
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
		$( '#qualification-list .searchable' ).each( function() {
		
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
		var ok = confirm("Are you sure you wish to delete this qualification? This cannot be undone.");
		if(ok==true){
			return true;
		}else{
			return false;
		}
	});
	
});
</script>


<h1>Manage Qualifications</h1>

<p>
	Qualifications are used and specified in user academic qualifications.  You cannot delete any
	qualification that is currently in use by a member within their profile. You should also take care when updating
	qualifications, any updates will be reflected in member profiles.
</p>

<hr>

<div class="left" style="margin-left:10px;margin-right:10px;width:400px;">

	<div class="box">
	
		<h2>Add New Qualification</h2>
		
			<div style="padding:0 12px;">
			
			<p>
				Add a new qualification below. Ensure the qualification you plan to add is not already listed. Tip: begin typing to filter
				the list of qualifications shown.
			</p>
				
			<?=form_open_multipart( 'admin/lists/qualification' )?>
			<?=form_hidden( 'save', TRUE )?>
			
			<?php
			
				if ( form_error( 'name' ) || form_error( 'description' ) ) :
				
					echo '<div class="error" style="text-align:center">';
					echo form_error( 'name' );
					echo form_error( 'description' );
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
					
						<td align="right"><strong>Description</strong>*:</td>
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
		$top_qualification 	= array();
		
		//	For each qualification result, pull the stats
		foreach ( $qualifications->result() AS $i ) :
			$top_qualification[$i->name] = $i->u_count;
		endforeach;
		
		//	Sort by value, high to low
		arsort($top_qualification);
		$top_qualification = array_slice($top_qualification, 0, 5, TRUE);
				
	?>
	
	<div class="box" style="margin-top:20px;padding:">
	
		<h2>Stats: Most Common Qualifications</h2>

		<?php
						
			echo '<ul>';
			foreach ( $top_qualification AS $tsi => $val ) :
				echo '<li>' . $tsi . ' <span class="indicator">' . $val . '</span></li>';
			endforeach;
			echo '</ul>';
			
		?>
					
	</div>
		
</div>

<div class="left box" style="margin-left:10px;margin-right:10px;width:auto;">
	
	<h2>Listed Qualifications</h2>
	
	<table id="qualification-list" style="padding:0 10px 10px;">
			
		<tbody>
		
			<?php $temp = array(); ?>
			<?php foreach ( $qualifications->result() AS $i ) : ?>
			<?php $temp[$i->id] = $i->name; ?>
			<?php endforeach; ?>
		
			<?php foreach ( $qualifications->result() AS $i ) : ?>
		
			<tr>
			
				<td class="search_me">
					<span class="searchable"><?=$i->name?></span>
					<br>
					<small class="searchable"><?=$i->description?></small>
					<br><a href="<?=site_url( 'admin/accounts/index?filter[qualification]=' . $i->id )?>" class="inline_indicator"><?=$i->u_count?> Interns</a>
				</td>
				<td width="88">
					
					<?=anchor( 'admin/lists/qualification_edit/' . $i->id, 'Edit', 'class="a-button a-button-small"' )?>
					<?php if ( $i->u_count == 0 ) : ?>
						<?=anchor( 'admin/lists/qualification_delete/' . $i->id, 'Delete', 'class="a-button a-button-small confirm-delete"' )?>
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

