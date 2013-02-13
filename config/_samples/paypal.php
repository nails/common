<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/*------------------------------------------------------------------------
 | PAYPAL
  ----------------------------------------------------------------------*/

//	'Live' details
$config['paypal']['live']['src']			= 'https://www.paypal.com/cgi-bin/webscr';
$config['paypal']['live']['email']			= '';
$config['paypal']['live']['password']		= '';
$config['paypal']['live']['notify']			= site_url( 'paypal/ipn' );
$config['paypal']['live']['thanks']			= site_url( 'paypal/processing' );
$config['paypal']['live']['cancel']			= site_url( 'paypal/cancel' );

//	'Sandbox' account details
$config['paypal']['sandbox']['src']			= 'https://www.sandbox.paypal.com/cgi-bin/webscr';
$config['paypal']['sandbox']['email']		= '';
$config['paypal']['sandbox']['password']	= '';
$config['paypal']['sandbox']['notify']		= site_url( 'paypal/ipn' );
$config['paypal']['sandbox']['thanks']		= site_url( 'paypal/processing' );
$config['paypal']['sandbox']['cancel']		= site_url( 'paypal/cancel' );