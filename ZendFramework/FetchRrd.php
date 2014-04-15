<?php

/**
 *
 * @author Rudie Shahinian
 * @version 3.2.4
 * @category   Company
 * @package    Company\Service\Nagios
 *           
 */
namespace Company\Service\Nagios;

/**
 *
 * @see Zend_Http_Client
 */
use Zend_Http_Client as Client;

/**
 *
 * @see Zend_Http_Client_Adapter_Curl
 */
use Zend_Http_Client_Adapter_Curl as Curl;

/**
 *
 * @see Company\Service\Nagios\Exception
 */
use Company\Service\Nagios\Exception as NagiosException;

/**
 *
 * @category Company
 * @package Company\Service\Nagios
 */
class FetchRrd {
	private $_username;
	private $_password;
	private $_apiurl;
	private $_host;
	private $_service;
	private $_start;
	private $_end;
	private $_resolution;
	private $_error;
	private $_data;
	private $_count;
	private $_increment;
	private $_units;
	private $_names;
	private $_datatypes;
	private $_title;
	private $_graphdata;
	private $_dependant;
	private $_independant;
	
	/**
	 * Custom Nagios RRD API Username
	 *
	 * @return $_username
	 */
	public function getUsername() {
		return $this->_username;
	}
	
	/**
	 * Custom Nagios RRD API Password
	 *
	 * @return $_password
	 */
	public function getPassword() {
		return $this->_password;
	}
	
	/**
	 * Custom Nagios RRD API URL
	 *
	 * @return $_apiurl
	 */
	public function getApiurl() {
		return $this->_apiurl;
	}
	
	/**
	 * Name of host to lookup from Nagios
	 *
	 * @return $_host
	 */
	public function getHost() {
		return $this->_host;
	}
	
	/**
	 * Name of service to lookup from Nagios
	 *
	 * @return $_service
	 */
	public function getService() {
		return $this->_service;
	}
	
	/**
	 * Start timestamp of RRD data set
	 *
	 * @return $_start
	 */
	public function getStart() {
		return $this->_start;
	}
	
	/**
	 * End timestamp of RRD data set
	 *
	 * @return $_end
	 */
	public function getEnd() {
		return $this->_end;
	}
	
	/**
	 * Time in seconds between the interval of data points requested
	 *
	 * @return $_resolution
	 */
	public function getResolution() {
		return $this->_resolution;
	}
	
	/**
	 * Error has occured
	 *
	 * @return $_error
	 */
	public function getError() {
		return $this->_error;
	}
	
	/**
	 * Containing RRD data set
	 *
	 * @return $_data
	 */
	public function getData() {
		return $this->_data;
	}
	
	/**
	 * Number of data points returned from RRD
	 *
	 * @return $_count
	 */
	public function getCount() {
		return $this->_count;
	}
	
	/**
	 * Interval between data points returned from RRD
	 *
	 * @return $_increment
	 */
	public function getIncrement() {
		return $this->_increment;
	}
	
	/**
	 * Name of data points returned from RRD
	 *
	 * @return $_names
	 */
	public function getNames() {
		return $this->_names;
	}
	
	/**
	 * Units of data points returned from RRD
	 *
	 * @return $_units
	 */
	public function getUnits() {
		return $this->_units;
	}
	
	/**
	 * Type of data points returned from RRD
	 *
	 * @return $_datatypes
	 */
	public function getDatatypes() {
		return $this->_datatypes;
	}
	
	/**
	 * Title of graph returned from RRD
	 *
	 * @return $_title
	 */
	public function getTitle() {
		return $this->_title;
	}
	
	/**
	 * Data points arranged for use in JS charting
	 *
	 * @return $_graphdata
	 */
	public function getGraphData() {
		return $this->_graphdata;
	}
	
	/**
	 * Dependant variable
	 *
	 * @return $_dependant
	 */
	public function getDependantData() {
		return $this->_dependant;
	}
	
	/**
	 * Independant veriable
	 *
	 * @return $_independant
	 */
	public function getIndependantData() {
		return $this->_independant;
	}
	
	/**
	 * Set the name of the host to lookup in RRD
	 *
	 * @param string $_host        	
	 */
	public function setHost($_host) {
		$this->_host = $_host;
	}
	
	/**
	 * Set the name of the service to lookup in RRD
	 *
	 * @param string $_service        	
	 */
	public function setService($_service) {
		$this->_service = $_service;
	}
	
	/**
	 * Set start timestamp of RRD data set to retrive
	 *
	 * @param integer $_start        	
	 */
	public function setStart($_start) {
		$this->_start = $_start;
	}
	
	/**
	 * Set end timestamp of RRD data set to retrive
	 *
	 * @param integer $_end        	
	 */
	public function setEnd($_end) {
		$this->_end = $_end;
	}
	
	/**
	 * Set resolution of RRD data set to retrive
	 *
	 * @param integer $_resolution        	
	 */
	public function setResolution($_resolution) {
		$this->_resolution = $_resolution;
	}
	
	/**
	 * Class constructor
	 *
	 * Instantiate the class with paramters to retrive RRD data produced in Nagios. $host and $service are used
	 * to look up a host with a particular service. $start and $end are start and end timestamps used to return RRD
	 * data within this time range. $resolution is the number of seconds between data points. $config is an array
	 * which is used to overwrite the configuration settings stored in the application.ini file.
	 *
	 * @param string $host        	
	 * @param string $service        	
	 * @param integer $start        	
	 * @param integer $end        	
	 * @param integer $resolution        	
	 * @param array $config        	
	 * @return void
	 */
	public function __construct($host, $service, $start = null, $end = null, $resolution = null, $config = null) {
		if (empty ( $config )) {
			$config = \Zend_Registry::get ( 'config' );
		}
		
		if ($end == null) {
			$end = time () - (10 * 60);
		}
		$this->_apiurl = $config->nagios->ssl . '/includes/components/ztp/';
		$this->_username = $config->nagios->username;
		$this->_password = $config->nagios->password;
		$this->host = $host;
		$this->service = $service;
		$this->start = $start;
		$this->end = $end;
		$this->resolution = $resolution;
	}
	
	/**
	 * Fetch RRD data
	 *
	 * Use a custom script on the Nagios server to retrive RRD data through a custom REST API. Data returned
	 * is formated as JSON. SSL must be used with Basic Authentication. Use {@link getData()} to get the data.
	 *
	 * @return void
	 */
	public function fetchData() {
		$uri = $this->_apiurl;
		$client = new Client ( $uri, array (
				'timeout' => 60,
				'keepalive' => true,
				'ssltransport' => 'tls' 
		) );
		$client->setAuth ( $this->_username, $this->_password, \Zend_Http_Client::AUTH_BASIC );
		$client->setParameterPost ( 'action', 'rrd' );
		$client->setParameterPost ( 'host', $this->host );
		$client->setParameterPost ( 'service', $this->service );
		if (isset ( $this->start )) {
			$client->setParameterPost ( 'start', $this->start );
		}
		if (isset ( $this->end )) {
			$client->setParameterPost ( 'end', $this->end );
		}
		if (isset ( $this->resolution )) {
			$client->setParameterPost ( 'resolution', $this->resolution );
		}
		try {
			$request = $client->request ( Client::POST );
		} catch ( Exception $e ) {
			$this->_error [] = $e;
		}
		try {
			$rrd = \Zend_Json::decode ( $request->getBody () );
		} catch ( Exception $e ) {
			$this->_error [] = $e;
		}
		$this->_data = $rrd ['datastrings'];
		$this->_end = $rrd ['end'];
		$this->_start = $rrd ['start'];
		$this->_count = $rrd ['count'];
		$this->_increment = $rrd ['increment'];
		$this->_units = $rrd ['units'];
		$this->_datatypes = $rrd ['datatypes'];
		$this->_title = $rrd ['title'];
		$this->_names = $rrd ['names'];
	}
	
	/**
	 * Fetch RRD data for use in charting
	 *
	 * Uses {@link fetchData()} to obtain the RRD data from Nagios and formats it for use in JS graphing tools
	 * (i.e. dojox/charting). Use {@link getGraphdata()} to get the data.
	 *
	 * @return void
	 */
	public function fetchGraphData() {
		$this->fetchData ();
		$i = 1;
		foreach ( $this->_data as $time => $data ) {
			$graphdata ['data'] ['dependant'] [0] [] = array (
					'text' => date ( 'g-i', $time ),
					'value' => $i 
			);
			$graphdata ['data'] ['dependant'] [0] ['units'] = 'timestamp';
			$graphdata ['data'] ['dependant'] [0] ['label'] = 'timestamp';
			$graphdata ['data'] ['dependant'] [0] ['datatype'] = 'timestamp';
			$i ++;
			foreach ( $data as $index => $datum ) {
				if ($datum == 'null') {
					$graphdata ['data'] ['independant'] [($index - 1)] ['values'] [] = 0;
				} else {
					$graphdata ['data'] ['independant'] [($index - 1)] ['values'] [] = $datum;
				}
			}
		}
		for($i = 0; $i < count ( $this->_units ); $i ++) {
			$graphdata ['data'] ['independant'] [$i] ['units'] = ( string ) $this->_units [$i] [0];
			$graphdata ['data'] ['independant'] [$i] ['label'] = ( string ) $this->_names [$i] [0];
			$graphdata ['data'] ['independant'] [$i] ['datatype'] = ( string ) $this->_datatypes [$i] [0];
		}
		$this->_dependant = $graphdata ['data'] ['dependant'];
		$this->_independant = $graphdata ['data'] ['independant'];
		$graphdata ['start'] = $this->_start;
		$graphdata ['end'] = $this->_end;
		$graphdata ['increment'] = $this->_increment;
		$graphdata ['title'] = $this->_title;
		$graphdata ['count'] = $this->_count;
		try {
			$this->_graphdata = \Zend_Json::encode ( $graphdata );
		} catch ( Exception $e ) {
			$this->_error [] = $e;
		}
	}
}

