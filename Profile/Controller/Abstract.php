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
		$this->getSection(); // Loads action, section and subsection to the view for navigation purposes.
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
		if (!$contextSwitch->hasContext('tooltip')) {
			$contextSwitch->addContext('tooltip', array(
				// 'headers' => array('Content-Type' => 'application/jsonp'),
				'callbacks' => array(
						'init' => array($this, 'initTooltipContext'),
				)
			));
		}
		$contextSwitch->addActionContext('view', 'tooltip');
		$contextSwitch->addActionContext('news', 'tooltip');
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
	
	public function initTooltipContext() {
		var_dump($this); exit;
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

	public function getSection($force = null) {
		$this->view->section = array(
			'action' => $this->getRequest()->getParam("action"),
			'section' => $this->getRequest()->getParam("section"),
			'subsection' => $this->getRequest()->getParam("subsection"),
		);
	}
	
	
	public function newsAction()
	{
		$profile = $this->getProfile();
		$query = array();
		$query['$or'][] = array('tags' =>
			array('$elemMatch' => array(
				'reason' => 'author',
				'ref' => $profile->createReference(),
				)
			)
		);
		$query['$or'][] = array('tags' =>
			array('$elemMatch' => array(
				'reason' => 'source',
				'ref' => $profile->createReference(),
				)
			)
		);
		$sort = array("_created" => -1);
		$paginator = Zend_Paginator::factory(EpicDb_Mongo::db('post')->fetchAll($query, $sort));
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page', 1))->setItemCountPerPage(5);
		$this->view->posts = $posts = $paginator;
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
		$record = $this->getProfile();
		$this->_helper->auth->unfollow($record);
		if ($this->getRequest()->isXmlHttpRequest()) {
			$this->_helper->layout->disableLayout();
			echo $this->view->followButton($record);
			exit;
		}
		$this->_redirect($this->getRequest()->getServer('HTTP_REFERER'));
	}


	public function followAction() {
		$record = $this->getProfile();
		$this->_helper->auth->follow($record);
		if ($this->getRequest()->isXmlHttpRequest()) {
			$this->_helper->layout->disableLayout();
			echo $this->view->followButton($record);
			exit;
		}
		$this->_redirect($this->getRequest()->getServer('HTTP_REFERER'));
	}


	public function unblockAction() {
		$record = $this->getProfile();
		$this->_helper->auth->unblock($this->getProfile());
		if ($this->getRequest()->isXmlHttpRequest()) {
			$this->_helper->layout->disableLayout();
			echo $this->view->followButton($record, array("mode" => "block"));
			exit;
		}
		$this->_redirect($this->getRequest()->getServer('HTTP_REFERER'));
	}


	public function blockAction() {
		$record = $this->getProfile();
		$this->_helper->auth->block($this->getProfile());
		if ($this->getRequest()->isXmlHttpRequest()) {
			$this->_helper->layout->disableLayout();
			echo $this->view->followButton($record, array("mode" => "block"));
			exit;
		}
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
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page', 1))->setItemCountPerPage(15);
		$this->view->followers = $paginator;
	}
	public function followingAction() {
		$profile = $this->getProfile();
	}
	public function crawlAction() {
		$profile = $this->getProfile();
		try {
			EpicDb_Crawler::getInstance()->crawl($profile, true);
		} catch (Exception $e) {
			$this->view->error = "Error: Caught ".get_class($e)." ".$e->getMessage()." \n";
		}
	}
	public function changeOwnerAction() {
		$profile = $this->getProfile(); 
		$user = EpicDb_Auth::getInstance()->getUserProfile();
   	R2Db_Auth::getInstance()->requirePrivilege(new MW_Auth_Resource_Super());			
		$this->view->newOwner = $newOwner = EpicDb_Mongo::db('profile')->fetchOne(array("_id" => new MongoId($this->getRequest()->getParam('target'))));
		if($confirm = $this->getRequest()->getParam('confirm')) {
			$newUser = $newOwner->user; 
			if(!$user instanceOf MW_Auth_Mongo_Role) {
				$newUser = MW_Auth_Mongo_Role::fetchOne(array("_id" => new MongoId($newOwner->user->_id)));
			}
			$profile->_owner = $newUser;
			$profile->save();
			$this->_redirect(urldecode($this->getRequest()->getParam('referer')));
		}
	}
} // END class EpicDb_Profile_Controller_Abstract