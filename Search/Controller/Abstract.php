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
		if($format === "html" || $format === "json") {
			$resultLimit = 15;
			Zend_Paginator::setDefaultItemCountPerPage( $resultLimit );
		}
		$search = EpicDb_Search::getInstance();
		$this->view->layout()->searchQuery = $q = trim($request->getParam('q'));

		// if(strlen($q) < 3) return; 

		$queryData = $search->parseQueryString($q);
		$this->view->searchTerms = $queryData['terms'];
		$query = $queryData['query'];

		if(!empty($query['records'])) {
			$records = EpicDb_Mongo::db('record')->fetchAll($query['records']);
		} else { 
			$records = array();
		}
		$paginator = Zend_Paginator::factory($records);
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page', 1));
		$this->view->records = $paginator;			

		if(!empty($query['profiles'])) {
			$profiles = EpicDb_Mongo::db('profile')->fetchAll($query['profiles']);			
		} else {
			$profiles = array();
		}
		$paginator = Zend_Paginator::factory($profiles);
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page', 1));
		$this->view->profiles = $paginator;

		if(!empty($query['posts'])) {
			$sort = array( 'touched' => -1, '_created' => -1 );
			$posts = EpicDb_Mongo::db('post')->fetchAll($query['posts'], $sort);					
		} else {
			$posts = array();
		}
		$paginator = Zend_Paginator::factory($posts);
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page', 1));
		$this->view->posts = $paginator;
		
		if(!empty($posts) || !empty($profiles) || !empty($records)) {
			EpicDb_Mongo::db('searchlog')->log($q);
		}
		
		if($format == "json") {
			$results = array();
			foreach($this->view->records as $record) {
				$results[] = array(
					'id' => $record->id,
					'_type' => $record->_type,
					'_name' => $record->name,
					'label' => $this->view->partial("search/_result.ajax.phtml", array("document" => $record)),
					'name' => $record->name,
					'link' => $this->view->recordLink($record).""
				);
			}
			foreach($this->view->profiles as $record) {
				if(count($results) > $resultLimit) continue;
				$results[] = array(
					'id' => $record->id,
					'_type' => $record->_type,
					'_name' => $record->name,
					'label' => $this->view->partial("search/_result.ajax.phtml", array("document" => $record)),
					'name' => $record->name,
					'link' => $this->view->recordLink($record).""
				);
			}
			foreach($this->view->posts as $record) {
				if(count($results) > $resultLimit) continue;
				$results[] = array(
					'id' => $record->id,
					'_type' => $record->_type,
					'_name' => $record->name,
					'label' => $this->view->partial("search/_result.ajax.phtml", array("document" => $record)),
					'name' => $record->name,
					'link' => $this->view->recordLink($record).""
				);
			}
			$this->view->results = $results;
			echo json_encode(array('results' => $this->view->results)); exit;
		} 
		
		if($param = $this->getRequest()->getParam("view")) {
			switch($param) {
				case "compact":
					$this->_helper->viewRenderer('compact');  
					break;
			}
		}
		// if ($posts->count() == 1 && !$format) {
		// 	$posts->next();
		// 	return $this->_redirect($this->view->url(array('post'=>$posts->current()),'post',true));
		// }
		
		
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