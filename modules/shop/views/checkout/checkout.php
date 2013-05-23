<div class="container shop checkout shipping-payment">
	<?php
	
		$_uri  = uri_string();
		$_uri .= $guest ? '?guest=true' : ''; 
		
		echo form_open( $_uri );
		
		// --------------------------------------------------------------------------
		
		//	Personal Details
		if ( $guest ) :

			echo '<section class="row sixteen columns first last">';
		
			//	Title
			if ( $requires_shipping || ( count( $payment_gateways ) > 1 && $basket->totals->grand > 0 ) ) :
			
				echo '<h2>Personal Details</h2>';
			
			endif;
			
			// --------------------------------------------------------------------------
			
			$_field					= array();
			$_field['key']			= 'first_name';
			$_field['label']		= 'First Name';
			$_field['placeholder']	= 'e.g John';
			$_field['default']		= $basket->personal_details->first_name ? $basket->personal_details->first_name : active_user( 'first_name' );
			
			echo form_field( $_field );
			
			// --------------------------------------------------------------------------
			
			$_field					= array();
			$_field['key']			= 'last_name';
			$_field['label']		= 'Surname';
			$_field['placeholder']	= 'e.g Smith';
			$_field['default']		= $basket->personal_details->last_name ? $basket->personal_details->last_name : active_user( 'last_name' );
			
			echo form_field( $_field );
			
			// --------------------------------------------------------------------------
			
			$_field					= array();
			$_field['key']			= 'email';
			$_field['label']		= 'Email';
			$_field['placeholder']	= 'e.g john@smith.com';
			$_field['default']		= $basket->personal_details->email ? $basket->personal_details->email : active_user( 'email' );
			
			echo form_field( $_field );

			echo '</section>';
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Shipping Options
		if ( $requires_shipping ) :

			echo '<section class="row sixteen columns first last">';
		
			//	Title
			if ( $guest || ( count( $payment_gateways ) > 1 && $basket->totals->grand > 0 ) ) :
			
				echo '<h2>Shipping Details</h2>';
			
			endif;
			
			// --------------------------------------------------------------------------
			
			$_field					= array();
			$_field['key']			= 'addressee';
			$_field['label']		= 'Addressee';
			$_field['placeholder']	= 'e.g John Smith';
			$_field['default']		= $basket->shipping_details->addressee ? $basket->shipping_details->addressee : active_user( 'first_name,last_name' );
			
			echo form_field( $_field );
			
			// --------------------------------------------------------------------------
			
			$_field					= array();
			$_field['key']			= 'line_1';
			$_field['label']		= 'House number and Street';
			$_field['placeholder']	= 'e.g 1 Chapel Hill';
			$_field['default']		= $basket->shipping_details->line_1 ? $basket->shipping_details->line_1 : active_user( 'address_line_1' );
			
			echo form_field( $_field );
			
			// --------------------------------------------------------------------------
			
			$_field					= array();
			$_field['key']			= 'line_2';
			$_field['label']		= 'Locality';
			$_field['placeholder']	= 'e.g Heswall';
			$_field['default']		= $basket->shipping_details->line_2 ? $basket->shipping_details->line_2 : active_user( 'address_line_2' );
			
			echo form_field( $_field );
			
			// --------------------------------------------------------------------------
			
			$_field					= array();
			$_field['key']			= 'town';
			$_field['label']		= 'Town';
			$_field['placeholder']	= 'e.g BOURNEMOUTH';
			$_field['default']		= $basket->shipping_details->town ? $basket->shipping_details->town : active_user( 'address_town' );
			
			echo form_field( $_field );
			
			// --------------------------------------------------------------------------
			
			$_field					= array();
			$_field['key']			= 'postcode';
			$_field['label']		= 'Postcode';
			$_field['placeholder']	= 'e.g BH1 1AA';
			$_field['default']		= $basket->shipping_details->postcode ? $basket->shipping_details->postcode : active_user( 'address_postcode' );
			
			echo form_field( $_field );
			
			// --------------------------------------------------------------------------
			
			$_field					= array();
			$_field['key']			= 'country';
			$_field['label']		= 'Country';
			$_field['class']		= 'chosen';
			$_field['placeholder']	= 'Choose a country';
			$_field['default']		= $basket->shipping_details->country ? $basket->shipping_details->country : active_user( 'address_line_country' );
			
			echo form_field_dropdown( $_field, array( 'United Kingdom','United States','Australia' ) );
			
			// --------------------------------------------------------------------------
			
			$_display = $this->input->post( 'country' ) == 'ID OF USA' ? 'block' : 'none';
			echo '<div id="state-chooser-us" style="display:' . $_display . '">';
			
				$_field					= array();
				$_field['key']			= 'us_state';
				$_field['label']		= 'State';
				$_field['class']		= 'chosen';
				$_field['placeholder']	= 'Choose a state';
				$_field['default']		= $basket->shipping_details->state ? $basket->shipping_details->state : active_user( 'address_state' );
				
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
				$_field['default']		= $basket->shipping_details->state ? $basket->shipping_details->state : active_user( 'address_state' );
				
				echo form_field_dropdown( $_field, array( 'AUS Example State','AUS Example State','AUS Example State' ) );
			
			echo '</div>';

			echo '</section>';
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Payment Options
		if ( ( count( $payment_gateways ) > 1 && $basket->totals->grand > 0 ) ) :
		
			echo '<section class="row sixteen columns first last">';

			//	Title
			if ( $guest || $requires_shipping ) :
			
				echo '<h2>Payment Options</h2>';
			
			endif;
		
			echo '<p>Please choose your preferred payment method.</p>';
			
			echo '<ul class="payment-gateways">';
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

			echo '</section>';
		
		else :
		
			echo form_hidden( 'payment_gateway', $payment_gateways[0]->id );
		
		endif;
		
		// --------------------------------------------------------------------------

		$_uri = shop_setting( 'shop_url' ) . 'basket';
		$_uri .= $guest ? '?guest=true' : '';
		
		echo '<div class="row sixteen columns first last">';
		echo '<hr />';
		echo anchor( $_uri, lang( 'action_back' ), 'class="awesome small"' );
		echo form_submit( 'submit', lang( 'action_continue' ), 'class="awesome" style="float:right;margin-right:0;"' );
		echo '</div>';
		
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