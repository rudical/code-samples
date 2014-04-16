<?php
/**
 *
 * @author Rudie Shahinian
 * @category   PHPUnit
 * @package    Company_Net_IPv4Test
 * @version    1.0.2
 */
class IPv4Test extends ControllerTestCase {
	private $_ipv4;
	public function setUp() {
		$this->_ipv4 = new Company_Net_IPv4 ();
		parent::setUp ();
	}
	
	/**
	 * Test validator functions: validateIP($ip), check_ip($ip), validateNetmask($mask)
	 */
	public function testValidate() {
		$ip = 'test';
		$this->assertFalse ( $this->_ipv4->validateIP ( $ip ) );
		$this->assertFalse ( $this->_ipv4->check_ip ( $ip ) );
		$ip = '192.168.0.1';
		$this->assertTrue ( $this->_ipv4->validateIP ( $ip ) );
		$this->assertTrue ( $this->_ipv4->check_ip ( $ip ) );
		$ip = '999.168.0.1';
		$this->assertFalse ( $this->_ipv4->validateIP ( $ip ) );
		$this->assertFalse ( $this->_ipv4->check_ip ( $ip ) );
		$mask = 'test';
		$this->assertFalse ( $this->_ipv4->validateNetmask ( $mask ) );
		$mask = '255.255.255.0';
		$this->assertTrue ( $this->_ipv4->validateNetmask ( $mask ) );
		$mask = '999.255.255.0';
		$this->assertFalse ( $this->_ipv4->validateNetmask ( $mask ) );
	}
	
	/**
	 * Test subnet parsing function: parseAddress($address)
	 */
	public function testParseAddress() {
		$address = "192.168.0.1/25";
		$this->assertEquals ( $this->_ipv4, $this->_ipv4->parseAddress ( $address ) );
		$address = "192.168.0.1";
		$this->assertEquals ( $this->_ipv4, $this->_ipv4->parseAddress ( $address ) );
		$address = "192.168.0.1/FFFFFFFF";
		$this->assertEquals ( $this->_ipv4, $this->_ipv4->parseAddress ( $address ) );
		$address = "192.168.0.1/255.255.255.255";
		$this->assertEquals ( $this->_ipv4, $this->_ipv4->parseAddress ( $address ) );
		$address = "192.168.0.1/xxx.xxx.xxx.xxx";
		try {
			$this->assertEquals ( $this->_ipv4, $this->_ipv4->parseAddress ( $address ) );
		} catch ( Company_Exception $e ) {
			$this->assertEquals ( "invalid netmask value", $e->getMessage () );
		}
		$address = "192.168.0.1/xxx";
		try {
			$this->assertEquals ( $this->_ipv4, $this->_ipv4->parseAddress ( $address ) );
		} catch ( Company_Exception $e ) {
			$this->assertEquals ( "invalid netmask value", $e->getMessage () );
		}
		$address = "test";
		try {
			$this->assertEquals ( $this->_ipv4, $this->_ipv4->parseAddress ( $address ) );
		} catch ( Company_Exception $e ) {
			$this->assertEquals ( "invalid IP address", $e->getMessage () );
		}
		$address = "test/test";
		try {
			$this->assertEquals ( $this->_ipv4, $this->_ipv4->parseAddress ( $address ) );
		} catch ( Company_Exception $e ) {
			$this->assertEquals ( "invalid IP address", $e->getMessage () );
		}
	}
	
	/**
	 * Test function for calculating network information based on an IP address and netmask: calculate()
	 */
	public function testCalculate() {
		try {
			$this->_ipv4->calculate ();
		} catch ( Company_Exception $e ) {
			$this->assertEquals ( "netmask or bitmask are required for calculation", $e->getMessage () );
		}
		
		$this->_ipv4->ip = "192.168.0.1";
		$this->_ipv4->netmask = "255.255.255.255";
		$this->assertTrue ( $this->_ipv4->calculate () );
		
		$this->_ipv4->ip = "test";
		$this->_ipv4->netmask = "255.255.255.255";
		try {
			$this->_ipv4->calculate ();
		} catch ( Company_Exception $e ) {
			$this->assertEquals ( 'invalid IP address', $e->getMessage () );
		}
		
		$this->_ipv4->ip = '';
		$this->_ipv4->netmask = "255.255.255.255";
		try {
			$this->_ipv4->calculate ();
		} catch ( Company_Exception $e ) {
			$this->assertEquals ( 'invalid IP address', $e->getMessage () );
		}
		$this->_ipv4->ip = null;
		$this->_ipv4->netmask = "255.255.255.255";
		try {
			$this->_ipv4->calculate ();
		} catch ( Company_Exception $e ) {
			$this->assertEquals ( 'invalid IP address', $e->getMessage () );
		}
	}
	
	/**
	 * Test function for converting CIDR to netmask: getNetmask($CIDR)
	 */
	public function testGetNetmask() {
		$this->assertFalse ( $this->_ipv4->getNetmask ( null ) );
		
		$this->assertEquals ( "255.255.255.0", $this->_ipv4->getNetmask ( 24 ) );
		$this->assertEquals ( "255.255.255.128", $this->_ipv4->getNetmask ( 25 ) );
		$this->assertEquals ( "255.255.255.192", $this->_ipv4->getNetmask ( 26 ) );
		$this->assertEquals ( "255.255.255.224", $this->_ipv4->getNetmask ( 27 ) );
		$this->assertEquals ( "255.255.255.240", $this->_ipv4->getNetmask ( 28 ) );
		$this->assertEquals ( "255.255.255.248", $this->_ipv4->getNetmask ( 29 ) );
		$this->assertEquals ( "255.255.255.252", $this->_ipv4->getNetmask ( 30 ) );
		$this->assertEquals ( "255.255.255.254", $this->_ipv4->getNetmask ( 31 ) );
		$this->assertEquals ( "255.255.255.255", $this->_ipv4->getNetmask ( 32 ) );
	}
	
	/**
	 * Test function for converting netmask to CIDR: getNetLength($netmask)
	 */
	public function testGetNetLength() {
		$this->assertFalse ( $this->_ipv4->getNetLength ( null ) );
		$netmask = "255.255.255.0";
		$this->assertEquals ( 24, $this->_ipv4->getNetLength ( $netmask ) );
		$netmask = "24";
		$this->assertEquals ( 24, $this->_ipv4->getNetLength ( $netmask ) );
		$netmask = "";
		$this->assertFalse ( $this->_ipv4->getNetLength ( $netmask ) );
		$netmask = "asdfasdf";
		$this->assertFalse ( $this->_ipv4->getNetLength ( $netmask ) );
		$netmask = 2544488;
		$this->assertFalse ( $this->_ipv4->getNetLength ( $netmask ) );
	}
	
	/**
	 * Test function obtaining subnet given ip and netmask: getSubnet ( $ip, $netmask )
	 */
	public function testGetSubnet() {
		$this->assertFalse ( $this->_ipv4->getSubnet ( null, null ) );
		$ip = "192.168.0.1";
		$netmask = "255.255.255.255";
		$this->assertEquals ( "192.168.0.1", $this->_ipv4->getSubnet ( $ip, $netmask ) );
		$this->_ipv4 = new Company_Net_IPv4 ();
		$ip = "192.168.0.43";
		$netmask = "255.255.255.0";
		$this->assertEquals ( "192.168.0.0", $this->_ipv4->getSubnet ( $ip, $netmask ) );
		$this->_ipv4 = new Company_Net_IPv4 ();
		$ip = "192.168.0.43";
		$netmask = "255.255.255.128";
		$this->assertEquals ( "192.168.0.0", $this->_ipv4->getSubnet ( $ip, $netmask ) );
		$this->_ipv4 = new Company_Net_IPv4 ();
		$ip = "192.168.0.43";
		$netmask = "255.255.255.192";
		$this->assertEquals ( "192.168.0.0", $this->_ipv4->getSubnet ( $ip, $netmask ) );
		$this->_ipv4 = new Company_Net_IPv4 ();
		$ip = "192.168.0.43";
		$netmask = "255.255.255.224";
		$this->assertEquals ( "192.168.0.32", $this->_ipv4->getSubnet ( $ip, $netmask ) );
		$this->_ipv4 = new Company_Net_IPv4 ();
		$ip = "192.168.0.43";
		$netmask = "255.255.255.240";
		$this->assertEquals ( "192.168.0.32", $this->_ipv4->getSubnet ( $ip, $netmask ) );
		$this->_ipv4 = new Company_Net_IPv4 ();
		$ip = "192.168.0.43";
		$netmask = "255.255.255.248";
		$this->assertEquals ( "192.168.0.40", $this->_ipv4->getSubnet ( $ip, $netmask ) );
		$this->_ipv4 = new Company_Net_IPv4 ();
		$ip = "192.168.0.43";
		$netmask = "255.255.255.252";
		$this->assertEquals ( "192.168.0.40", $this->_ipv4->getSubnet ( $ip, $netmask ) );
		$this->_ipv4 = new Company_Net_IPv4 ();
		$ip = "192.168.0.43";
		$netmask = "255.255.255.254";
		$this->assertEquals ( "192.168.0.42", $this->_ipv4->getSubnet ( $ip, $netmask ) );
		$this->_ipv4 = new Company_Net_IPv4 ();
		$ip = "192.168.0.43";
		$netmask = "255.255.255.255";
		$this->assertEquals ( "192.168.0.43", $this->_ipv4->getSubnet ( $ip, $netmask ) );
	}

	/**
	 * Test function for converting IP-adress in dot-quad format to hex: atoh ( $ip )
	 */
	public function testAtoh() {
		$addr = "";
		$this->assertFalse ( $this->_ipv4->atoh ( $addr ) );
		$addr = "192.168.0.1";
		$this->assertEquals ( 'c0a80001', $this->_ipv4->atoh ( $addr ) );
	}
	/**
	 * Test function for converting hex to IP-adress in dot-quad format: htoa ( $addr )
	 */
	public function testHtoa() {
		$addr = "";
		$this->assertFalse ( $this->_ipv4->htoa ( $addr ) );
		$addr = 'c0a80001';
		$this->assertEquals ( '192.168.0.1', $this->_ipv4->htoa ( $addr ) );
	}
	
	/**
	 * Test function to verify if IP is in subnet: ipInNetwork ( $ip, $network )
	 */
	public function testIpInNetwork() {
		$ip = "192.168.0.1";
		$network = "192.168.0.0/24";
		$this->assertTrue ( $this->_ipv4->ipInNetwork ( $ip, $network ) );
		$ip = "192.168.1.1";
		$network = new Company_Net_IPv4 ();
		$this->assertFalse ( $this->_ipv4->ipInNetwork ( $ip, $network ) );
	}
	
	/**
	 * Test function to verify if IP is private: isPrivate ( $ip )
	 */
	public function testIsPrivate() {
		$ip = "192.168.2.0";
		$this->assertTrue ( $this->_ipv4->isPrivate ( $ip ) );
		$ip = "99.168.2.0";
		$this->assertFalse ( $this->_ipv4->isPrivate ( $ip ) );
	}
}

