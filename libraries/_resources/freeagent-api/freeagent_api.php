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
 * @package    FreeAgent_API
 * @author     Dave Goodchild <dave@hostliketoast.com>
 * @copyright  2011 Dave Goodchild and Host Like Toast
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    v1.3, 04/08/2011 @ 16:21
 * @link       http://www.hostliketoast.com
 */

class FreeAgent_Api extends Base_Api {
	
	const API_NAME		= 'FreeAgent';
	
	const PROTOCOL		= 'https';
	const DOMAIN		= 'freeagentcentral.com';
	
	const FORMAT		= 'json';
	const THROW_ERRORS	= true;
	
	private $m_sCompanyName;
	private $m_sUsername;
	private $m_sPassword;
	
	private $m_sBaseUrl;
	
	public function __construct( $insCompanyName, $insUsername, $insPassword ) {
		$this->m_sCompanyName	= $insCompanyName;
		$this->m_sUsername		= $insUsername;
		$this->m_sPassword		= $insPassword;
		
		$this->m_sBaseUrl		= self::PROTOCOL.'://'.$this->m_sCompanyName.'.'.self::DOMAIN;
		$this->m_oHttpClient	= new FreeAgent_RestfulHttpClient( $this->getRequestCredentials() );
	}
	
	protected function getApiName() {
		return self::API_NAME;
	}
	
	public function getVerify() {
		return $this->get( 'verify' );
	}
	
	public function getInvoiceTimeline() {
		return $this->get( 'company/invoice_timeline' );
	}
	
	public function getTaxTimeline() {
		return $this->get( 'company/tax_timeline' );
	}
	
	public function getContacts() {
		return $this->get( 'contacts' );
	}
	
	public function getContact( $insId ) {
		return $this->get( 'contacts/'.$insId );
	}
	
	public function getContactInvoices( $insContactId ) {
		return $this->get( 'contacts/'.$insContactId.'/invoices' );
	}

	public function getBills( $insFrom = null, $insTo = null ) {
		if ( is_null( $insFrom ) ) {
			$nNow = gmmktime();
			$nFourWeeksAgo = $nNow - (60*60*24*28);

			$insFrom = gmdate( 'Y-m-d', $nFourWeeksAgo );
			$insTo = gmdate( 'Y-m-d', $nNow );
		}
		$aParams = array( 'view' => $insFrom.'_'.$insTo );
		return $this->get( 'bills', $aParams );
	}

	public function getBill( $insId ) {
		return $this->get( 'bills/'.$insId );
	}
	
	public function getBillTypes() {
		return $this->get( 'bills/types' );
	}
	
	public function getProjects( $insView = 'all' ) {
		if ( in_array( strtolower( $insView ), array( 'all', 'active', 'completed', 'cancelled', 'inactive' ) ) ) {
			$insView == 'all';
		}
		$aParams = array( 'view' => $insView );
		return $this->get( 'projects', $aParams );
	}
	
	public function getProject( $insId ) {
		return $this->get( 'projects/'.$insId );
	}
	
	public function getProjectTasks( $insProjectId ) {
		return $this->get( 'projects/'.$insProjectId.'/tasks' );
	}
	
	public function getProjectTask( $insProjectId, $insTaskId ) {
		return $this->get( 'projects/'.$insProjectId.'/tasks/'.$insTaskId );
	}
	
	public function getProjectInvoices( $insProjectId ) {
		return $this->get( 'projects/'.$insProjectId.'/invoices' );
	}
	
	public function getProjectTimeslips( $insProjectId ) {
		return $this->get( 'projects/'.$insProjectId.'/timeslips' );
	}
	
	public function getInvoices( $insView = 'all' ) {
		if ( in_array( strtolower( $insView ), array( 'all', 'recent_open_or_overdue', 'open_or_overdue', 'draft', 'scheduled_to_email', 'thank_you_emails', 'reminder_emails', 'last_3_months', 'last_6_months' ) ) ) {
			$insView == 'all';
		}
		$aParams = array( 'view' => $insView );
		return $this->get( 'invoices', $aParams );
	}
	
	public function getInvoicesByRange( $insFrom = null, $insTo = null ) {
		if ( is_null( $insFrom ) ) {
			$nNow = gmmktime();
			$nFourWeeksAgo = $nNow - (60*60*24*28);

			$insFrom = gmdate( 'Y-m-d', $nFourWeeksAgo );
			$insTo = gmdate( 'Y-m-d', $nNow );
		}
		$aParams = array( 'view' => $insFrom.'_'.$insTo );
		return $this->get( 'invoices', $aParams );
	}
	
	public function getInvoice( $insId ) {
		return $this->get( 'invoices/'.$insId );
	}
	
	public function getInvoiceTypes() {
		return $this->get( 'invoices/types' );
	}
	
	public function getInvoiceItems( $insInvoiceId ) {
		return $this->get( 'invoices/'.$insInvoiceId.'/invoice_items' );
	}
	
	public function getInvoiceItem( $insInvoiceId, $insItemId ) {
		return $this->get( 'invoices/'.$insInvoiceId.'/invoice_items/'.$insItemId );
	}
	
	public function getTimeslips( $insFrom = null, $insTo = null ) {
		if ( is_null( $insFrom ) ) {
			$nNow = gmmktime();
			$nFourWeeksAgo = $nNow - (60*60*24*28);

			$insFrom = gmdate( 'Y-m-d', $nFourWeeksAgo );
			$insTo = gmdate( 'Y-m-d', $nNow );
		}
		$aParams = array( 'view' => $insFrom.'_'.$insTo );
		return $this->get( 'timeslips', $aParams );
	}
	
	public function getTimeslip( $insId ) {
		return $this->get( 'timeslips/'.$insId );
	}
	
	public function getUsers() {
		return $this->get( 'company/users' );
	}
	
	public function getUser( $insId ) {
		return $this->get( 'company/users/'.$insId );
	}
	
	public function getBankAccounts() {
		return $this->get( 'bank_accounts' );
	}
	
	public function getBankAccount( $insId ) {
		return $this->get( 'bank_accounts/'.$insId );
	}
	
	public function deleteContact( $insId ) {
		return $this->delete( 'contacts/'.$insId );
	}
	
	public function deleteBill( $insId ) {
		return $this->delete( 'bills/'.$insId );
	}
	
	public function deleteProject( $insId ) {
		return $this->delete( 'projects/'.$insId );
	}
	
	public function deleteInvoice( $insId ) {
		return $this->delete( 'invoices/'.$insId );
	}
	
	public function deleteInvoiceItem( $insInvoiceId, $insId ) {
		return $this->delete( 'invoices/'.$insInvoiceId.'/invoice_items/'.$insId );
	}
	
	public function deleteTimeslip( $insId ) {
		return $this->delete( 'timeslips/'.$insId );
	}
	
	public function deleteUser( $insId ) {
		return $this->delete( 'company/users/'.$insId );
	}
	
	public function deleteExpense( $insUserId, $insId ) {
		return $this->delete( 'users/'.$insUserId.'/expenses/'.$insId );
	}
	
	public function deleteAttachment( $insId ) {
		return $this->delete( 'attachments/'.$insId );
	}
	
	public function createContact( $inaData ) {
		// required = array( 'first-name', 'last-name' );
		return $this->create( 'contacts', $this->createPostXml( 'contact', $inaData ) );
	}
	
	public function createBill( $inaData ) {
		// nominal-code, contact-id, reference, dated-on, due-date, total-value
		return $this->create( 'bills', $this->createPostXml( 'bill', $inaData ) );
	}
	
	public function createProject( $inaData ) {
		// contact-id, name, status, hours-per-day, billing-period, budget-units
		return $this->create( 'projects', $this->createPostXml( 'project', $inaData ) );
	}
	
	public function createProjectTask( $insProjectId, $inaData ) {
		// name
		return $this->create( 'projects/'.$insProjectId.'/tasks', $this->createPostXml( 'task', $inaData ) );
	}
	
	public function createInvoice( $inaData ) {
		// contact-id, dated-on, payment-terms-in-days and reference
		return $this->create( 'invoices', $this->createPostXml( 'invoice', $inaData ) );
	}

	public function createInvoiceItem( $insInvoiceId, $inaData ) {
		// item-type, description, quantity, price
		return $this->create( 'invoices/'.$insInvoiceId.'/invoice_items', $this->createPostXml( 'invoice-item', $inaData ) );
	}
	
	public function createTimeslip( $inaData, $inaData ) {
		// project-id, user-id, hours,dated-on, task-id or new-task
		return $this->create( 'timeslips', $this->createPostXml( 'timeslip', $inaData ) );
	}
	
	public function updateContact( $insId, $inaData ) {
		return $this->update( 'contacts/'.$insId, $this->createPostXml( 'contact', $inaData ) );
	}
	
	public function updateBill( $insId, $inaData ) {
		return $this->update( 'bills/'.$insId, $this->createPostXml( 'bill', $inaData ) );
	}
	
	public function updateProject( $insId, $inaData ) {
		return $this->update( 'projects/'.$insId, $this->createPostXml( 'project', $inaData ) );
	}
	
	public function updateProjectTask( $insProjectId, $insTaskId, $inaData ) {
		return $this->update( 'projects/'.$insProjectId.'/tasks/'.$insTaskId, $this->createPostXml( 'task', $inaData ) );
	}
	
	public function updateInvoice( $insId ) {
		return $this->update( 'invoices/'.$insId, $this->createPostXml( 'invoice', $inaData ) );
	}
	
	public function updateInvoiceItem( $insInvoiceId, $insItemId ) {
		return $this->update( 'invoices/'.$insInvoiceId.'/invoice_items/'.$insItemId, $this->createPostXml( 'invoice-item', $inaData ) );
	}
	
	public function updateTimeslip( $insId, $inaData ) {
		return $this->update( 'timeslips/'.$insId, $this->createPostXml( 'timeslip', $inaData ) );
	}
	
	protected function getRequestUrl( $insRequest ) {
		return $this->m_sBaseUrl.'/'.$insRequest;
	}
	
	public function isResultOk( $inoResult ) {
		return ($inoResult->status_code === 200 || $inoResult->status_code === 201);
	}
	
	public function isResultConflict( $inoResult ) {
		return ($inoResult->status_code === 409);
	}

	protected function getRequestHeaders() {
		return array(		
			'Content-type: application/xml',
			'Accept: application/xml',
			'Cache-Control: no-cache',
			'Pragma: no-cache',
			'Expect:'
		);
	}
	
	protected function getRequestCredentials() {
		return $this->m_sUsername.':'.$this->m_sPassword;
	}
	
	protected function createPostXml( $insRootNode, $inaData ) {
		$oGen = new Xml_Generator();
		$oGen->setRootNode( $insRootNode );
		$sXml = $oGen->generateFromArray( $inaData );
		return $sXml;
	}
}

class FreeAgent_RestfulHttpClient extends RestfulHTTPClient {
	
	private $m_sCredentials;
	
	public function __construct( $insCredentials ) {
		$this->m_sCredentials = $insCredentials;
	}
	
	public function get( $insUrl, $inaHeaders, $inaData = array() ) {
		return $this->request( self::METHOD_GET, $insUrl, $inaHeaders, $inaData, $this->m_sCredentials );
	}
	
	public function post( $insUrl, $inaHeaders, $inaData = array() ) {
		return $this->request( self::METHOD_POST, $insUrl, $inaHeaders, $inaData, $this->m_sCredentials );
	}
	
	public function put( $insUrl, $inaHeaders, $inaData = array() ) {
		return $this->request( self::METHOD_PUT, $insUrl, $inaHeaders, $inaData, $this->m_sCredentials );
	}
	
	public function delete( $insUrl, $inaHeaders, $inaData = array() ) {
		return $this->request( self::METHOD_DELETE, $insUrl, $inaHeaders, $inaData, $this->m_sCredentials );
	}
	
	protected function request( $insMethod, $insUrl, $inaHeaders, $inaData = array(), $insCredentials = '' ) {
		$oCurl = curl_init();
		
		switch ( $insMethod ) {
			case self::METHOD_GET:
				if ( count( $inaData ) > 0 ) {
					$aUrlData = array();
					foreach ( $inaData as $sKey => $sValue ) {
						$aUrlData[] = $sKey.'='.$sValue;
					}
					
					$insUrl .= '?'.implode( '&', $aUrlData );
				}
				break;
				
			case self::METHOD_POST:
				curl_setopt( $oCurl, CURLOPT_POST, 1 );
				curl_setopt( $oCurl, CURLOPT_POSTFIELDS, $inaData );
				break;
				
			case self::METHOD_PUT:
				curl_setopt( $oCurl, CURLOPT_CUSTOMREQUEST, 'PUT' );
				curl_setopt( $oCurl, CURLOPT_POSTFIELDS, $inaData );
				break;
				
			case self::METHOD_DELETE:
				curl_setopt( $oCurl, CURLOPT_CUSTOMREQUEST, 'DELETE' );
				break;
				
			default:
				throw new Exception( 'Invalid REST request method.' );
		}
		
		curl_setopt( $oCurl, CURLOPT_URL, $insUrl );
		curl_setopt( $oCurl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $oCurl, CURLOPT_TIMEOUT, 60 );
		curl_setopt( $oCurl, CURLOPT_HTTPHEADER, $inaHeaders );
		
		curl_setopt( $oCurl, CURLOPT_FOLLOWLOCATION, false );
		curl_setopt( $oCurl, CURLOPT_FRESH_CONNECT, true );
		
		curl_setopt( $oCurl, CURLOPT_USERAGENT, 'PHP FreeAgentCentral API' );
		curl_setopt( $oCurl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
		curl_setopt( $oCurl, CURLOPT_USERPWD, $insCredentials );
		
		curl_setopt( $oCurl, CURLOPT_HEADER, TRUE );
		
		// A little hack to get by the "SSL certificate problem, verify that the CA cert is OK" error.
		curl_setopt( $oCurl, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt( $oCurl, CURLOPT_SSL_VERIFYPEER, 0 );
		
		$sContent	= curl_exec( $oCurl );
		$nErrorCode	= curl_errno( $oCurl );
		$sError		= curl_error( $oCurl );
		$aInfo		= curl_getinfo( $oCurl );
		
		curl_close( $oCurl );

		if ( $nErrorCode ) {
			$sHeaders = '';
			$sBody = '';
		}
		else {
			list( $sHeaders, $sBody ) = explode( "\r\n\r\n", $sContent, 2 );
			$sBody = trim( $sBody );
		}
		
		$oXml = @simplexml_load_string( trim( $sBody ) );
		$aXmlAsArray = array();
		
		if ( $oXml !== false ) {
			$aXmlAsArray = ApiHelper::objectToArray( $oXml );
			foreach ( $aXmlAsArray as $sKey => $mValue ) {
				if ( isset( $mValue['id'] ) ) {
					$aXmlAsArray[$sKey] = array( $mValue );
				}
			}
		}
		
		$aReturn = array(
			'headers'		=> ApiHelper::formatHeaders( $sHeaders ),
			'body'			=> $sBody,
		
			'array'			=> $aXmlAsArray,
		
			'error'			=> $sError,
			'errno'			=> $nErrorCode,
			'info'			=> $aInfo,
		
			'status_code'	=> $aInfo['http_code'],
			'success'		=> (
				($aInfo['http_code'] === 200) ||
				($aInfo['http_code'] === 201 && ( in_array( $insMethod, array( self::METHOD_POST, self::METHOD_PUT ) ) ) )
			)
		);
		
		$oResponse = new BasicResponseObject( $aReturn );
		
		return $oResponse;
	}
}