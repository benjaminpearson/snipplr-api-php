<?php
/**
 * A Response Helper that manipulates the response XML into formatted arrays for ease of use.
 * The responses received from Snipplr follow a set structure. Each method attempts to format
 * this structure for use in your application. If a response doesn't match the structure
 * defined, an error is assumed. If Snipplr changes the output of their responses in future updates,
 * this file will also need to be updated.
 */

class SnipplrResponse
{
	function processSnippetGetResponse($response) {
		$formatted = array();

		// if response is in correct array structure then process it, else return empty array
		if(isset($response['params']['param']['value']['struct']['member'])) {
			$item = $response['params']['param']['value']['struct']['member'];
			$formatted = array(
				'id' => $item[0]['value']['string'],
				'user_id' => $item[1]['value']['string'],
				'username' => $item[2]['value']['string'],
				'title' => $item[3]['value']['string'],
				'language' => $item[4]['value']['string'],
				'comment' => $item[5]['value']['string'],
				'created' => $item[6]['value']['string'],
				'updated' => $item[7]['value']['string'],
				'source' => $item[8]['value']['string'],
				'tags' => explode(" ", trim($item[9]['value']['string'])),
				'snipplr_url' => $item[10]['value']['string']
			);
		}

		return $formatted;
	}

	function processSnippetListResponse($response) {
		$formatted = array();

		// if response is in correct array structure then process it, else return empty array
		if(isset($response['params']['param']['value']['array']['data']['value'])) {
			foreach($response['params']['param']['value']['array']['data']['value'] as $item) {
				$item = $item['struct']['member'];

				$datetime = new DateTime($item[2]['value']['struct']['member'][0]['value']['dateTime.iso8601']);

				$formatted[] = array(
					'id' => $item[0]['value']['string'],
					'title' => $item[1]['value']['string'],
					'updated' => array(
						'datetime' => $datetime->format('Y-m-d h:m:s'),
						'timezone' => $item[2]['value']['struct']['member'][1]['value']['string'],
					),
					'private' => $item[3]['value']['boolean'],
					'favourite' => $item[4]['value']['boolean'],
				);
			}
		}

		return $formatted;
	}

	function processLanguagesListResponse($response) {
		$formatted = array();
		// if response is in correct array structure then process it, else return empty array
		if(isset($response['params']['param']['value']['struct']['member'])) {
			foreach($response['params']['param']['value']['struct']['member'] as $item) {
				$formatted[] = array(
					'slug' => $item['name'],
					'name' => $item['value']['string']
				);
			}
		}

		return $formatted;
	}
}
?>