<?php
/**
 * EpicDb_Form_Profile_Group
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Form_Profile_Group extends EpicDb_Form_Profile {
	public function init() {
		parent::init();
		$profile = $this->getProfile();
		$this->addElement("textarea", "description", array(
				'filters' => array('StringTrim', 'StripTags'),
				'label' => 'About this Group',
				'cols' => 75,
				
			));
		$this->addElement("select", "_groupType", array(
				'label' => 'Type of Group',
				'multiOptions' => array(
					'open' => 'Open Group (Anyone can join)',
					'closed' => 'Closed Group (Applications Accepted)',
					'invite-only' => 'Closed Group (Invites Only)',
				),
			));
		if(EpicDb_Auth::getInstance()->hasPrivilege(new EpicDb_Auth_Resource_Moderator())) {
			$this->addElement("checkbox", "_isForum", array(
				'label' => 'Display this group as a forum'
			));
			$this->addElement("text", "_forumOrder", array(
				'label' => 'Forum # for Ordering',
				'description' => 'A made up number for ordering the forums. Every 50 a separator is added into the display.'
			));
		}
		$this->setDefaults($profile->export());
	}
	
	public function save($data) {
		$profile = $this->getProfile();		
		$profile->name = $this->name->getValue();
		$profile->_groupType = $this->_groupType->getValue();
		$profile->_created = time();
		$profile->description = $this->description->getValue();
		if($this->_isForum) {
			$profile->_isForum = true;
			if($this->_forumOrder) {
				$profile->_forumOrder = (int) $this->_forumOrder->getValue();
			}
		}
		if($profile->isNewDocument()) {
			// Do we need this still?
			$user = EpicDb_Auth::getInstance()->getUser();
			$profile->_owner = $user;
			$profile->grant($user);
			$profile->admins->addDocument(EpicDb_Auth::getInstance()->getUserProfile());				
		}
		$profile->save();
		return $profile;
	}
}