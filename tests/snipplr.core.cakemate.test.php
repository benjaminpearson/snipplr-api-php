<?php
/**
 * Tests for snipplr.core.php
 * This test case is specific for CakePHP and requires CakeMate (Textmate plugin) in order to be run.
 * It allows you to easily debug the output of method calls and also validate that you config settings
 * are valid and working correctly.
 *
 * CakeMate URL: http://mark-story.com/posts/view/running-cakephp-unit-tests-with-textmate
 */

require_once('..'.DIRECTORY_SEPARATOR.'snipplr.core.php');

class SnipplrCoreTest extends UnitTestCase {
	var $SnipplrCore;


	function setup() {
		$this->SnipplrCore = new SnipplrCore();
	}

	function testGet() {
		$snippet = $this->SnipplrCore->get("18153");
		$this->assertTrue(isset($snippet['id']));
		//debug($snippet);
	}

	function testGetNotFound() {
		$snippet = $this->SnipplrCore->get("00000");
		$this->assertTrue(empty($snippet));
		//debug($snippet);
	}

	function testGetAll() {
		$snippets = $this->SnipplrCore->getAll();
		$this->assertTrue(isset($snippets[0]['id']));
		$this->assertTrue(isset($snippets[1]['id']));
		//debug($snippets);
	}

	function testGetAllOptionTags() {
		// Multiple tags don't seem to work in the Snipplr API. Only the last tag will be used.
		$snippets = $this->SnipplrCore->getAll(array('tags' => 'css'));
		//debug($snippets);
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
		$this->assertEqual($titles, $sort_titles);
		//debug($snippets);
	}

	function testGetAllOptionLimit() {
		$snippets = $this->SnipplrCore->getAll(array('limit' => '4'));
		$this->assertEqual(sizeof($snippets), 4);
		//debug($snippets);
	}

	function testGetAllLanguages() {
		$languages = $this->SnipplrCore->getAllLanguages();
		$this->assertEqual($languages[0]['slug'], 'actionscript');
		$this->assertEqual($languages[0]['name'], 'ActionScript');
		//debug($languages);
	}
}
?>
