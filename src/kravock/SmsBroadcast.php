<?php

namespace kravock;

class SmsBroadcast
{

	private $_username;
	private $_password;
	private $_sender_name;
	private $_api_endpoint = 'https://api.smsbroadcast.com.au/api-adv.php';

	function __construct($config = array())
	{
		if ( ! isset($config['username']) AND empty($_username)) {
			throw new Exception('Username not Specified');
		}

		if ( ! isset($config['password']) AND empty($_password)) {
			throw new Exception('Password not Specified');
		}

		if ( isset( $config['sender_name'] )) {
			$this->_sender_name = $config['sender_name'];
		}

		$this->_username = $config['username'];
		$this->_password = $config['password'];
	}

	public function initialize($config = array())
	{
		$this->__construct($config);
	}

	public function setSender($sender_name = '')
	{
		$this->_sender_name = $sender_name;
	}

	public function getSender()
	{
		return $this->_sender_name;
	}

	/**
	 * Send an SMS
	 * @param  string  $message    required
	 * @param  array   $recipients required - Array of mobile numbers to send to
	 * @param  boolean $ref        optional - Your reference number for the message to help you track the message status
	 * @param  integer $maxsplit   optional - Determines the maximum length of your SMS message
	 * @param  integer $delay      optional - Number of minutes to delay the message. Use this to schedule messages for later delivery.
	 * @return string              Returns a string in format OK:61400111222:2942263 for each successful message and BAD:0400abc111:Invalid Number for each failed sms
	 */
	public function sendSms($message = '', $recipients = array(), $ref = FALSE, $maxsplit = 1, $delay = 0)
	{
		if ( empty($recipients) ) {
			throw new Exception( 'You haven\'t specified any recipients' );
		}

		if ( empty($message) ) {
			throw new Exception( 'You haven\'t specified a message to send' );
		}

		if ( strlen( $ref ) > 20 ) {
			throw new Exception( 'Ref must be 20 characters or less' );
		}

		if ( $maxsplit > 5 ) {
			throw new Exception( 'Maxsplit must be 5 or less' );
		}

		if( empty( $this->_sender_name ) ) {
			throw new Exception( 'You haven\'t specified a sender name' );
		}

		$url = '&to='. implode(',', $recipients);
		$url .= '&from='.$this->_sender_name;
		$url .= '&message='.$message;

		if ( $ref ) {
			$url .= '&ref='.$ref;
		}

		$url .= '&maxsplit='.$maxsplit;

		if ( $delay > 0 ) {
			$url .= '&delay='.$delay;
		}

		return $this->_fetch($url);

	}

	public function getBalance()
	{
		$url = '&action=balance';
		$result = $this->_fetch($url);

		return (int) explode(':', $result)[1];
	}

	private function _fetch($url = '')
	{

		$built_string = $this->_api_endpoint . '?username='.rawurlencode( $this->_username ).
		'&password='.rawurlencode( $this->_password );

		$built_string .= $url;

		$ch = curl_init($built_string);

		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $built_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec ($ch);
		curl_close ($ch);

		if( explode(':', $output) AND $output[0] === 'ERROR' ) {
			throw new Exception( $output[1] );
		}

		return $output;
	}

}
