<?php
/**
 * Tests for snipplr.core.php
 * This test case is specific for PHPUnit and requires the phpunit PEAR package.
 * It allows you to easily debug the output of method calls and also validate that you config settings
 * are valid and working correctly.
 *
 * Command line: phpunit SnipplrCoreTest snipplr.core.phpunit.test.php
 */

require_once 'PHPUnit'.DIRECTORY_SEPARATOR.'Framework.php';
require_once '..'.DIRECTORY_SEPARATOR.'snipplr.core.php';

class SnipplrCoreTest extends PHPUnit_Framework_TestCase
{
	protected $SnipplrCore = null;

	function setUp() {
		$this->SnipplrCore = new SnipplrCore();
	}

	function testGet() {
		$snippet = $this->SnipplrCore->get("18153");
		$this->assertTrue(isset($snippet['id']));
		//print_r($snippet);
	}

	function testGetNotFound() {
		$snippet = $this->SnipplrCore->get("00000");
		$this->assertTrue(empty($snippet));
		//print_r($snippet);
	}

	function testGetAll() {
		$snippets = $this->SnipplrCore->getAll();
		$this->assertTrue(isset($snippets[0]['id']));
		$this->assertTrue(isset($snippets[1]['id']));
		//print_r($snippets);
	}

	function testGetAllOptionTags() {
		// Multiple tags don't seem to work in the Snipplr API. Only the last tag will be used.
		$snippets = $this->SnipplrCore->getAll(array('tags' => 'css'));
		//print_r($snippets);
	}

	function testGetAllOptionSortTitle() {
		$snippets = $this->SnipplrCore->getAll(array('sort' => 'title'));
		// check its in alphabetical order against php sort method
		$titles = array();
		foreach($snippets as $snippet) {
			$titles[] = strtoupper($snippet['title']);
		}
		$sort_titles = $titles;
		sort($sort_titles);
		$this->assertEquals($titles, $sort_titles);
		//print_r($snippets);
	}

	function testGetAllOptionLimit() {
		$snippets = $this->SnipplrCore->getAll(array('limit' => '4'));
		$this->assertEquals(sizeof($snippets), 4);
		//print_r($snippets);
	}

	function testGetAllLanguages() {
		$languages = $this->SnipplrCore->getAllLanguages();
		$this->assertEquals($languages[0]['slug'], 'actionscript');
		$this->assertEquals($languages[0]['name'], 'ActionScript');
		//print_r($languages);
	}
}
?>