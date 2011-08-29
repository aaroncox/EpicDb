<?php
/**
 * EpicDb_Form_Profile_Group_Guild
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Form_Profile_Group_Guild extends EpicDb_Form_Profile_Group
{
	protected $_profile = null;
	protected $_isNew = false;
	
	/**
	 * getProfile - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function getProfile()
	{
		if($this->_profile) return $this->_profile;
		$this->_profile = new R2Db_Mongo_Profile_Group_Guild();
		$this->_isNew = true;
		return $this->_profile;
	}
	
	public function getRecord()
	{
	  return $this->_profile;
	}
	/**
	 * setProfile - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function setProfile($profile)
	{
		if(!$profile instanceOf EpicDb_Mongo_Profile_Group_Guild) {
			throw new Exception("This isn't a guild profile.");
		}
		$this->_profile = $profile;
		return $this;
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
				'filters' => array('StringTrim'),
				'label' => 'Name',
			));
		$this->addElement("select", "faction", array(
				'label' => 'Faction',
				'multiOptions' => R2Db_Mongo::db('faction')->getSelectOptions(),
			));
		$this->addElement("select", "playstyle", array(
				'label' => 'Guild Type',
				'multiOptions' => array(
					'Not Specified' => 'Not Specified',
					'Generic Guild' => 'Generic Guild',
					'Role-Playing Guild' => 'Role-Playing Guild',
					'Raiding/Progression Guild' => 'Raiding/Progression Guild',
					'Social Guild' => 'Social Guild',
					'PvP Guild' => 'PvP Guild',
					'Casual Guild' => 'Casual Guild',
				),
			));
		$this->addElement("select", "ages", array(
				'label' => 'Ages Preferred',
				'multiOptions' => array(
					'Not Specified' => 'Not Specified',
					'All Ages' => 'All Ages',
					'18+' => '18+',
				),
			));
		$this->addElement("select", "regions", array(
				'label' => 'Region Info',
				'multiOptions' => array(
					'Not Specified' => 'Not Specified',
					'North America' => 'North America',
					'Europe' => 'Europe',
					'Asia' => 'Asia',
					'South America' => 'South America',
					'Africa' => 'Africa',
					'Oceanic' => 'Oceanic',
					'Worldwide' => 'Worldwide',
					'Other' => 'Other',
				),
			));
		$this->addElement("text", "language", array(
				'label' => 'Language(s)',
			));
		
		$this->addElement("text", "url", array(
				'filters' => array('StringTrim'),
				'label' => 'Guild Website',
			));
		$this->addElement("text", "feed", array(
				'filters' => array(new MW_Filter_HttpAddress(), 'StringTrim'),
				'label' => 'RSS/Atom Feed',
			));			
		$this->addElement("textarea", "description", array(
				'filters' => array('StringTrim', 'StripTags'),
				'label' => 'About this Group',
			));
		
		$this->setDefaults($profile->export());
		if($profile->faction) {
			$this->faction->setValue($profile->faction->id);
		}
		if($this->_isNew) {
			$this->setButtons(array("save" => "Create Guild"));						
		} else {
			$this->setButtons(array("save" => "Save Guild"));			
		}
	}
	public function process($data) {
		$profile = $this->getProfile();
		if($this->isValid($data)) {
			$profile->name = $this->name->getValue();
			$profile->_groupType = $this->_groupType->getValue();
			$profile->url = $this->url->getValue();
			$profile->feed = $this->feed->getValue();
			$profile->ages = $this->ages->getValue();
			$profile->regions = $this->regions->getValue();
			$profile->language = $this->language->getValue();
			$profile->playstyle = $this->playstyle->getValue();
			$profile->faction = EpicDb_Mongo::db('faction')->fetchOne(array("id" => (int)$this->faction->getValue()));
			$profile->description = $this->description->getValue();
			if($profile->isNewDocument()) {
				$user = MW_Auth::getInstance()->getUser();
				$profile->_created = time();
				$profile->_owner = $user;
				$user->addGroup($profile->getAdminRole());
				$user->save();
				$membership = MW_Auth::getInstance()->getUserProfile();
				$profile->admins->addDocument($membership);				
			}
			$profile->save();
			return true;
		}
	}	
	
} // END class EpicDb_Form_Profile_Group_Guild