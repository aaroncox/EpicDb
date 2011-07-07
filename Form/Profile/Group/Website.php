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
class EpicDb_Form_Profile_Group_Website extends EpicDb_Form_Profile_Group
{
	/**
	 * getProfile - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function getProfile()
	{
		if($this->_profile) return $this->_profile;
		$class = EpicDb_Mongo::dbClass('website');
		$this->_profile = new $class;
		$this->_isNew = true;
		return $this->_profile;
	}
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
		$this->addElement("text", "name", array(
				'filters' => array('StringTrim', 'StripTags'),
				'label' => 'Website Name',
			));
		$this->addElement("text", "url", array(
				'filters' => array('StringTrim'),
				'label' => 'Website URL',
			));
		$this->addElement("text", "feed", array(
				'filters' => array(new MW_Filter_HttpAddress(), 'StringTrim'),
				'label' => 'RSS/Atom Feed',
			));
		$this->addElement("text", "twitter", array(
				'filters' => array('StringTrim'),
				'label' => 'Twitter Username',
			));			
		$this->addElement("text", "logo", array(
				'filters' => array(new MW_Filter_HttpAddress(), 'StringTrim'),
				'label' => 'Website Logo URL',
				'description' => 'The system will resize the image down to a max-width of 100px, so please link to an image that will scale properly!',
			));
		$this->addElement("text", "icon", array(
				'filters' => array(new MW_Filter_HttpAddress(), 'StringTrim'),
				'label' => 'Website Icon URL',
				'description' => 'For profile display purposes, we\'d like a 80x80 icon to represent your website.',
			));
		$this->addElement("textarea", "description", array(
				'filters' => array('StringTrim', 'StripTags'),
				'label' => 'About this Website',
			));
		
		$this->setDefaults($profile->export());
		$this->setButtons(array("save" => "Save Profile"));
	}
	public function process($data) {
		$profile = $this->getProfile();
		if($this->isValid($data)) {
			$profile->name = $this->name->getValue();
			$profile->url = $this->url->getValue();
			$profile->feed = $this->feed->getValue();
			$profile->twitter = $this->twitter->getValue();
			$profile->logo = $this->logo->getValue();
			$profile->icon = $this->icon->getValue();
			$profile->description = $this->description->getValue();
			if($profile->isNewDocument()) {
				// Do we need this still?
				$user = MW_Auth::getInstance()->getUser();
				$profile->_owner = $user;
				$profile->grant($user);
				$profile->admins->addDocument(EpicDb_Auth::getInstance()->getUserProfile());				
			}
			$profile->save();
			return true;
		}
	}
	
} // END class EpicDb_Form_Profile
