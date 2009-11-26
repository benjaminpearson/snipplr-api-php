<?php
/**
 * This is the mothership file. Create an instance of this and you'll have access to the API methods defined below.
 * Add new API methods as they become available. Each method contains documentation about the params and return values.
 */

require_once('helpers'.DIRECTORY_SEPARATOR.'snipplr.rest.php');
require_once('snipplr.config.php');

class SnipplrCore
{
	var $SnipplrRest;

	function SnipplrCore($api_key = SNIPPLR_API_KEY) {
    $this->SnipplrRest = new SnipplrRest($api_key);
	}

	/*
	Gets a single snippet with more detail than a "getAll" call.

	Returns
		- id
		- user_id
		- username
		- title
		- language
		- comment
		- created
		- updated
		- source
		- tags (space delimited list of the snippet’s tags)
		- snipplr_url

	Params
		snippet_id  (required) The ID of the snippet to fetch. An error message will be returned if the ID is invalid.
	*/
	function get($snippet_id) {
		$snippet = $this->SnipplrRest->post('snippet.get', compact('snippet_id'));
		return $snippet;
	}

	/*
	Gets all snippets owned by the user or marked as favorites of the user. Note: It doesn't search all
	of the snippets on Snipplr.

	Returns and array of snippets with following fields
		- id
		- title
		- updated['datetime']
		- updated['timezone']
		- private (boolean)
		- favorite (boolean)

	Params:
		options['tags']   (optional) NOTE: Multiple tags don't seem to work in the Snipplr API. Only the last tag will be used.
		                             A space delimited list of tags (keywords) to filter the results by.
		                             Returns snippets which contain at least one of the keywords in the snippet’s
		                             title or that match one of the snippet’s tags.
		options['sort']   (optional) Can be one of these three values: “title”, “date”, “random”.
		options['limit']  (optional) The number of snippets to return. A limit of 0 or 1 appears to return unlimited snippets.

	*/
	function getAll($options = array()) {
		// Multiple tags don't seem to work in the Snipplr API. Only the last tag will be used.
		$tags = isset($options['tags']) ? $options['tags'] : "";
		$sort = isset($options['sort']) ? $options['sort'] : "date";
		$limit = isset($options['limit']) ? $options['limit'] : "";

		$snippets = $this->SnipplrRest->post('snippet.list', compact('tags','sort','limit'));
		return $snippets;
	}

	/*
	Gets all languages that Snipplr uses.

	Returns and array of languages with following fields
		- slug   This id can be used when adding snippets
		- name   The more human meaningful name of the language

	Params: -

	*/
	function getAllLanguages() {
		$snippets = $this->SnipplrRest->post('languages.list');
		return $snippets;
	}
}
?>
