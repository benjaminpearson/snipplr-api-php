<?php
/**
 * An XML Helper that allows conversion between arrays and XML objects.
 * This version isn't specific for Snipplr and can be transported to other applications by changing
 * the classname and appropriate static class name calls in the code. "SnipplrXML" is used as a pseudo namespace.
 *
 * Originating Source: http://snipplr.com/view/3491/convert-php-array-to-xml-or-simple-xml-object-if-you-wish/
 */
class SnipplrXML
{
	/**
	 * The main function for converting to an XML document.
	 * Pass in a multi dimensional array and this recursively loops through and builds up an XML document.
	 *
	 * @param array $data
	 * @param string $rootNodeName - what you want the root node to be - defaults to data.
	 * @param SimpleXMLElement $xml - should only be used recursively
	 * @return string XML
	 */
	public static function toXML($data, $rootNodeName = 'ResultSet', &$xml = null) {
		// turn off compatibility mode as simple xml throws a wobbly if you don't.
		if(ini_get('zend.ze1_compatibility_mode') == 1) {
			ini_set('zend.ze1_compatibility_mode', 0);
		}

		if(is_null($xml)) {
			$xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName/>");
		}

		// loop through the data passed in.
		foreach($data as $key => $value) {
			// no numeric keys in our xml please!
			$numeric = 0;
			if(is_numeric($key)) {
				$numeric = 1;
				$key = $rootNodeName;
			}

			// delete any char not allowed in XML element names
			$key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);

			// if there is another array found recrusively call this function
			if(is_array($value)) {
				$node = SnipplrXML::isAssoc($value) || $numeric ? $xml->addChild($key) : $xml;

				// recrusive call.
				if($numeric) {
					$key = 'anon';
				}
				SnipplrXML::toXml($value, $key, $node);
			} else {
				// add single node.
				$value = htmlentities($value);
				$xml->addChild($key, $value);
			}
		}

		// pass back as XML
		return $xml->asXML();
	}


	/**
	 * Convert an XML document to a multi dimensional array
	 * Pass in an XML document (or SimpleXMLElement object) and this recrusively loops through and builds a representative array
	 *
	 * @param string $xml - XML document - can optionally be a SimpleXMLElement object
	 * @return array ARRAY
	 */
	public static function toArray( $xml ) {
		if(is_string($xml)) {
			$xml = new SimpleXMLElement( $xml );
		}

		$children = $xml->children();

		if(!$children) {
			return (string) $xml;
		}

		$arr = array();
		$adjusted = array(); // used to determine if a node has been converted to an array of nodes
		foreach($children as $key => $node) {
			$node = SnipplrXML::toArray($node);

			// support for 'anon' non-associative arrays
			if($key == 'anon') {
				$key = count($arr);
			}

			// if the node is already set, put it into an array
			if(isset($arr[$key])) {
				if(isset($adjusted[$key]) && $adjusted[$key] == false) {
					$first_value = $arr[$key];
					$arr[$key] = array();
					$arr[$key][] = $first_value;
					$adjusted[$key] = true;
				}

				if(!is_array($arr[$key]) || (isset($arr[$key][0]) && $arr[$key][0] == null)) {
					$arr[$key] = array( $arr[$key] );
				}
				$arr[$key][] = $node;
			} else {
				$arr[$key] = $node;
				$adjusted[$key] = false;
			}
		}
		return $arr;
	}

	// determine if a variable is an associative array
	public static function isAssoc( $array ) {
		return (is_array($array) && 0 !== count(array_diff_key($array, array_keys(array_keys($array)))));
	}
}
?>