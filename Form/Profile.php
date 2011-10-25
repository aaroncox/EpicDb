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
class EpicDb_Form_Profile extends EpicDb_Form
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
		$class = EpicDb_Mongo::dbClass('profile');
		$this->_profile = new $class;
		$this->_isNew = true;
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
	
	public function __construct($options = null)
	{
		parent::__construct( $options );
		// postinit - post decorators
		$this->setDefaults( $this->getDefaultValues() );
	}
	
	public function getInitialData()
	{
		return $profile = $this->getProfile();
	}
	
	public function getDefaultValues()
	{
		$values = array();
		$data = $this->getInitialData();
		$values['icon'] = array($data->tags->getTag('icon'));
		return $values;
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
		$this->addElement("tags", "icon", array(
			'recordType' => implode(",",EpicDb_Mongo_Schema::getInstance()->getRecordTypes()),
			'label' => 'My Icon',
			'description' => 'You have 2 options now when choosing what your profile icon is: You can use the Gravatar associated to your email address or you can choose a record from our database and use it\'s icon',
			'limit' => 1,
			'class' => 'ui-state-default',
		));
		$this->setDefaults($profile->export());
		$this->setButtons(array("save" => "Save Profile"));
	}
	
	public function save($data) {
		$profile = $this->getProfile();
		$profile->name = $this->name->getValue();
		$profile->_lastEdited = time();
		$profile->_lastEditedBy = EpicDb_Auth::getInstance()->getUserProfile();
		$value = $this->icon->getValue();
		$filter = new EpicDb_Filter_TagJSON();
		$value = $filter->toArray($value);
		$profile->tags->setTags('icon', $value);
		$profile->save();
		return $profile;
	}
	
	public function process($data) {
		if($this->isValid($data)) {
			$this->save($data);
			return true;
		}
	}
	public function render()
	{
		foreach($this->getElements() as $element) {
			$element->setAttrib('class', 'ui-state-default');
		}
		$this->save->setAttrib('class','login r2-button ui-state-default ui-corner-all');
		$this->getDecorator('HtmlTag')->setOption('class','r2-form transparent-bg rounded')->setOption('id', 'ad-edit');
		return parent::render();
	}	
} // END class R2Db_Form_Profile
