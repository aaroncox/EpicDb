<?php
/**
 * EpicDb_View_Helper_ProfileSummary
 *
 * undocumented class
 *
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_View_Helper_ProfileContext extends MW_View_Helper_HtmlTag
{
	public function profileContext(EpicDb_Mongo_Profile $profile) {
		// $this->card($this->profile, array("class" => "wide"))
		$placeholder = $this->view->context();
		$placeholder->append($this->view->tooltip($profile)."");
		if($profile->reputation) {
			$placeholder->append($this->view->levelBar($profile)."");
		}
		$buttons = '';
		if ($profile->user && MW_Auth::getInstance()->hasPrivilege(new MW_Auth_Resource_Super(), 'sudo')) {
			$buttons .= $this->view->button(array(
				'controller'=>'user',
				'action'=>'sudo',
				'id'=>$profile->user->_id.''
			), 'default', true, array(
				'text' => 'Sudo',
				'icon' => 'key'
			));
		}
		if(EpicDb_Auth::getInstance()->hasPrivilege($profile, 'edit')) {
			$buttons .= $this->view->button(array(
				'action' => 'edit',
				'profile' => $profile
			), 'profile', true, array(
				'text' => 'Edit Profile',
				'icon' => 'key',
			));						
		}	
		$buttons .= $this->view->button(array(
			'action' => 'memberships',
			'profile' => $profile
		), 'profile', true, array(
			'text' => 'Memberships',
			'icon' => 'person',
		));						
		$currentUser = EpicDb_Auth::getInstance()->getUserProfile();	
		if($currentUser && $currentUser->createReference() != $profile->createReference()) {
			$buttons .= $this->view->followButton($profile);
			$buttons .= $this->view->followButton($profile, array("mode" => "block"));
			$buttons .= $this->view->button(array(
				'action' => 'message',
				'profile' => $profile
			), 'profile', true, array(
				'text' => 'Message',
				'icon' => 'pencil',
			));						
		}

		if($buttons) $placeholder->widget($this->view->partial("./_context/available-actions.phtml", array('buttons' => $buttons)));
		
		// if($profile->bio) $placeholder->widget($this->htmlTag("h3", array(), "Biography")."".$this->htmlTag("p", array(), $profile->bio));

		if($profile->following && $profile->following->export() != array()) {
			$placeholder->widget($this->view->partial("./_context/icon-cloud.phtml", array('title' => $profile->name.' is Following', 'icons' => $profile->following)));
		}

		$followers = $profile->getMyFollowers();
		if($totalFollowers = $followers->count()) {
			$placeholder->widget($this->view->partial("./_context/icon-cloud.phtml", array('title' => $profile->name.'\'s Followers', 'icons' => $followers)));
		}		
	}
} // END class EpicDb_View_Helper_ProfileSummary