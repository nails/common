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
		
		$( '#institution-list' ).find( '.searchable' ).each( function() {
		
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
		$( '#institution-list .searchable' ).each( function() {
		
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
		var ok = confirm("Are you sure you wish to delete this institution? This cannot be undone.");
		if(ok==true){
			return true;
		}else{
			return false;
		}
	});
	
});
</script>


<h1>Manage Institutions</h1>

<p>
	Institutions are used and specified in user qualifications and in user societies.  You cannot delete any
	institution that is currently in use by a member within their profile. You should also take care when updating
	institutions, any updates will be reflected in member profiles.
</p>

<hr>

<div class="left" style="margin-left:10px;margin-right:10px;width:350px;">

	<div class="box">
	
		<h2>Add New Institution</h2>
		
			<div style="padding:0 12px;">
			
			<p>
				Add a new institution below. Ensure the institution you plan to add is not already listed. Tip: begin typing to filter
				the list of institutions shown.
			</p>
				
			<?=form_open_multipart( 'admin/lists/institution' )?>
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
					
						<td align="right"><strong>Parent</strong>*:</td>
						<td>
							<select name="parent">
								<option value="">Please Select...</option>
								<?php
									foreach ( $institutions->result() AS $ll ) :
										echo '<option value="'.$ll->id.'">'.$ll->name.'</option>';
									endforeach;
								?>
							</select>
						</td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong>Logo</strong>:</td>
						<td>
							<input type="file" name="userfile" />
						</td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong>Website</strong>:</td>
						<td>
							<input type="text" name="website" placeholder="i.e. http://www.university.ac.uk">
						</td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong>Active?</strong>:</td>
						<td>
							<input type="checkbox" name="active" checked="checked">
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
		$top_institution_qualifications 	= array();
		$top_institution_societies			= array();
		
		//	For each institution result, pull the stats
		foreach ( $institutions->result() AS $i ) :
			$top_institution_qualifications[$i->name] 		= $i->q_count;
			$top_institution_societies[$i->name]		 	= $i->s_count;
		endforeach;
		
		//	Sort by value, high to low
		arsort($top_institution_qualifications);
		arsort($top_institution_societies);
		$top_institution_qualifications = array_slice($top_institution_qualifications, 0, 5, TRUE);
		$top_institution_societies = array_slice($top_institution_societies, 0, 5, TRUE);
				
	?>
	
	<div class="box" style="margin-top:20px;">
	
		<h2>Stats: Most Popular Institution in Qualifications</h2>

		<?php
						
			echo '<ul>';
			foreach ( $top_institution_qualifications AS $tsi => $val ) :
				echo '<li>' . $tsi . ' <span class="indicator">' . $val . '</span></li>';
			endforeach;
			echo '</ul>';
			
		?>
					
	</div>
	
	<div class="box" style="margin-top:20px;">
	
		<h2>Stats: Most Popular Institutions in Societies</h2>

		<?php
						
			echo '<ul>';
			foreach ( $top_institution_societies AS $tsi => $val ) :
				echo '<li>' . $tsi . ' <span class="indicator">' . $val . '</span></li>';
			endforeach;
			echo '</ul>';
			
		?>
					
	</div>
	
</div>

<div class="left box" style="margin-left:10px;margin-right:10px;width:auto;">
	
	<h2>Listed Institutions</h2>
	
	<table id="institution-list" style="padding:0 10px 10px;">
			
		<tbody>
		
			<?php $temp = array(); ?>
			<?php foreach ( $institutions->result() AS $i ) : ?>
			<?php $temp[$i->id] = $i->name; ?>
			<?php endforeach; ?>

		
			<?php foreach ( $institutions->result() AS $i ) : ?>
		
			<tr>
				
				<td width="40">
					<?php if($i->logo) : ?>
					<?=img( 'img/scale/' . str_replace( '/', '-', CDN_PATH ) . 'institution_images/' . $i->logo . '/40' );?>
					<?php else: ?>
					<img src="/img/placeholder/40/40/">
					<?php endif; ?>
				</td>
			
				<td class="search_me">
					<span class="searchable"><?=$i->name?></span> <?php if ( $i->parent ) : echo ' <small style="font-size:10px;font-weight:bold;color:#ff0000;">within ' . $temp[$i->parent] . '</small>'; endif; ?>
					<br><small style="color:#999;">Dependencies: <?=$i->q_count?> Qualifications, <?=$i->s_count?> Societies</small>
				</td>
				<td width="20">
					<?php if ( $i->active==1 ) : ?>
						<?=img( array( 'src' => 'assets/app/img/tick.png', 'style' => 'box-shadow:none', 'del' => 'tooltip-t', 'title' => 'Active!') )?>
					<?php elseif ( $i->active==2 ) : ?>
						<?=img( array( 'src' => 'assets/app/img/tick_grey.png', 'style' => 'box-shadow:none', 'del' => 'tooltip-t', 'title' => 'Ignored') )?>
					<?php else: ?>	
						<?=img( array( 'src' => 'assets/app/img/cross.png', 'style' => 'box-shadow:none', 'del' => 'tooltip-t', 'title' => 'Not Active!') )?>
					<?php endif; ?>
				</td>
				<td width="88">
					
					<a href="<?=site_url( 'admin/lists/institution_edit/' . $i->id )?>" class="a-button a-button-small">Edit</a>
					<?php if ( $i->q_count == 0 && $i->s_count == 0 ) : ?>
						<a href="<?=site_url( 'admin/lists/institution_delete/' .$i->id )?>" class="a-button a-button-small confirm-delete">Delete</a>
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

