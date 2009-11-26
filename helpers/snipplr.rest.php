<?php
/**
 * A REST Helper that prepares and sends data to API URLs.
 * Snipplr only uses POST requests which require XML structured data to be sent to them.
 *
 */

require_once('snipplr.xml.php');
require_once('snipplr.response.php');

class SnipplrRest
{
	var $api_key;
	var $url = 'http://snipplr.com/xml-rpc.php';
	var $SnipplrXML;
	var $SnipplrResponse;

	function SnipplrRest($api_key) {
		$this->api_key = $api_key;
		$this->SnipplrXML = new SnipplrXML();
		$this->SnipplrResponse = new SnipplrResponse();
	}

	// Snipplr API only uses post calls. This manages the steps involved.
	function post($method, $params = array()) {
		try {
			$xml = $this->_constructXML($method, $params);
			$response = $this->_httpPost($this->url, $xml);
			return $this->_processResponse($method, $response);
		} catch (Exception $e) {
			return null;
		}
	}

	// Contructs an XML object in the format required by Snipplr
	function _constructXML($method, $params = array()) {
		// list of methods that require an api key
		$api_key_required_methods = array('snippet.list');

		// set the methodName for xml query
		$xml_data['methodName'] = $method;

		// if method requires api key then add it
		if(in_array($method, $api_key_required_methods)) {
			$xml_data['params']['param'][] = array(
				'key' => "api_key",
				'value' => $this->api_key
			);
		}

		// add all other specified params
		foreach($params as $key => $value) {
			$xml_data['params']['param'][] = array(
				'key' => $key,
				'value' => $value
			);
		}

		return $this->SnipplrXML->toXML($xml_data, 'methodCall');
	}

	// Makes a generic http post request
	function _httpPost($url, $data) {
		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $url);
	 	curl_setopt($c, CURLOPT_POST, 1);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, true);
	 	curl_setopt($c, CURLOPT_POSTFIELDS, $data);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($c);
		curl_close($c);
		return $output;
	}

	// Processes the xml
	function _processResponse($method, $response) {
		$raw = $this->SnipplrXML->toArray($response);

		switch($method) {
			case 'snippet.get':
				return $this->SnipplrResponse->processSnippetGetResponse($raw);
			case 'snippet.list':
				return $this->SnipplrResponse->processSnippetListResponse($raw);
			case 'languages.list':
				return $this->SnipplrResponse->processLanguagesListResponse($raw);
			default:
				return null;
		}
	}
}
?>