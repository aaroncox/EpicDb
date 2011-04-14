<?php
/**
 *
 *
 * @author Corey Frang
 * @package EpicDb_Search
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 */

/**
 *  EpicDb_Search
 *
 * undocumented
 *
 * @author Corey Frang
 * @package EpicDb_Search
 * @copyright Copyright (c) 2011 Momentum Workshop, Inc
 * @version $Id:$
 */
class EpicDb_Search {

	/**
	 * Class Instance - Singleton Pattern
	 *
	 * @var self
	 **/
	static protected $_instance = NULL;

	/**
	 * private constructor - singleton pattern
	 *
	 * @return void
	 * @author Corey Frang
	 **/
	private function __construct()
	{
	}

	/**
	 * Returns (or creates) the Instance - Singleton Pattern
	 *
	 * @return self
	 * @author Corey Frang
	 **/
	static public function getInstance()
	{
		if (self::$_instance === NULL) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}


	/**
	 * Turns a query term into a thing
	 *
	 * @return void
	 * @author Corey Frang
	 **/
	public function parseQueryTerm($term, $type = 'string')
	{
		if ($type == 'string' || $type == "quoted") {
			if ($type == "string" && $tag = $this->findTagByName( $term )) {
				$type = 'tag';
				$term = $tag;
			} else {
				$re = new MongoRegex("/\b".preg_quote($term)."\b/i");
				return array(
					"terms" => array("contains" => array($term)), 
					"query" => array('$or' => array(
						array("body" => $re),
						array("title" => $re),
					))
				);
			}
		}
		if ($type == "tag") {
			if (is_string($term)) {
				$term = $this->findTagByName( $term );
			}
			if ($term instanceOf MW_Mongo_Document) {
				return array(
					"terms" => array("tagged" => array($term)), 
					"query" => array(
						"tags.ref" => array(
							'$all' => array($term->createReference())
						)
					)
				);
			}
		}
		return false;
	}

	public function findTagByName( $name )
	{
		$query = array(
			"name" => new MongoRegex("/^".preg_quote($name)."$/i")
		);
		if ($record = EpicDb_Mongo::db('record')->fetchOne($query)) {
			return $record;
		}
		if ($record = EpicDb_Mongo::db('profile')->fetchOne($query)) {
			return $record;
		}
	}

	public function parseQueryString($query)
	{
		$return = array(
			'terms' => array('tagged'=>array(), 'contains'=>array()),
			'query' => array(),
		);
		$curParse = "";
		$state = "string";
		$search = $this;
		$pushCur = function() use (&$curParse, &$return, $search, &$state) {
			if (strlen($curParse)) {
				$term = $search->parseQueryTerm($curParse, $state);
				if ($term) {
					$return = array_merge_recursive($return,$term);
				}
				$curParse = '';
			}
		};
		for ($x=0; $x<strlen($query); $x++) {
			$char = $query[$x];
			if ($state == "string") {
				// auto-split at whitespace
				if (preg_match("/^\s$/",$char)) {
					$pushCur();
					continue;
				} if (!strlen($curParse) && $char == "\"") {
					$state = "quoted";
					continue;
				} if ($char == "[") {
					$pushCur();
					$state = "tag";
					continue;
				} else {
					$curParse .= $query[$x];
					continue;
				}
			}
			if ($state == "tag") {
				if ($char != "]") {
					$curParse .= $char;
				} else {
					$pushCur();
					$state = "string";
				}
			}
			if ($state == "quoted") {
				if ($char != "\"") {
					$curParse .= $char;
				} else {
					$pushCur();
					$state = "string";
				}
			}
		}
		$pushCur($state);
		
		// var_dump($query, json_encode($return), $results->export()); exit;
		return $return;
	}

}