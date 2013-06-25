<?php

/*
 | --------------------------------------------------------------------
 | NAILS INSTALLER
 | --------------------------------------------------------------------
 |
 | This tiny class is the kick point for installing a new Nails application
 | or installing modules into an existing application.
 |
 | Lead Developer: Pablo de la Peña	(p@shedcollective.org, @hellopablo)
 | Lead Developer: Gary Duncan		(g@shedcollective.org, @gsdd)
 | 
 | Documentation: http://docs.nailsapp.co.uk
 | 
 | CodeIgniter version: v2.1.0
 |
 |
 */


class NAILS_Configure
{
	public function __construct()
	{
		echo 'Configure!';
	}
}

$CONFIGURE = new NAILS_Configure();