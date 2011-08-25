<?php
/**
 * EpicDb_Form_Profile
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Form_Profile_User extends EpicDb_Form_Profile
{
	/**
	 * init - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function init()
	{
		parent::init();
		$profile = $this->getProfile();
		$this->addElement("text", "email", array(
				'filters' => array('StripTags'),
				'validators' => array(new Zend_Validate_EmailAddress()),
				'label' => 'Email Address',
				'description' => 'Used to generate your Gravatar image',
			));
		$this->addElement("select", "faction", array(
				'label' => 'Faction',
				'multiOptions' => array(null => "Not specified")+R2Db_Mongo::db('faction')->getSelectOptions(),
			));
		$this->addElement("textarea", "bio", array(
				'filters' => array('StringTrim', 'StripTags'),
				'label' => 'About Me',
				'cols' => 75,
				
			));
		$this->setDefaults($profile->export());
		$this->setButtons(array("save" => "Save Profile"));
	}
	
	public function save($data) {
		$profile = $this->getProfile();
		$profile->email = $this->email->getValue();
		$profile->bio = $this->bio->getValue();	
		$profile->faction = EpicDb_Mongo::db('faction')->fetchOne(array("id" => (int)$this->faction->getValue()));	
		return parent::save($data);
	}
} // END class EpicDb_Form_Profile
