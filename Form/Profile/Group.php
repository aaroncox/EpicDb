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
		$this->setDefaults($profile->export());
	}
}