<?php

/**
 * A Base API class from which all API implementations will extend.
 *
 * PHP version 5
 *
 * LICENSE: Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category   API
 * @package    Base_API
 * @author     Dave Goodchild <dave@hostliketoast.com>
 * @copyright  2011 Dave Goodchild and Host Like Toast
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    v1.3, 04/08/2011 @ 16:21
 * @link       http://www.hostliketoast.com
 */

class BasicResponseObject {
	
	public function __construct( $inaInitiate = array() ) {
		$this->applyPropertiesFromArray( $inaInitiate );
	}
	
	protected function applyPropertiesFromArray( $inaProperties ) {
		foreach ( $inaProperties as $sKey => $sValue ) {
			$this->{$sKey} = $sValue;
		}
	}
}

class BasicBackup extends BasicResponseObject {
	
}

class RestfulHTTPClient {
	
	const METHOD_GET		= 'GET';
	const METHOD_POST		= 'POST';
	const METHOD_PUT		= 'PUT';
	const METHOD_DELETE		= 'DELETE';
	
	public function get( $insUrl, $inaHeaders, $inaData = array() ) {}
	public function post( $insUrl, $inaHeaders, $inaData = array() ) {}
	public function put( $insUrl, $inaHeaders, $inaData = array() ) {}
	public function delete( $insUrl, $inaHeaders, $inaData = array() ) {}
}

class Base_Api {
	
	const THROW_ERRORS	= false;
	
	protected $m_oHttpClient;
	
	public function __construct() {
		
	}
	
	public function backup() {
		throw new Exception( "Backup process has not been defined for ".$this->getApiName()." API" );
	}
	
	protected function getRequestUrl( $insRequest = '' ) {
		return '';
	}

	protected function getRequestHeaders() {
		return array();
	}

	protected function get( $insRequest, $inaData = array() ) {
		$oResult = call_user_func(
			array( $this->m_oHttpClient, 'get' ),
			$this->getRequestUrl( $insRequest ),
			$this->getRequestHeaders(),
			$inaData
		);
		$this->throwError( $oResult );
		
		return $oResult;
	}
	
	protected function create( $insRequest, $inaData = array() ) {
		$oResult = call_user_func(
			array( $this->m_oHttpClient, 'post' ),
			$this->getRequestUrl( $insRequest ),
			$this->getRequestHeaders(),
			$inaData
		);
		$this->throwError( $oResult );
		
		return $oResult;
	}
	
	protected function update( $insRequest, $inaData = array() ) {
		$oResult = call_user_func(
			array( $this->m_oHttpClient, 'put' ),
			$this->getRequestUrl( $insRequest ),
			$this->getRequestHeaders(),
			$inaData
		);
		$this->throwError( $oResult );
		
		return $oResult;
	}
	
	protected function delete( $insRequest, $inaData = array() ) {
		$oResult = call_user_func(
			array( $this->m_oHttpClient, 'delete' ),
			$this->getRequestUrl( $insRequest ),
			$this->getRequestHeaders(),
			$inaData
		);
		$this->throwError( $oResult );
		
		return $oResult;
	}
	
	protected function getApiName() {
		return '(API NAME NOT SPECIFIED)';
	}
	
	protected function throwError( $inoResult ) {
		if ( !$inoResult->success && !self::THROW_ERRORS ) {
			//echo '<div style="background-color:#900000;padding:15px;color:#fff;white-space:pre;">';
			//var_dump( $inoResult );
			//echo '</div>';
		}
		if ( $inoResult->success || !self::THROW_ERRORS ) {
			return;
		}
		throw new Exception( $this->getApiName()." API request failed: (".$inoResult->status_code.")".$inoResult->error );
	}
}

class ApiHelper {
	
	static public function formatHeaders( $insHeaders ) {
		$aHeaderLines = explode( "\r\n", $insHeaders );
		$aHeaders = array();
		for ( $i = 0; $i < count( $aHeaderLines ); $i++ ) {
			$aParts = explode( ":", $aHeaderLines[$i], 2 );
			if ( isset( $aParts[1] ) ) {
				$aParts[1] = trim( $aParts[1] );
			}
			$aHeaders[trim( $aParts[0] )] = isset( $aParts[1] )? $aParts[1]: '';
		}
		return $aHeaders;
	}
	
	static public function xmlToArray( $insXml ) {
		if ( substr( trim( $insXml ), 0, 1 ) != '<' ) {
			return array();
		}
		$oSxi = new SimpleXmlIterator( $insXml, null, false );
		return self::simpleXmlIteratorToArray( $oSxi );
	}

	static protected function simpleXmlIteratorToArray( $inoSxi ) {
		$aOutput = array();
		for( $inoSxi->rewind(); $inoSxi->valid(); $inoSxi->next() ) {
			if ( !array_key_exists( $inoSxi->key(), $aOutput ) ) {
				$aOutput[$inoSxi->key()] = array();
			}
			if ( $inoSxi->hasChildren() ) {
				$aOutput[$inoSxi->key()][] = self::simpleXmlIteratorToArray( $inoSxi->current() );
			}
			else {
				$aOutput[$inoSxi->key()][] = strval( $inoSxi->current() );
			}
		}
		return $aOutput;
	}
	
	static public function objectToArray( $inoObject ) {
		if ( is_object( $inoObject ) ) {
			$inoObject = get_object_vars( $inoObject );
		}
 
		if ( is_array( $inoObject ) ) {
			return array_map( array( 'self', __FUNCTION__ ), $inoObject );
		}
		return $inoObject;
	}
	
	static public function arrayToObject( $inaArray ) {
		if ( is_array( $inaArray ) ) {			
			return (object) array_map( array( 'self', __FUNCTION__ ), $inaArray );
		}
		return $inaArray;
	}
}