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
			->initContext();
	}

	public function indexAction() 
	{
		$request = $this->getRequest();
		$format = $request->getParam('format');
		$search = EpicDb_Search::getInstance();
		$this->view->layout()->searchQuery = $q = $request->getParam('q');

		$queryData = $search->parseQueryString($q);
		$this->view->searchTerms = $queryData['terms'];
		$query = $queryData['query'];

		$sort = array( 'touched' => -1, '_created' => -1 );

		// $posts = EpicDb_Mongo::db('post')->fetchAll($query, $sort);
		// 
		// $paginator = Zend_Paginator::factory($posts);
		// $paginator->setCurrentPageNumber($this->getRequest()->getParam('page', 1));
		// 
		// $this->view->posts = $paginator;
		// 
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
		$exactMatch = false;

		$q = $request->getParam('q');
		$lower = strtolower($q);

		$query = array("name" => new MongoRegex("/".$q."/i"));

		$return = array();

		$results = EpicDb_Mongo::db('record')->fetchAll($query, array());
		foreach ($results as $result) {
			$ref = $result->createReference();
			$return[] = array(
				'$ref' => $ref['$ref'],
				'$id' => $ref['$id'].'',
				'card' => $this->view->card($result, array("class" => "medium-icon", "content" => array("is a" => $result->_type)))."",
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
					'$ref' => $ref['$ref'],
					'$id' => $ref['$id'].'',
					'card' => $this->view->card($result, array("class" => "medium-icon", "content" => array("is a" => $result->_type)))."",
					'name' => $result->name,
				);
			}
		}
		if (!$exactMatch) {
			$blank = EpicDb_Mongo::newDoc('tag');
			$blank->name = $q;

			$return[] = array(
				'$new' => true,
				'name' => $q,
				'card' => $this->view->card($blank, array("class" => "medium-icon create-new", "content" => array("not found..." => "Create New Tag" ) ) )."",
			);
		}
		
		// there is a bug here with non-json -- results doesn't contain them all... fix it later
		$this->view->results = $format == 'json' ? $return : $results;
		
		echo json_encode(array('results' => $this->view->results)); exit;
	}
	
} // END class EpicDb_Search_Controller_Abstract