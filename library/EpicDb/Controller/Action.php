<?php
/**
 * R2Db_Controller
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Controller_Action extends MW_Controller_Action
{
  public function preDispatch() {

  }
	public function init() {
		$profile = $this->getUserProfile();
		if($profile) {
			$profile->updateCards($this->getRequest()->getParams());
		}
		return parent::init();
	}
	
	protected $_profile;
  public function getProfile()
  {
    if ($this->_profile) return $this->_profile;
		if($this->_profile = $this->getRequest()->getParam("profile")) {
			return $this->view->profile = $this->_profile;
		}	
		$id = $this->getRequest()->getParam("id");
		$result = EpicDb_Mongo_Profile_User::getProfileById($id);
		if(!$result) {
			throw new MW_Controller_404Exception("Profile not found...");
		}
		$this->view->stats = $stats = $result->getSocialStats();
		return $this->_profile = $this->view->profile = $result;
  }

	protected $_userProfile;
  public function getUserProfile()
  {
    if ($this->_userProfile) return $this->_userProfile;
		if(!MW_Auth::getInstance()->getUser()) {
			if($this->getRequest()->isXmlHttpRequest()) {
				echo "You are not logged in!";
			} 
			return null;
		}
		return $this->_userProfile = EpicDb_Auth::getInstance()->getUserProfile();
  }

} // END class R2Db_Controller