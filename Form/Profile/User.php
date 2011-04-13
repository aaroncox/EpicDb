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
		$this->addElement("text", "display_email", array(
				'filters' => array('StripTags'),
				'validators' => array(new Zend_Validate_EmailAddress()),
				'required' => true,
				'label' => 'Email Address',
				'description' => 'Used to generate your Gravatar image',
			));
		$this->addElement("textarea", "bio", array(
				'filters' => array('StringTrim', 'StripTags'),
				'label' => 'About Me',
			));
		$this->setDefaults($profile->export());
		$this->setButtons(array("save" => "Save Profile"));
	}
	public function process($data) {
		$profile = $this->getProfile();
		if($this->isValid($data)) {
			$profile->display_email = $this->display_email->getValue();
			$profile->bio = $this->bio->getValue();		
			$profile->save();
			$eventData = array();
			$eventData['target'] = $this->getProfile();
			$eventData['form'] = $this;
			$eventData['complete'] = strlen($profile->bio);
			EpicDb_Achievement::trigger('edit', $eventData);
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
	
} // END class EpicDb_Form_Profile
