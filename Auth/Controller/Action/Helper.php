<?php
/**
 * EpicDb_Auth_Controller_Plugin_UserProfile
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Auth_Controller_Action_Helper extends MW_Auth_Controller_Action_Helper
{
	protected $_userProfile = null;
	
	public function getUserProfile()
  {
    if ($this->_userProfile) return $this->_userProfile;
		$auth = EpicDb_Auth::getInstance();
		if(!$auth->getUser()) {
			if($this->getRequest()->isXmlHttpRequest()) {
				echo "You are not logged in!";
			} 
			return null;
		}
		return $this->_userProfile = $auth->getUserProfile();
  }

	public function follow($document) {
		$userProfile = $this->getUserProfile();
		if($document && $userProfile) {
			$result = $userProfile->follow($document)->save();
		}
	}
	
	public function unfollow($document) {
		$userProfile = $this->getUserProfile();
		if($document && $userProfile) {
			$result = $userProfile->unfollow($document)->save();
		}
	}
	
	public function block($document) {
		$userProfile = $this->getUserProfile();
		if($document && $userProfile) {
			$result = $userProfile->block($document)->save();
		}
	}
	
	public function unblock($document) {
		$userProfile = $this->getUserProfile();
		if($document && $userProfile) {
			$result = $userProfile->unblock($document)->save();
		}
	}
} // END class EpicDb_Auth_Controller_Plugin_UserProfile