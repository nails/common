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
		
		$( '#sector-list' ).find( '.searchable' ).each( function() {
		
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
		$( '#sector-list .searchable' ).each( function() {
		
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
		var ok = confirm("Are you sure you wish to delete this sector? This cannot be undone.");
		if(ok==true){
			return true;
		}else{
			return false;
		}
	});
	
});
</script>


<h1>Manage Sectors</h1>

<p>
	Sectors are available to users via their profile (i.e. desired sectors), to employers (i.e. sectors the business operates within) 
	and internships (i.e. internships must have one sector specified). You cannot delete sectors that are currently in use by one of the
	dependencies described. You must also take care when updating sectors; any updates will be reflected in both saved and listed
	information displayed on the website.
</p>

<hr>

<div class="left" style="margin-left:10px;margin-right:10px;width:300px;">

	<div class="box">
	
		<h2>Add New Sector</h2>
		
			<div style="padding:0 12px;">
			
			<p>
				Add a new sector below. Ensure the sector you plan to add is not already listed. Tip: begin typing to filter
				the list of sectors shown.
			</p>
				
			<?=form_open_multipart( 'admin/lists/sector' )?>
			<?=form_hidden( 'save', TRUE )?>
			
			<?php
			
				if ( form_error( 'title' ) ) :
				
					echo '<div class="error" style="text-align:center">';
					echo form_error('title');
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
					
						<td align="right"><strong>Short Title (Optional)</strong>:</td>
						<td>
							<input type="text" name="title_short" value="">
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
		$top_sector_interns 	= array();
		$top_sector_employers 	= array();
		$top_sector_internships	= array();
		
		//	For each sector result, pull the stats
		foreach ( $sectors AS $s ) :
			$top_sector_interns[ $s->title ] 		= $s->u_count;
			$top_sector_employers[ $s->title ] 	= $s->e_count;
			$top_sector_internships[ $s->title ] 	= $s->i_count;
		endforeach;
		
		//	Sort by value, high to low
		arsort( $top_sector_interns );
		arsort( $top_sector_employers );
		arsort( $top_sector_internships );
		$top_sector_interns = array_slice( $top_sector_interns, 0, 5, TRUE );
		$top_sector_employers = array_slice( $top_sector_employers, 0, 5, TRUE );
		$top_sector_internships = array_slice( $top_sector_internships, 0, 5, TRUE );
				
	?>
	
	<div class="box" style="margin-top:20px;padding:">
	
		<h2>Stats: Intern Preferences, Most Popular</h2>

		<?php
						
			echo '<ul>';
			foreach ( $top_sector_interns AS $tsi => $val ) :
				echo '<li>' . $tsi . ' <span class="indicator">' . $val . '</span></li>';
			endforeach;
			echo '</ul>';
			
		?>
					
	</div>
	
	<div class="box" style="margin-top:20px;padding:">
	
		<h2>Stats: Internships, Most Common</h2>

		<?php
						
			echo '<ul>';
			foreach ( $top_sector_internships AS $tsi => $val ) :
				echo '<li>' . $tsi . ' <span class="indicator">' . $val . '</span></li>';
			endforeach;
			echo '</ul>';
			
		?>
					
	</div>
	
	<div class="box" style="margin-top:20px;padding:">
	
		<h2>Stats: Employers, Most Common</h2>

		<?php
						
			echo '<ul>';
			foreach ( $top_sector_employers AS $tsi => $val ) :
				echo '<li>' . $tsi . ' <span class="indicator">' . $val . '</span></li>';
			endforeach;
			echo '</ul>';
			
		?>
					
	</div>

</div>

<div class="left box" style="margin-left:10px;margin-right:10px;width:auto;">
	
	<h2>Listed Sectors</h2>
	
	<table id="sector-list" style="padding:0 10px 10px;">
			
		<tbody>
		
			<?php foreach ( $sectors AS $s ) : ?>
		
			<tr>
			
				<td class="search_me">
					<span class="searchable"><span class="title"><?=$s->title?></span><?php if ( $s->title_short ) : echo ' (<span class="title_short">' . $s->title_short . '</span>)'; endif; ?></span>
					<br><span class="inline_indicator"><?=$s->e_count?> Employers</span> <a href="<?=site_url( 'admin/accounts/index?filter[sector]=' . $s->id )?>" class="inline_indicator"><?=$s->u_count?> Interns</a> <span class="inline_indicator"><?=$s->i_count?> Internships</span>
				</td>
				<td width="88">
					
					<?=anchor( 'admin/lists/sector_edit/' . $s->id, 'Edit', 'class="a-button a-button-small"' )?>
					<?php if ( $s->u_count == 0 ) : ?>
						<?=anchor( 'admin/lists/sector_delete/' . $s->id, 'Delete', 'class="a-button a-button-small confirm-delete"' )?>
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

