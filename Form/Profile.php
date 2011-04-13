<?php
/**
 * R2Db_Form_Profile
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Form_Profile extends MW_Form
{
	protected $_profile = null;

	/**
	 * getProfile - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function getProfile()
	{
		if($this->_profile) return $this->_profile;
		$class = EpicDb_Mongo::dbClass('profile');
		$this->_profile = new $class;
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
		if(!$profile instanceOf EpicDb_Mongo_Profile) {
			throw new Exception("This isn't a valid profile type [".$profile->_type."].");
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
				'filters' => array('StringTrim', 'StripTags'),
				'required' => true,
				'label' => 'Display Name',
			));
		$this->setDefaults($profile->export());
		$this->setButtons(array("save" => "Save Profile"));
	}
	public function process($data) {
		$profile = $this->getProfile();
		if($this->isValid($data)) {
			$profile->name = $this->name->getValue();
			$profile->save();
			return true;
		}
	}
	public function render()
	{
		foreach($this->getElements() as $element) {
			$element->setAttrib('class', 'ui-state-default');
		}
		$this->save->setAttrib('class','login r2-button ui-state-default ui-corner-all');
		$this->getDecorator('HtmlTag')->setOption('class','r2-form transparent-bg rounded padded-10')->setOption('id', 'ad-edit');
		return parent::render();
	}	
	
} // END class R2Db_Form_Profile
