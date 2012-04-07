<!-- jQuery -->
<script type="text/javascript" charset="utf-8">
$(function () 
{

	$('.new_btn').click(function(){
	
		$('.new_frm').show();
		return false;
	
	});
	
	$( 'input[name=skill]' ).keyup( function() {

		var search = $(this).val();
		var counter = 0;
		
		$( '#subjects-list' ).find( '.searchable' ).each( function() {
		
			var skill = $(this).html();
			var pattern = new RegExp(search, "gi");
			
			if ( skill.match( pattern ) ) {
				
				$(this).parent().parent().show();
				
			} else {
				
				$(this).parent().parent().hide();
				
			}
			
			if ( search == '' ) {
				$( this ).parent().parent().show();
			}
		
		});
		
		//	How many results
		$( '#subjects-list .searchable' ).each( function() {
		
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
		var ok = confirm("Are you sure you wish to delete this subject? This cannot be undone.");
		if(ok==true){
			return true;
		}else{
			return false;
		}
	});
	
});
</script>


<h1>Manage School Subjects</h1>

<p>
	Subjects are used and specified in user qualifications.  You cannot delete any
	subject that is currently in use by a member within their profile. You should also take care when updating
	subjects, any updates will be reflected in member profiles.
</p>

<hr>

<div class="left" style="margin-left:10px;margin-right:10px;width:400px;">

	<div class="box">
	
		<h2>Add New Subject</h2>
		
			<div style="padding:0 12px;">
			
			<p>
				Add a new subject below. Ensure the subject you plan to add is not already listed. Tip: begin typing to filter
				the list of subjects shown.
			</p>
				
			<?=form_open_multipart( 'admin/lists/skill' )?>
			<?=form_hidden( 'save', TRUE )?>
			
			<?php
			
				if ( form_error( 'skill' ) || form_error( 'category' ) ) :
				
					echo '<div class="error" style="text-align:center">';
					echo form_error( 'skill' );
					echo form_error( 'category' );
					echo $error;
					echo '</div>';
				
				endif;
			
			?>
			
				<table class="blank" style="width:auto;">
				
					<tr>
					
						<td align="right"><strong>Skill</strong>*:</td>
						<td>
							<input type="text" name="skill" value="">
						</td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong>Category</strong>*:</td>
						<td>
							<select name="category">
								<option value="">Please Select...</option>
								<?php
									foreach ( $skill_categories->result() AS $ll ) :
										echo '<option value="'.$ll->id.'">'.$ll->category.'</option>';
									endforeach;
								?>
							</select>
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
		$top_subjects 	= array();
		
		//	For each skill result, pull the stats
		foreach ( $skills->result() AS $i ) :
			$top_subjects[$i->skill] 		= $i->u_count;
		endforeach;
		
		//	Sort by value, high to low
		arsort($top_subjects);
		$top_subjects = array_slice($top_subjects, 0, 5, TRUE);
				
	?>
	
	<div class="box" style="margin-top:20px;padding:">
	
		<h2>Stats: Most Common Subjects in Qualifications</h2>

		<?php
						
			echo '<ul>';
			foreach ( $top_subjects AS $tsi => $val ) :
				echo '<li>' . $tsi . ' <span class="indicator">' . $val . '</span></li>';
			endforeach;
			echo '</ul>';
			
		?>
					
	</div>
		
</div>

<div class="left box" style="margin-left:10px;margin-right:10px;width:auto;">
	
	<h2>Listed Subjects</h2>
	
	<table id="subjects-list" style="padding:0 10px 10px;">
			
		<tbody>
		
			<?php $temp = array(); ?>
			<?php foreach ( $skill_categories->result() AS $c ) : ?>
			<?php $temp[$c->id] = $c->category; ?>
			<?php endforeach; ?>

		
			<?php foreach ( $skills->result() AS $i ) : ?>
		
			<tr>
			
				<td class="search_me">
					<span class="searchable"><?=$i->skill?></span> <?php if ( $i->category ) : echo ' (' . $temp[$i->category] . ')'; else: echo '<small>NO CATEGORY!</small>'; endif; ?>
					<br><small style="color:#999;">Dependencies: <?=$i->u_count?> users list this</small>
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
					
					<?=anchor( 'admin/lists/skill_edit/' . $i->id, 'Edit', 'class="a-button a-button-small"' )?>
					<?php if ( $i->u_count == 0 ) : ?>
						<?=anchor( 'admin/lists/skill_delete/' . $i->id, 'Delete', 'class="a-button a-button-small confirm-delete"' )?>
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

