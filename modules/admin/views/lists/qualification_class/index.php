<!-- jQuery -->
<script type="text/javascript" charset="utf-8">
$(function () 
{

	$('.new_btn').click(function(){
	
		$('.new_frm').show();
		return false;
	
	});
	
	$( 'input[name=class]' ).keyup( function() {

		var search = $(this).val();
		var counter = 0;
		
		$( '#qualification_class-list' ).find( '.searchable' ).each( function() {
		
			var class = $(this).html();
			var pattern = new RegExp(search, "gi");
			
			if ( class.match( pattern ) ) {
				
				$(this).parent().parent().show();
				
			} else {
				
				$(this).parent().parent().hide();
				
			}
			
			if ( search == '' ) {
				$( this ).parent().parent().show();
			}
		
		});
		
		//	How many results
		$( '#qualification_class-list .searchable' ).each( function() {
		
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
		var ok = confirm("Are you sure you wish to delete this qualification class? This cannot be undone.");
		if(ok==true){
			return true;
		}else{
			return false;
		}
	});
	
});
</script>


<h1>Manage Qualification Classifications</h1>

<p>
	Qualification classifications are used and specified in user academic qualifications.  You cannot delete any
	classification that is currently in use by a member within their profile. You should also take care when updating
	classifications, any updates will be reflected in member profiles.
</p>

<hr>

<div class="left" style="margin-left:10px;margin-right:10px;width:400px;">

	<div class="box">
	
		<h2>Add New Qualification Classification</h2>
		
			<div style="padding:0 12px;">
			
			<p>
				Add a new classification below. Ensure the classification you plan to add is not already listed. Tip: begin typing to filter
				the list of classifications shown.
			</p>
				
			<?=form_open_multipart( 'admin/lists/qualification_class' )?>
			<?=form_hidden( 'save', TRUE )?>
			
			<?php
			
				if ( form_error( 'class' ) ) :
				
					echo '<div class="error" style="text-align:center">';
					echo form_error( 'class' );
					echo $error;
					echo '</div>';
				
				endif;
			
			?>
			
				<table class="blank" style="width:auto;">
				
					<tr>
					
						<td align="right"><strong>Name</strong>*:</td>
						<td>
							<input type="text" name="class" value="">
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
		$top_qualification_class 	= array();
		
		//	For each qualification_class result, pull the stats
		foreach ( $qualification_classes->result() AS $i ) :
			$top_qualification_class[$i->class] = $i->u_count;
		endforeach;
		
		//	Sort by value, high to low
		arsort($top_qualification_class);
		$top_qualification_class = array_slice($top_qualification_class, 0, 5, TRUE);
				
	?>
	
	<div class="box" style="margin-top:20px;padding:">
	
		<h2>Stats: Most Common Classifications</h2>

		<?php
						
			echo '<ul>';
			foreach ( $top_qualification_class AS $tsi => $val ) :
				echo '<li>' . $tsi . ' <span class="indicator">' . $val . '</span></li>';
			endforeach;
			echo '</ul>';
			
		?>
					
	</div>
		
</div>

<div class="left box" style="margin-left:10px;margin-right:10px;width:auto;">
	
	<h2>Listed Classifications</h2>
	
	<table id="qualification_class-list" style="padding:0 10px 10px;">
			
		<tbody>
		
			<?php $temp = array(); ?>
			<?php foreach ( $qualification_classes->result() AS $i ) : ?>
			<?php $temp[$i->id] = $i->class; ?>
			<?php endforeach; ?>
		
			<?php foreach ( $qualification_classes->result() AS $i ) : ?>
		
			<tr>
			
				<td class="search_me">
					<span class="searchable"><?=$i->class?></span>
					<br><a href="<?=site_url( 'admin/accounts/index?filter[qualification_classification]=' . $i->id )?>" class="inline_indicator"><?=$i->u_count?> Interns</a>
				</td>
				<td width="88">
					
					<?=anchor( 'admin/lists/qualification_class_edit/' . $i->id, 'Edit', 'class="a-button a-button-small"' )?>
					<?php if ( $i->u_count == 0 ) : ?>
						<?=anchor( 'admin/lists/qualification_class_delete/' . $i->id, 'Delete', 'class="a-button a-button-small confirm-delete"' )?>
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

