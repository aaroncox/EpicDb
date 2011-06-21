<?php
/**
 * EpicDb_Profile_Controller_Abstract
 *
 * Abstract controller for Profiles
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
abstract class EpicDb_Profile_Controller_Abstract extends MW_Controller_Action
{
	// The EpicDb_Mongo_Profile from the request parameter "profile".
	protected $_profile;

	public function init() {
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('feed', 'html')
								->addActionContext('manage', 'html')
								->addActionContext('invite', 'html')
								;
		$contextSwitch = $this->_helper->getHelper('ContextSwitch');
		if (!$contextSwitch->hasContext('rss')) {
			$contextSwitch->addContext('rss', array(
				'callbacks' => array(
						'init' => array($this, 'initRssContext'),
						'post' => array($this, 'postRssContext'),
				)
			));
		}
		$contextSwitch->addActionContext('feed', 'rss');
		try {
			$ajaxContext->initContext();
		} catch (Exception $e) {
			// Unknown Context Exception?
		}
		try {
			$contextSwitch->initContext();
		} catch (Exception $e) {
			// Unknown Context Exception?
		}

		parent::init();
	}

	public function initRssContext()
	{
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$view = $viewRenderer->view;
		if ($view instanceof Zend_View_Interface) {
				$viewRenderer->setNoRender(true);
		}
	}

	public function postRssContext()
	{
		$generator = new EpicDb_Feed_Generator(array('view'=>$this->view));
		$generator->setLink($this->view->url());
		$generator->setTitle($this->getProfile()->name."'s public feed");
		foreach ($this->view->posts as $post)
		{
			$generator->addPost($post);
		}
		$generator->toRss()->send();
	}

	public function getProfile()
	{
		if($this->_profile) return $this->_profile;
		if($this->_profile = $this->getRequest()->getParam("profile")) {
			return $this->view->profile = $this->_profile;
		}
		$id = $this->getRequest()->getParam("id");
		$type = $this->getRequest()->getParam('type', 'user');
		$result = EpicDb_Mongo::db($type)->fetchOne(array( "id"=>(int)$id));

		if(!$result) {
			throw new MW_Controller_404Exception("Profile not found...");
		}
		return $this->_profile = $this->view->profile = $result;
	}


	public function unfollowAction() {
		$this->_helper->auth->unfollow($this->getProfile());
		$this->_redirect($this->getRequest()->getServer('HTTP_REFERER'));
	}


	public function followAction() {
		$this->_helper->auth->follow($this->getProfile());
		$this->_redirect($this->getRequest()->getServer('HTTP_REFERER'));
	}


	public function unblockAction() {
		$this->_helper->auth->unblock($this->getProfile());
		$this->_redirect($this->getRequest()->getServer('HTTP_REFERER'));
	}


	public function blockAction() {
		$this->_helper->auth->block($this->getProfile());
		$this->_redirect($this->getRequest()->getServer('HTTP_REFERER'));
	}

	protected function _getProfileForm($profile) {
		EpicDb_Auth::getInstance()->requirePrivilege($profile, "edit");
		return $profileForm = $this->view->profileForm = $profile->getEditForm();
	}
	
	public function editAction()
	{
		$profile = $this->getProfile();
		$this->_getProfileForm($profile);
		$this->_handleMWForm($this->view->profileForm);
	}
	public function followersAction() {
		$profile = $this->getProfile();
		$paginator = Zend_Paginator::factory($profile->getMyFollowers());
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page', 1))->setItemCountPerPage(30);
		$this->view->followers = $paginator;
	}
	public function followingAction() {
		$profile = $this->getProfile();
	}
	public function crawlAction() {
		$profile = $this->getProfile();
		try {
			EpicDb_Crawler::crawl($profile, true);
		} catch (Exception $e) {
			$this->view->error = "Error: Caught ".get_class($e)." ".$e->getMessage()." \n";
		}
	}
} // END class EpicDb_Profile_Controller_Abstract