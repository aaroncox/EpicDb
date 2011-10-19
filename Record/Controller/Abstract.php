<?php
/**
 * EpicDb_Record_Controller_Abstract
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Record_Controller_Abstract extends MW_Controller_Action
{
	public function init() {
		$record = $this->getRecord();
		if($record) {
			$this->view->headLink(array('rel' => 'canonical', 'href'=>$this->view->url(array('record'=>$record), 'record', true)));			
			// Tooltip Context! Booyah
			$contextSwitch = $this->_helper->getHelper('contextSwitch');
			if (!$contextSwitch->hasContext('tooltip')) {
				$contextSwitch->addContext('tooltip', array(
					// 'headers' => array('Content-Type' => 'application/jsonp'),
					'callbacks' => array(
							'init' => array($this, 'initTooltipContext'),
					)
				));
			}
			$contextSwitch->addActionContext('view', 'tooltip');
			try {
				$contextSwitch->initContext();
			} catch (Exception $e) {
				// Unknown Context Exception?
			}
		}

		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('feed', 'html');

		// Generate Section information
		$this->getSection();

		parent::init();
	}

	/**
	 * getPost - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function getRecord()
	{
		$this->view->record = $record = $this->_request->getParam('record');
		return $record;
	}

	// Sets the section variables on the view for navigation purposes.
	public function getSection($force = null) {
		$this->view->section = array(
			'action' => $this->getRequest()->getParam("action"),
			'section' => $this->getRequest()->getParam("section"),
			'subsection' => $this->getRequest()->getParam("subsection"),
			'sortBy' => $this->getRequest()->getParam("sortBy"),
		);
	}

	public function editAction() {
		$record = $this->view->record;
		EpicDb_Auth::getInstance()->requirePrivilege(new EpicDb_Auth_Resource_Moderator());
		$this->view->form = $form = $record->getEditForm();
		$this->_handleMWForm($form, $record->_type);
	}

	// Displays a Record
	public function viewAction() {
	}
	
	public function listAction() {
		$query = array();
		$sort = array("name" => 1);
		$this->view->recordType = $recordType = $this->getRequest()->getParam('recordType');
		$this->view->recordTypeName = $this->getRequest()->getParam('recordTypeName');
		$records = EpicDb_Mongo::db($recordType)->fetchAll($query, $sort);
		$paginator = Zend_Paginator::factory($records);
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page', 1));
		$this->view->records = $paginator;
	}

	public function seedAction() {
		if(!$id = $this->getRequest()->getParam('seed',0)) throw new MW_Controller_404Exception("Unknown seed");
		$query = array(
			'id' => (int) $id
		);
		$this->view->seed = $seed = EpicDb_Mongo::db('seed')->fetchOne($query);
		$record = $this->view->record;
		if(!$seed) throw new MW_Controller_404Exception("Unknown seed");
		// var_dump($record->_type, $seed->types); exit;
		if(!in_array($record->_type, $seed->types)) throw new Exception("This record type is unable to use this seed.");
		if(EpicDb_Auth::getInstance()->hasPrivilege(new EpicDb_Auth_Resource_Moderator())) {
			$this->view->form = $form = new EpicDb_Form_Seed_Tag(array('seed' => $seed, 'record' => $record));
			$this->_handleMWForm($form);
		}
	}

	public function historyAction() {
		$record = $this->view->record;
	}
	
	public function unfollowAction() {
		$record = $this->view->record;
		$this->_helper->auth->unfollow($record);
		if ($this->getRequest()->isXmlHttpRequest()) {
			$this->_helper->layout->disableLayout();
			echo $this->view->followButton($record);
			exit;
		}
		$this->_redirect($this->getRequest()->getServer('HTTP_REFERER'));
	}


	public function followAction() {
		$record = $this->view->record;
		$this->_helper->auth->follow($record);
		if ($this->getRequest()->isXmlHttpRequest()) {
			$this->_helper->layout->disableLayout();
			echo $this->view->followButton($record);
			exit;
		}
		$this->_redirect($this->getRequest()->getServer('HTTP_REFERER'));
	}
} // END class EpicDb_Record_Controller_Abstract extends MW_Controller_Action