<div class="container shop checkout shipping-payment">
	<?php
	
		$_uri  = uri_string();
		$_uri .= $guest ? '?guest=true' : ''; 
		
		echo form_open( $_uri );
		
		// --------------------------------------------------------------------------
		
		if ( $requires_shipping ) :
		
			if ( count( $payment_gateways ) > 1 ) :
			
				echo '<h2>Shipping Options</h2>';
			
			endif;
			
			// --------------------------------------------------------------------------
			
			$_field					= array();
			$_field['key']			= 'addressee';
			$_field['label']		= 'Addressee';
			$_field['placeholder']	= 'e.g John Smith';
			$_field['default']		= active_user( 'first_name,last_name' );
			
			echo form_field( $_field );
			
			// --------------------------------------------------------------------------
			
			$_field					= array();
			$_field['key']			= 'line_1';
			$_field['label']		= 'House number and Street';
			$_field['placeholder']	= 'e.g 1 Chapel Hill';
			$_field['default']		= active_user( 'address_line_1' );
			
			echo form_field( $_field );
			
			// --------------------------------------------------------------------------
			
			$_field					= array();
			$_field['key']			= 'line_2';
			$_field['label']		= 'Locality';
			$_field['placeholder']	= 'e.g Heswall';
			$_field['default']		= active_user( 'address_line_2' );
			
			echo form_field( $_field );
			
			// --------------------------------------------------------------------------
			
			$_field					= array();
			$_field['key']			= 'town';
			$_field['label']		= 'Town';
			$_field['placeholder']	= 'e.g BOURNEMOUTH';
			$_field['default']		= active_user( 'address_town' );
			
			echo form_field( $_field );
			
			// --------------------------------------------------------------------------
			
			$_field					= array();
			$_field['key']			= 'postcode';
			$_field['label']		= 'Postcode';
			$_field['placeholder']	= 'e.g BH1 1AA';
			$_field['default']		= active_user( 'address_postcode' );
			
			echo form_field( $_field );
			
			// --------------------------------------------------------------------------
			
			$_field					= array();
			$_field['key']			= 'country';
			$_field['label']		= 'Country';
			$_field['class']		= 'chosen';
			$_field['placeholder']	= 'Choose a country';
			$_field['default']		= active_user( 'address_country' );
			
			echo form_field_dropdown( $_field, array( 'United Kingdom','United States','Australia' ) );

			
			// --------------------------------------------------------------------------
			
			$_display = $this->input->post( 'country' ) == 'ID OF USA' ? 'block' : 'none';
			echo '<div id="state-chooser-us" style="display:' . $_display . '">';
			
				$_field					= array();
				$_field['key']			= 'us_state';
				$_field['label']		= 'State';
				$_field['class']		= 'chosen';
				$_field['placeholder']	= 'Choose a state';
				$_field['default']		= active_user( 'address_state' );
				
				echo form_field_dropdown( $_field, array( 'USA Example State','USA Example State','USA Example State' ) );
			
			echo '</div>';
			
			// --------------------------------------------------------------------------
			
			$_display = $this->input->post( 'country' ) == 'ID OF AUSTRALIA' ? 'block' : 'none';
			echo '<div id="state-chooser-aus" style="display:' . $_display . '">';
			
				$_field					= array();
				$_field['key']			= 'aus_state';
				$_field['label']		= 'State';
				$_field['class']		= 'chosen';
				$_field['placeholder']	= 'Choose a state';
				$_field['default']		= active_user( 'address_state' );
				
				echo form_field_dropdown( $_field, array( 'AUS Example State','AUS Example State','AUS Example State' ) );
			
			echo '</div>';
			
			// --------------------------------------------------------------------------
			
			if ( count( $payment_gateways ) > 1 ) :
			
				echo '<h2>Payment Options</h2>';
			
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Payment Options
		if ( count( $payment_gateways ) > 1 ) :
		
			echo '<p>Please choose your preferred payment method.</p>';
			
			echo '<ul class="payment-gateways clearfix">';
			foreach ( $payment_gateways AS $pg ) :
			
				echo '<li>';
				echo '<label>';
				echo form_radio( 'payment_gateway', $pg->id );
				if ( $pg->logo ) :
				
					echo img( NAILS_URL . 'img/modules/shop/payment-gateway/' . $pg->logo );
					
				else :
				
					echo $pg->label;
				
				endif;
				echo '</label>';
				echo '</li>';
			
			endforeach;
			echo '</ul>';
		
		else :
		
			echo form_hidden( 'payment_gateway', $payment_gateways[0]->id );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		
		echo form_submit( 'submit', lang( 'action_continue' ), 'class="awesome"' );
		
		// --------------------------------------------------------------------------
		
		echo form_close();
	?>
</div>

<script tyle="text/javascript">
<!--//

	$(function(){
	
		//	Chosen
		//$( 'select.chosen' ).chosen();
		
		// --------------------------------------------------------------------------
		
		//	US state chooser
		$( 'select[name=country]' ).on( 'change', function() {
		
			_value = $('option:selected',this).text();
			
			if ( _value ==  'United States'  )
			{
				$( '#state-chooser-us' ).show();
			}
			else
			{
				$( '#state-chooser-us' ).hide();
			}
			
			// --------------------------------------------------------------------------
			
			if ( _value ==  'Australia'  )
			{
				$( '#state-chooser-aus' ).show();
			}
			else
			{
				$( '#state-chooser-aus' ).hide();
			}
			
		} );
	
	});

//-->
</script>