<?php

/**
 * PHP Implementation of the FreeAgent API.
 *
 * http://www.freeagentcentral.com/developers/freeagent-api
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
 * @package    Xml_Generator
 * @author     Dave Goodchild <dave@hostliketoast.com>
 * @copyright  2011 Dave Goodchild and Host Like Toast
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    v1.3, 04/08/2011 @ 16:21
 * @link       http://www.hostliketoast.com
 */

class Xml_Generator {
	private $m_oXml;
	private $m_sVersion;
	private $m_sCharset;
	private $m_sRootNode;
	
	private $m_aAdditional;
	
	private $m_oDB;
 
	public function __construct( $inoDB = null, $insVersion = '1.0', $insCharset = 'UTF-8' ) {
		$this->m_sVersion = $insVersion;
		$this->m_sCharset = $insCharset;
		$this->m_sRootNode = 'root';
		
		$this->m_aAdditional = array();
		
		$this->m_oDB = $inoDB;
	}
	
	public function setRootNode( $insRootNode ) {
		$this->m_sRootNode = $insRootNode;
	}
	
	public function addAdditionalNode( $insKey, $insValue ) {
		$this->m_aAdditional[$insKey] = $insValue;
	}
	
	public function applyAdditionalXml() {
		//$this->m_oXml->writeElement( $sKey, $sValue );
		$this->writeArray( $this->m_oXml, $this->m_aAdditional );
	}

	public function generateFromArray( $inaData = array() ) {
		$this->generateStart();
		$this->applyAdditionalXml();
		$this->writeArray( $this->m_oXml, $inaData );
		
		return $this->generateFinish();
	}
 
	private function writeArray( XMLWriter $inoXml, $inaData ) {
		foreach ( $inaData as $sKey => $sValue ) {
			if ( is_array( $sValue ) ) {		
				$inoXml->startElement( $sKey );
				$this->write( $inoXml, $sValue );
				$inoXml->endElement();
				continue;
			}
			$inoXml->writeElement( $sKey, $sValue );
		}
	}
	
	private function generateStart() {
		$this->m_oXml = new XmlWriter();
		$this->m_oXml->openMemory();
		$this->m_oXml->startDocument( $this->m_sVersion, $this->m_sCharset );
		$this->m_oXml->startElement( $this->m_sRootNode );
	}
	
	private function generateFinish() {
		$this->m_oXml->endElement();
		$this->m_oXml->endDocument();
		$sXml = $this->m_oXml->outputMemory( true );
		$this->m_oXml->flush();
		return $sXml;		
	}
}
