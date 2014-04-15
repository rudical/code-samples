<?php
/**
*
* @author Rudie Shahinian
* @category   Company
* @package    Company_Service
* @subpackage SalesForce
* @version    1.2.0
*/

/**
 *
 * @see Zend_Soap_Client
 */
/**
 *
 * @see Company_Service_SalesForce_Header_HeaderAbstract
 */

/**
 * This class implementes basic functionality required by any SalesForce client instance.
 *
 * @category Company
 * @package Company_Service
 * @subpackage SalesForce
 */
abstract class Company_Service_SalesForce_Client_ClientAbstract extends Zend_Soap_Client {
	/**
	 * Class constructor.
	 *
	 * @param string $wsdl        	
	 * @param array|Zend_Config $options        	
	 * @throws Company_Service_SalesForce_Exception
	 */
	public function __construct($wsdl = null, $options = null) {
		// if $wsdl is empty then set default value.
		$wsdl = ! empty ( $wsdl ) ? $wsdl : $this->_getDefaultWsdl ();
		
		parent::__construct ( $wsdl, $options );
		
		$this->_checkRequirements ();
		
		$this->setSoapVersion ( SOAP_1_1 );
		
		$_SERVER ['HTTP_USER_AGENT'] = 'Salesforce/PHPToolkit/1.0';
	}
	
	/**
	 * Add SOAP input header.
	 *
	 * @param Company_Service_SalesForce_Header_HeaderAbstract $soapHeader        	
	 * @param boolean $permanent        	
	 * @return Company_Service_SalesForce_Client_ClientAbstract
	 * @throws Company_Service_SalesForce_Exception
	 */
	public function addSalesForceHeader(Company_Service_SalesForce_Header_HeaderAbstract $soapHeader, $permanent = false) {
		// Check if it is allowed to add header for current client.
		if (sizeof ( $soapHeader->getSupportedWsdls () ) && ! in_array ( $this->getWsdlName (), $soapHeader->getSupportedWsdls () )) {
			throw new Company_Service_SalesForce_Exception ( 'Invalid soap header.' );
		}
		
		$header = new SoapHeader ( $this->getTargetNamespace (), $soapHeader->getName (), $soapHeader->getData () );
		
		parent::addSoapInputHeader ( $header, $permanent );
		
		return $this;
	}
	
	/**
	 *
	 * @return string
	 */
	abstract public function getTargetNamespace();
	
	/**
	 * Perform a SOAP call
	 *
	 * @param string $name        	
	 * @param array $arguments        	
	 * @return mixed
	 */
	public function __call($name, $arguments) {
		$this->_filterAllSoapHeaders ( $name );
		return parent::__call ( $name, $arguments );
	}
	
	/**
	 * Returns wsdl name.
	 *
	 * @return string
	 */
	abstract public function getWsdlName();
	
	/**
	 * Initialize SOAP Client object
	 *
	 * @throws Zend_Soap_Client_Exception
	 */
	protected function _initSoapClientObject() {
		$phpversion = substr ( phpversion (), 0, strpos ( phpversion (), '-' ) );
		
		if ($phpversion > '5.1.2') {
			parent::setCompressionOptions ( array (
					'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP 
			) );
		}
		
		parent::_initSoapClientObject ();
	}
	
	/**
	 * Test if php version has all extensions required by SalesForce.
	 *
	 * @return void
	 * @throws Company_Service_SalesForce_Exception
	 */
	private function _checkRequirements() {
		if (! extension_loaded ( 'openssl' )) {
			/**
			 *
			 * @see Company_Service_SalesForce_Exception
			 */
			throw new Company_Service_SalesForce_Exception ( 'OpenSSL extension is not enabled' );
		}
	}
	
	/**
	 *
	 * @param string $name
	 *        	wsdl method name
	 * @return array
	 */
	private function _filterAllSoapHeaders($name) {
		$this->_soapInputHeaders = $this->_filterSoapHeaders ( $this->_soapInputHeaders, $name );
		$this->_permanentSoapInputHeaders = $this->_filterSoapHeaders ( $this->_permanentSoapInputHeaders, $name );
	}
	
	/**
	 * Perform filtering of soap input headers before performing soap request
	 *
	 * @param array $soapHeaders        	
	 * @param string $name
	 *        	wsdl method name
	 * @return array
	 */
	private function _filterSoapHeaders(array $soapHeaders, $name) {
		foreach ( $soapHeaders as &$soapInputHeader ) {
			$className = 'Company_Service_SalesForce_Header_' . $soapInputHeader->name;
			
			try {
				if (! class_exists ( $className )) {
					
					Zend_Loader::loadClass ( $className );
				}
			} catch ( Exception $e ) {
				/**
				 * Company_Service_SalesForce_Exception
				 */
				throw new Company_Service_SalesForce_Exception ( "Unable to load '{$className}': " . $e->getMessage (), $e->getCode () );
			}
			
			$headerObj = new $className ();
			
			$supportedCalls = $headerObj->getSupportedCalls ();
			if (! empty ( $supportedCalls ) && ! in_array ( $name, $supportedCalls )) {
				// remove this header
				$soapInputHeader = NULL;
			}
		}
		
		unset ( $soapInputHeader );
		return array_filter ( $soapHeaders );
	}
	
	/**
	 * Process login response data and start client session.
	 *
	 * @param string $sessionId        	
	 * @param string $serverUrl        	
	 * @return void
	 */
	protected function _processLoginResponse($sessionId, $serverUrl) {
		$this->setLocation ( $serverUrl );
		
		$this->addSalesForceHeader ( new Company_Service_SalesForce_Header_SessionHeader ( $sessionId ), true );
	}
	
	/**
	 * Perform result pre-processing
	 *
	 * @param array $arguments        	
	 */
	protected function _preProcessResult($result) {
		if (isset ( $result->result )) {
			return $result->result;
		} else {
			return $result;
		}
	}
	
	/**
	 * Returns path to default wsld file.
	 *
	 * @return string
	 */
	protected function _getDefaultWsdl() {
		return realpath ( dirname ( __FILE__ ) . '/../' . $this->getWsdlName () . '.wsdl.xml' );
	}
}

