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
	
	public function keywordExplode($source) {
		$return = array();
		if(is_array($source)) {
			foreach($source as $string) {
				$return += $this->keywordExplode($string);
			}
			return $return;
		}
		$return = array_unique(preg_split('/[^\w`-]+/', strtolower((string)$source)));
		return array_filter($return, function($value) {
			if($value && strlen($value) > 1) {
				return true;				
			}
		});
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
			$re = new MongoRegex("/\b".preg_quote($term)."/i");
			$nameQuery = array(
				'$and' => array(
					array("name" => $re),
				), 
				'_type' => array(
					'$ne' => 'skill-temp'
				),
			);
			$postQuery = array(
				'_private' => array(
					'$ne' => true
				),
				'_published' => array(
					'$ne' => false,
				),
				'$and' => array(
					array("title" => $re),
					array("body" => $re),
				)
			);
			return array(
				"terms" => array("contains" => array($term)), 
				"query" => array(
					"records" => $nameQuery,
					"profiles" => $nameQuery,
					"posts" => $postQuery,
				)
			);
		}
		if ($type == "tag") {
			if (is_string($term)) {
				$term = $this->findTagByName( $term );
			}
			if ($term instanceOf MW_Mongo_Document) {
				$query = array();
				$query['$or'][] = array(
					"tags.ref" => array(
						'$all' => array($term->createReference())
					)					
				);
				return array(
					"terms" => array("tagged" => array($term)), 
					"query" => array(
						"records" => $query,
						"posts" => $query,
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
			'query' => array('posts' => array(), 'profiles' => array(), 'records' => array()),
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
		
		// var_dump(json_encode($return)); exit;
		return $return;
	}

}