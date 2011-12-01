<?php
/**
 * EpicDb_Search_Controller_Abstract
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Search_Controller_Abstract extends MW_Controller_Action
{
	public function init() {
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('tag', 'json')
			->addActionContext('index', 'json')
			->addActionContext('index', 'html')
			->initContext();
	}

	public function indexAction() 
	{
		$request = $this->getRequest();
		$format = $request->getParam('format');

		$this->view->layout()->searchQuery = $q = trim($request->getParam('q'));
		$query = array();
		$sort = array(
			'score' => -1,
		);
		// Attempting to find the "type" searches requested
		preg_match_all('/\btype:(\S+)/i', $q, $types);
		$q = str_replace($types[0], "", $q);
		if(isset($types[1])) {
			foreach($types[1] as $type) {
				$query['type'] = $type;
			}
		}
		// Explode the keywords into the Query
		foreach(EpicDb_Search::keywordExplode($q) as $keyword) {
			$query['$and'][] = array(
				'keywords' => new MongoRegex('/'.$keyword.'/i')
			);
		}
		$records = EpicDb_Mongo::db('search')->fetchAll($query, $sort);
		// var_dump($query, $records->export()); exit;
		$paginator = Zend_Paginator::factory($records);
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page', 1));
		$this->view->results = $paginator;			
	}

	public function questionAction()
	{
		$request = $this->getRequest();
		$format = $request->getParam('format');
		$search = EpicDb_Search::getInstance();
		$this->view->layout()->searchQuery = $q = $request->getParam('q');

		$queryData = $search->parseQueryString($q);
		$this->view->searchTerms = $queryData['terms'];
		$query = $queryData['query'];
		$query["_type"] = "question";
		$sort = array( 'touched' => -1, '_created' => -1 );

		$questions = EpicDb_Mongo::db('post')->fetchAll($query,$sort);

		$paginator = Zend_Paginator::factory($questions);
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page', 1));

		$this->view->questions = $paginator;

		if ($questions->count() == 1 && !$format) {
			$questions->next();
			return $this->_redirect($this->view->url(array('post'=>$questions->current()),'post',true));
		}
	}

	public function tagAction()
	{
		$request = $this->getRequest();
		$format = $request->getParam('format');
		$type = $request->getParam('type', false);
		$exactMatch = false;

		$q = $request->getParam('q');
		$lower = strtolower($q);

		$query = array("name" => new MongoRegex("/".$q."/i"));
		if ( $type ) {
			$types = explode( ",", $type );
			$query['_type'] = array( '$in' => $types );
		}

		$return = array();

		$results = EpicDb_Mongo::db('record')->fetchAll($query, array());
		foreach ($results as $result) {
			$ref = $result->createReference();
			$return[] = array(
				'id' => $result->id,
				'type' => $result->_type,
				'label' => $this->view->partial("search/_result.ajax.phtml", array("document" => $result)),
				'card' => $this->view->card($result, array("class" => "medium-icon create-new") )."",
				'name' => $result->name,
			);
			if ($lower == strtolower($result->name)) {
				$exactMatch = true;
			}
		}

		$length = count($return);
		if ($length != 20) {
			$results = EpicDb_Mongo::db('profile')->fetchAll($query, array());
			foreach ($results as $result) {
				$ref = $result->createReference();
				$return[] = array(
					'id' => $result->id,
					'type' => $result->_type,
					'label' => $this->view->partial("search/_result.ajax.phtml", array("document" => $result)),
					'name' => $result->name,
					'card' => $this->view->card($result, array("class" => "medium-icon create-new") )."",
				);
			}
		}
		if (!$exactMatch && !$type) {
			$blank = EpicDb_Mongo::newDoc('tag');
			$blank->name = $q;
			$blank->_type = 'create tag';
			$return[] = array(
				'new' => true,
				'type' => 'tag',
				'name' => $q,
				'label' => $this->view->partial("search/_result.ajax.phtml", array("document" => $blank)),
				'card' => $this->view->card($blank, array("class" => "medium-icon create-new", "content" => array("not found..." => "Create New Tag" ) ) )."",
				
			);
		}
		
		// there is a bug here with non-json -- results doesn't contain them all... fix it later
		$this->view->results = $format == 'json' ? $return : $results;
		
		echo json_encode(array('results' => $this->view->results)); exit;
	}
	
} // END class EpicDb_Search_Controller_Abstract