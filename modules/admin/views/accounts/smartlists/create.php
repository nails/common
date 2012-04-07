<!--	CUSTOM CSS	-->
<style type="text/css">
	
	.col
	{
		width: 200px;
	}
	.operator
	{
		width: 200px;
	}
	.value
	{
		width: 200px;
	}
	.options
	{
		width: 50px;
	}
	.col .selector
	{
		display: none;
	}
	.col .selector.disabled
	{
		display: block;
	}

</style>

<script type="text/javascript">
<!--// Hide from old browsers
	
	$(function(){
	
		$( '.table select' ).live( 'change', function(){
		
			var row_id = $( this ).parents( 'tr' ).attr( 'id' )
			
			if ( $( this ).val() != '' ) {
			
				$( this ).parents( 'tr' ).find( '.col .selector' ).hide();
				$( this ).parents( 'tr' ).find( '.col .selector#uniform-' + row_id + '_' +  $( this ).val() ).show();
				
			} else {
			
				$( this ).parents( 'tr' ).find( '.col .selector' ).hide();
				$( this ).parents( 'tr' ).find( '.col .selector.disabled' ).show();
			
			}
		
		});
		
		var row_id = 0;
		$( 'a#add_row' ).click( function() {
			
			row_id++
			var tr = 	'<tr id="row_' + row_id + '">' +
						'<td class="table"><?=str_replace( "\n", '', form_dropdown( 'where[\' + row_id + \'][table]', $tables ) )?></td>' +
						'<td class="col">' +
						'<?php
								
								echo str_replace( "\n", '', form_dropdown( 'where[\' + row_id + \'][col]', array( '' => '-' ), NULL, 'disabled="disabled"' ) );
							
								foreach ( $cols AS $t => $c ) :
								
									echo str_replace( "\n", '', form_dropdown( 'where[\' + row_id + \'][col]['.$t.']', $c, NULL, 'id="row_\' + row_id + \'_'.$t.'"' ) );
									
								endforeach;
							?>' +
						'</td>' +
						'<td class="operator"><?=str_replace( "\n", '', form_dropdown( 'where[\' + row_id + \'][operator]', $operators ) )?></td>' +
						'<td class="value"><?=str_replace( "\n", '', form_input( 'where[\' + row_id + \'][value]' ) )?></td>' +
						'<td><a href="#" class="remove a-button a-button-small">Remove</a></td>' +
						'</tr>';
			
			$( 'tbody' ).append( tr );
			
			$( 'tr#row_' + row_id + ' select, tr#row_' + row_id + ' input' ).uniform();
			
			return false;
		
		});
		
		$( 'a.remove' ).live( 'click', function() {
			
			$( this ).parents( 'tr' ).remove();
		
		});
		
		//	Form validation
		$( 'form' ).submit( function() {
			
			var errors = 0;
			$( '#error-breakdown p span' ).html( '' );
			
			//	Check title
			debug.log( $( 'input[name=title]' ).val().replace( /[^a-zA-Z0-9]/gi,'' ).length );
			if ( $( 'input[name=title]' ).val().replace( /[^a-zA-Z0-9]/gi,'' ).length == 0 ) {
				errors++;
				$( '#error-breakdown p span' ).append( '<br />&rsaquo; The Title field is required' );
			}
			
			//	Check rules
			$( 'tbody tr' ).each( function() {
			
				row_error = 0;
				
				//	Check this row for errors
				//	Is there a table?
				if ( $( this ).find( '.table select' ).val() == '' ) {
					errors++;
					row_error++;
					$( '#error-breakdown p span' ).append( '<br />&rsaquo; The Table field is required' );
				}
				
				//	Is there a column selected for the selected table?
				if ( $( this ).find( '.col select#' + $( this ).attr( 'id' ) + '_' + $( this ).find( '.table select' ).val() ).val() == 0 ) {
					errors++;
					row_error++;
					$( '#error-breakdown p span' ).append( '<br />&rsaquo; The Column field is required' );
				}
				
				//	Is there an operator?
				if ( $( this ).find( '.operator select' ).val() == '' ) {
					errors++;
					row_error++;
					$( '#error-breakdown p span' ).append( '<br />&rsaquo; The Operator field is required' );
				}
				
				//	Is there a value?
				if ( $( this ).find( '.value input' ).val().replace( / /g, '' ) == '' ) {
					errors++;
					row_error++;
					$( '#error-breakdown p span' ).append( '<br />&rsaquo; The Value field is required' );
				}
				
				
				if ( row_error ) {
				
					$( this ).addClass( 'error' );
					
				} else {
				
					$( this ).removeClass( 'error' );
				
				}
			
			});
			
			if ( errors ) {
			
				$( '#error-breakdown' ).show();
			
			} else {
				
				return true;
				
			}
			
			return false;
			
			
		
		});
		
	});
	
//-->
</script>

<section>
	<h1>Smart Lists &rsaquo; Smart List Builder</h1>
</section>

<p>Use the form below to build your search query. You must have previously taken a note of the relevant field names and ID's required to build the query properly. Please also note that a matched account must meet <strong>all</strong> rules.</p>

<hr>
<div class="error">
	<p><strong>To be developed in the future</strong>
	<br> &rsaquo; 'Helpers' to help find ID's of objects (e.g: search institutions for 'Oxford' and get it's ID)
	</p>
	
</div>

<hr>
<?=form_open()?>
<?=form_hidden( 'save', TRUE )?>

<p>
	<label style="min-width:100px;">Smart List Title</label>
	<?=form_input( 'title', set_value( 'title' ), 'style="width:350px;"' )?>
</p>

<div class="error" id="error-breakdown" style="display:none;">
	<p>
		<strong><?=lang( 'there_is_an_error_in_this_section' )?></strong>
		<span></span>
	</p>
</div>

<table>

	<thead>
		<tr>
			<th>Table</th>
			<th class="col">Column</th>
			<th class="operator">Operator</th>
			<th class="value">Value</th>
			<th class="options">&nbsp;</th>
		</tr>
	</thead>
	
	<tbody>
		<tr id="row_0">
			<td class="table"><?=form_dropdown( 'where[0][table]', $tables, set_value( 'where[0][table]' ) )?></td>
			<td class="col">
				<?php
					
					echo form_dropdown( 'where[0][col]', array( '' => '-' ), NULL, 'disabled="disabled"' );
				
					foreach ( $cols AS $t => $c ) :
					
						echo form_dropdown( 'where[0][col]['.$t.']', $c, set_value( 'where[0][col]' ), 'id="row_0_'.$t.'"' );
						
					endforeach;
				?>
				</td>
			<td class="operator"><?=form_dropdown( 'where[0][operator]', $operators, set_value( 'where[0][operator]' ) )?></td>
			<td class="value"><?=form_input( 'where[0][value]', set_value( 'where[0][value]' ) )?></td>
			<td><a href="#" class="remove a-button a-button-small">Remove</a></td>
		</tr>
	</tbody>
	
</table>

<p>
	<a href="#" id="add_row" class="a-button a-button-small">+ Add another rule</a>
</p>

<p style="margin-top:20px;">
	<?=form_submit( 'submit',	'Save Smart List' )?>
	<!--<?=form_submit( 'test',		'Test Search', 'disabled' )?>-->
</p>
<?=form_close()?>