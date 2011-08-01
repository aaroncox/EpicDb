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
		$this->setDefaults($profile->export());
	}
	
	public function save($data) {
		$profile = $this->getProfile();
		$profile->_groupType = $this->_groupType->getValue();
		$profile->save();
		return $profile;
	}
}