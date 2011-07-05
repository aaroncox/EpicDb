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
		
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext->addActionContext('feed', 'html');
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
		
		$this->view->headLink(array('rel' => 'canonical', 'href'=>$this->view->url(array('record'=>$record), 'record', true)));	
	
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
		if(!$record) {
			throw new Exception("Unable to load record...");
		}
		return $record;
	}
	
	// Sets the section variables on the view for navigation purposes.
	public function getSection($force = null) {
		$this->view->section = array(
			'action' => $this->getRequest()->getParam("action"),
			'section' => $this->getRequest()->getParam("section"),
			'subsection' => $this->getRequest()->getParam("subsection"),
		);
	}
	
	
	// Displays a Record
	public function viewAction() {
	}
} // END class EpicDb_Record_Controller_Abstract extends MW_Controller_Action